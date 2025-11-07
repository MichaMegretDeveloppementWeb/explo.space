<?php

namespace App\Services\Admin\EditRequest\Detail;

use App\Contracts\Services\Admin\EditRequest\Detail\EditRequestTranslationServiceInterface;
use App\Contracts\Translation\TranslationStrategyInterface;
use App\Models\EditRequest;
use Illuminate\Support\Facades\Log;

class EditRequestTranslationService implements EditRequestTranslationServiceInterface
{
    public function __construct(
        private readonly TranslationStrategyInterface $translationStrategy
    ) {}

    /**
     * Traduire un champ spécifique dans suggested_changes
     */
    public function translateField(EditRequest $editRequest, string $fieldName): bool
    {
        // Vérifier que c'est une modification
        if (! $editRequest->isModification()) {
            return false;
        }

        /** @var array<int, array{field: string, field_label?: string, old_value: mixed, new_value: mixed, detected_language?: string, translated_value?: string|null, status: string}> $suggestedChanges */
        $suggestedChanges = $editRequest->suggested_changes ?? [];

        // Vérifier que le tableau n'est pas vide
        if (empty($suggestedChanges)) {
            return false;
        }

        // Trouver le champ à traduire
        $fieldIndex = null;
        foreach ($suggestedChanges as $index => $change) {
            if (isset($change['field']) && $change['field'] === $fieldName) {
                $fieldIndex = $index;
                break;
            }
        }

        if ($fieldIndex === null) {
            return false;
        }

        $field = $suggestedChanges[$fieldIndex];

        // Vérifier qu'il y a quelque chose à traduire
        if (empty($field['new_value']) || ! is_string($field['new_value'])) {
            return false;
        }

        // Vérifier que la langue n'est pas déjà française
        $detectedLang = $field['detected_language'] ?? 'unknown';
        if ($detectedLang === 'fr' || $detectedLang === 'unknown' || $detectedLang === 'none') {
            return false;
        }

        // Traduire
        try {
            $translatedValue = $this->translationStrategy->translate(
                $field['new_value'],
                $detectedLang,
                'fr'
            );

            // Mettre à jour le champ avec la traduction
            $suggestedChanges[$fieldIndex]['translated_value'] = $translatedValue;

            // Sauvegarder
            $editRequest->suggested_changes = $suggestedChanges;
            $editRequest->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to translate EditRequest field', [
                'edit_request_id' => $editRequest->id,
                'field_name' => $fieldName,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Traduire la description de l'EditRequest
     */
    public function translateDescription(EditRequest $editRequest): bool
    {
        // Vérifier qu'il y a une description à traduire
        if (empty($editRequest->description)) {
            return false;
        }

        // Vérifier que la langue n'est pas déjà française
        $detectedLang = $editRequest->detected_language;
        if ($detectedLang === 'fr' || $detectedLang === 'unknown') {
            return false;
        }

        // Vérifier qu'on n'a pas déjà une traduction
        if (! empty($editRequest->description_translation)) {
            return false;
        }

        // Traduire
        try {
            $translatedText = $this->translationStrategy->translate(
                $editRequest->description,
                $detectedLang,
                'fr'
            );

            // Sauvegarder uniquement la traduction (l'original est déjà dans description)
            $editRequest->description_translation = $translatedText;
            $editRequest->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to translate EditRequest description', [
                'edit_request_id' => $editRequest->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
