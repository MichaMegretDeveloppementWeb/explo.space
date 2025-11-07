<?php

namespace App\Services\Web\Place\EditRequest;

use App\Contracts\Repositories\Web\Place\EditRequest\EditRequestCreateRepositoryInterface;
use App\Contracts\Translation\TranslationStrategyInterface;
use App\DTO\Web\Place\PlaceDetailDTO;
use App\Enums\RequestStatus;
use App\Models\EditRequest;

class EditRequestCreateService
{
    public function __construct(
        private readonly TranslationStrategyInterface $translationStrategy,
        private readonly EditRequestCreateRepositoryInterface $repository
    ) {}

    /**
     * Créer une demande de modification/signalement
     *
     * @param  array<string, mixed>  $validatedData
     */
    public function createEditRequest(array $validatedData, PlaceDetailDTO $place): EditRequest
    {
        // 1. Détecter la langue
        $detectedLanguage = $this->detectLanguage($validatedData);

        // 2. Construire suggested_changes si modification
        $suggestedChanges = null;
        if ($validatedData['type'] === 'modification') {
            $suggestedChanges = $this->buildSuggestedChanges(
                $validatedData['selected_fields'],
                $validatedData['new_values'],
                $place
            );
        }

        // 3. Créer l'EditRequest
        return $this->repository->create([
            'place_id' => $place->id,
            'contact_email' => $validatedData['contact_email'],
            'detected_language' => $detectedLanguage,
            'type' => $validatedData['type'],
            'description' => $validatedData['description'],
            'suggested_changes' => $suggestedChanges,
            'status' => RequestStatus::Submitted,
        ]);
    }

    /**
     * Détecter la langue du contenu soumis
     *
     * @param  array<string, mixed>  $data
     */
    private function detectLanguage(array $data): string
    {
        // Construire texte à analyser
        $textsToAnalyze = [$data['description']];

        // Si modification, ajouter les nouvelles valeurs textuelles
        if ($data['type'] === 'modification' && isset($data['new_values'])) {
            if (! empty($data['new_values']['title'])) {
                $textsToAnalyze[] = $data['new_values']['title'];
            }
            if (! empty($data['new_values']['description'])) {
                $textsToAnalyze[] = $data['new_values']['description'];
            }
            if (! empty($data['new_values']['practical_info'])) {
                $textsToAnalyze[] = $data['new_values']['practical_info'];
            }
        }

        // Combiner textes et limiter à 50 caractères
        $combinedText = mb_substr(implode(' ', $textsToAnalyze), 0, 50);

        // Détecter via Strategy (minimum 5 caractères)
        if (strlen($combinedText) >= 5) {
            try {
                return $this->translationStrategy->detectLanguage($combinedText);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Language detection failed for EditRequest', [
                    'error' => $e->getMessage(),
                    'text_sample' => $combinedText,
                ]);

                return 'unknown';
            }
        }

        return 'unknown';
    }

    /**
     * Construire le JSON suggested_changes pour une modification
     *
     * @param  array<int, string>  $selectedFields
     * @param  array<string, mixed>  $newValues
     * @return array<int, array<string, mixed>>
     */
    private function buildSuggestedChanges(array $selectedFields, array $newValues, PlaceDetailDTO $place): array
    {
        $suggestedChanges = [];

        // Mapping des labels de champs
        $fieldLabels = [
            'title' => __('web/pages/place-show.edit_request.field_title'),
            'description' => __('web/pages/place-show.edit_request.field_description'),
            'coordinates' => __('web/pages/place-show.edit_request.field_coordinates'),
            'address' => __('web/pages/place-show.edit_request.field_address'),
            'practical_info' => __('web/pages/place-show.edit_request.field_practical_info'),
        ];

        foreach ($selectedFields as $field) {
            $newValue = $newValues[$field] ?? null;

            $change = [
                'field' => $field,
                'field_label' => $fieldLabels[$field] ?? $field,
                'old_value' => $this->getOldValue($field, $place),
                'new_value' => $newValue,
                'detected_language' => $this->detectFieldLanguage($field, $newValue),
                'translated_value' => null,
                'status' => 'pending',
            ];

            $suggestedChanges[] = $change;
        }

        return $suggestedChanges;
    }

    /**
     * Détecter la langue d'un champ spécifique
     *
     * @param  mixed  $value
     */
    private function detectFieldLanguage(string $field, $value): string
    {
        // Les coordonnées n'ont pas de langue
        if ($field === 'coordinates') {
            return 'none';
        }

        // Si la valeur est vide ou n'est pas une chaîne, retourner 'unknown'
        if (empty($value) || ! is_string($value)) {
            return 'unknown';
        }

        // Minimum 5 caractères pour détecter
        if (mb_strlen($value) < 5) {
            return 'unknown';
        }

        // Détecter via Strategy
        try {
            return $this->translationStrategy->detectLanguage(mb_substr($value, 0, 100));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Language detection failed for EditRequest field', [
                'error' => $e->getMessage(),
                'field' => $field,
                'value_sample' => mb_substr($value, 0, 50),
            ]);

            return 'unknown';
        }
    }

    /**
     * Récupérer l'ancienne valeur d'un champ depuis le DTO
     *
     * @return mixed
     */
    private function getOldValue(string $field, PlaceDetailDTO $place)
    {
        return match ($field) {
            'title' => $place->title,
            'description' => $place->description,
            'practical_info' => $place->practicalInfo ?? '',
            'address' => $place->address ?? '',
            'coordinates' => [
                'lat' => $place->latitude,
                'lng' => $place->longitude,
            ],
            default => null,
        };
    }
}
