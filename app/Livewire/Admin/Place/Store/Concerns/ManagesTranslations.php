<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

use App\Contracts\Translation\TranslationStrategyInterface;
use App\Exceptions\Translation\TranslationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Trait ManagesTranslations
 *
 * Gestion des traductions automatiques pour le formulaire Place.
 *
 * IMPORTANT: Les propriétés publiques Livewire doivent être déclarées dans le composant principal,
 * pas dans le trait. Ce trait utilise les propriétés suivantes qui doivent être définies dans PlaceStoreForm:
 * - public array $pendingTranslations = []
 * - public bool $showTranslationConfirmation = false
 * - public string $translationTargetLocale = ''
 * - public array $fieldsToOverwrite = []
 * - public bool $hasEmptyFieldsToTranslate = false
 */
trait ManagesTranslations
{
    /**
     * Traduire depuis la langue source vers le français
     * Appelée uniquement quand $showSpecialTranslateButton === true
     */
    public function translateFromSourceLanguage(): void
    {
        // Sécurité: Vérifier que nous sommes bien en mode création depuis PlaceRequest
        if (! $this->placeRequestId || ! $this->showSpecialTranslateButton || ! $this->detectedLanguage) {
            $this->addError('translation', 'Action non autorisée.');

            return;
        }

        // Récupérer les textes actuels FR (dans la langue source)
        $sourceTexts = [
            'title' => $this->translations['fr']['title'] ?? '',
            'description' => $this->translations['fr']['description'] ?? '',
            'practical_info' => $this->translations['fr']['practical_info'] ?? '',
        ];

        // Filtrer les textes vides
        $textsToTranslate = array_filter($sourceTexts);

        if (empty($textsToTranslate)) {
            $this->addError('translation', 'Aucun contenu à traduire.');

            return;
        }

        try {
            // Utiliser le service Strategy existant
            $translationService = app(\App\Contracts\Translation\TranslationStrategyInterface::class);

            // Vérifier l'usage DeepL
            $translationService->checkUsage();

            // Traduire les textes
            $translations = $translationService->translateBatch(
                $textsToTranslate,
                $this->detectedLanguage,
                'fr'
            );

            // Remplacer les champs FR avec les traductions
            if (isset($translations['title']) && ! empty($translations['title'])) {
                $this->translations['fr']['title'] = $translations['title'];
                $this->translations['fr']['slug'] = \Illuminate\Support\Str::slug($translations['title']);
            }

            if (isset($translations['description']) && ! empty($translations['description'])) {
                $this->translations['fr']['description'] = $translations['description'];
            }

            if (isset($translations['practical_info']) && ! empty($translations['practical_info'])) {
                $this->translations['fr']['practical_info'] = $translations['practical_info'];
            }

            // Marquer comme traduit
            $this->isTranslatedFromSource = true;
            $this->showSpecialTranslateButton = false;

            session()->flash('translation_success',
                "Contenu traduit avec succès depuis {$this->detectedLanguageName}."
            );

        } catch (\App\Exceptions\Translation\TranslationException $e) {
            \Illuminate\Support\Facades\Log::warning('Translation from source language failed', [
                'place_request_id' => $this->placeRequestId,
                'detected_language' => $this->detectedLanguage,
                'technical_error' => $e->getTechnicalMessage(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('translation', $e->getDisplayMessage());

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Unexpected translation error', [
                'place_request_id' => $this->placeRequestId,
                'detected_language' => $this->detectedLanguage,
                'error' => $e->getMessage(),
            ]);

            $this->addError('translation',
                'Une erreur inattendue est survenue lors de la traduction. Les données originales ont été conservées.'
            );
        }
    }

    /**
     * Initiate automatic translation for target locale
     */
    public function initiateTranslation(string $targetLocale): void
    {
        $this->resetErrorBag('translation');
        $this->translationTargetLocale = $targetLocale;

        // Determine source locale (the other one)
        $sourceLocale = $targetLocale === 'fr' ? 'en' : 'fr';

        // Get source texts (only non-empty fields)
        $sourceTexts = $this->getSourceTexts($sourceLocale);

        if (empty($sourceTexts)) {
            session()->flash('warning', 'Aucun champ à traduire dans la langue source.');

            return;
        }

        try {
            // Check DeepL usage before translating
            $translationService = app(TranslationStrategyInterface::class);
            $translationService->checkUsage();

            // Translate texts
            $translations = $translationService->translateBatch(
                $sourceTexts,
                $sourceLocale,
                $targetLocale
            );

            $this->pendingTranslations = $translations;

            // Check if any target fields are already filled
            $fieldsToOverwrite = $this->getFieldsToOverwrite($targetLocale, array_keys($translations));

            // Check if there are empty fields that will be translated
            $emptyFields = array_diff(array_keys($translations), $fieldsToOverwrite);
            $this->hasEmptyFieldsToTranslate = ! empty($emptyFields);

            if (! empty($fieldsToOverwrite)) {
                // Show confirmation modal
                $this->fieldsToOverwrite = $fieldsToOverwrite;
                $this->showTranslationConfirmation = true;
            } else {
                // Apply translations directly (all fields are empty)
                $this->applyTranslations();
            }

        } catch (TranslationException $e) {
            session()->flash('error', $e->getDisplayMessage());
            Log::warning('Translation failed', [
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'technical_error' => $e->getTechnicalMessage(),
                'error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur inattendue est survenue lors de la traduction. Veuillez réessayer.');
            Log::error('Unexpected translation error', [
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Confirm and apply translations (replace all - after modal confirmation)
     */
    public function confirmTranslation(): void
    {
        $this->showTranslationConfirmation = false;
        $this->applyTranslations(replaceAll: true);
    }

    /**
     * Apply translations only to empty fields (skip filled fields)
     */
    public function confirmTranslationOnlyEmpty(): void
    {
        $this->showTranslationConfirmation = false;
        $this->applyTranslations(replaceAll: false);
    }

    /**
     * Cancel translation (close modal)
     */
    public function cancelTranslation(): void
    {
        $this->showTranslationConfirmation = false;
        $this->pendingTranslations = [];
        $this->fieldsToOverwrite = [];
    }

    /**
     * Apply pending translations to target locale fields
     *
     * @param  bool  $replaceAll  If true, replace all fields (even filled ones). If false, only fill empty fields.
     */
    private function applyTranslations(bool $replaceAll = true): void
    {
        $targetLocale = $this->translationTargetLocale;
        $successCount = 0;
        $skippedCount = 0;
        $failedFields = [];

        foreach ($this->pendingTranslations as $field => $translation) {
            // Check if field should be skipped (only when replaceAll = false)
            if (! $replaceAll) {
                $currentValue = $this->translations[$targetLocale][$field] ?? '';
                if (! empty(trim($currentValue))) {
                    // Skip this field, it's already filled
                    $skippedCount++;

                    continue;
                }
            }

            // Apply translation
            if (! empty($translation)) {
                $this->translations[$targetLocale][$field] = $translation;

                // Auto-generate slug from title
                if ($field === 'title') {
                    $this->translations[$targetLocale]['slug'] = Str::slug($translation);
                }

                $successCount++;
            } else {
                $failedFields[] = $field;
            }
        }

        // Reset pending state
        $this->pendingTranslations = [];
        $this->fieldsToOverwrite = [];

        // Show appropriate message
        if ($successCount > 0 && empty($failedFields)) {
            $message = "{$successCount} champ(s) traduit(s) avec succès.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} champ(s) rempli(s) conservé(s).";
            }
            session()->flash('success', $message);
        } elseif ($successCount > 0 && ! empty($failedFields)) {
            $fieldsList = implode(', ', $failedFields);
            session()->flash('warning', "{$successCount} champ(s) traduit(s). Échec pour : {$fieldsList}");
        } else {
            session()->flash('error', 'Aucun champ n\'a pu être traduit.');
        }
    }

    /**
     * Get non-empty source texts for translation
     *
     * @return array<string, string>
     */
    private function getSourceTexts(string $sourceLocale): array
    {
        $texts = [];

        $fieldsToTranslate = ['title', 'description', 'practical_info'];

        foreach ($fieldsToTranslate as $field) {
            $value = $this->translations[$sourceLocale][$field] ?? '';
            if (! empty(trim($value))) {
                $texts[$field] = $value;
            }
        }

        return $texts;
    }

    /**
     * Get fields that will be overwritten (already have content in target locale)
     *
     * @param  array<string>  $fieldsBeingTranslated
     * @return array<string>
     */
    private function getFieldsToOverwrite(string $targetLocale, array $fieldsBeingTranslated): array
    {
        $toOverwrite = [];

        foreach ($fieldsBeingTranslated as $field) {
            $currentValue = $this->translations[$targetLocale][$field] ?? '';
            if (! empty(trim($currentValue))) {
                $toOverwrite[] = $field;
            }
        }

        return $toOverwrite;
    }

    /**
     * Auto-update slug when title changes
     *
     * Hook into Livewire's updated lifecycle. This method is automatically called
     * by Livewire when any property in $this->translations[{locale}][{field}] changes.
     *
     * Key format explanation:
     * - When wire:model="translations.fr.title" changes, Livewire calls:
     *   updatedTranslations($newValue, "fr.title")
     * - The $key parameter contains the path within the translations array
     *   Format: "{locale}.{field}" (e.g., "fr.title", "en.description")
     * - We parse this key to extract locale and field, then auto-generate slug if field is 'title'
     *
     * @param  mixed  $value  The new value (not used directly as we read from $this->translations)
     * @param  string|null  $key  Dot-notation path within translations array (e.g., "fr.title")
     */
    public function updatedTranslations(mixed $value, ?string $key = null): void
    {
        // Si $key est null (set sur tout le tableau), on ne fait rien
        if ($key === null) {
            return;
        }

        // Key format from Livewire: "{locale}.title" or "{locale}.slug" etc.
        $parts = explode('.', $key);

        if (count($parts) < 2) {
            return;
        }

        $locale = $parts[0];
        $field = $parts[1];

        // Auto-generate slug when title changes
        if ($field === 'title') {
            $title = $this->translations[$locale]['title'] ?? '';
            $this->translations[$locale]['slug'] = Str::slug($title);
        }
    }
}
