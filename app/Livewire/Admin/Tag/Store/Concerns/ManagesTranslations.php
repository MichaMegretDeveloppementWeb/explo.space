<?php

namespace App\Livewire\Admin\Tag\Store\Concerns;

use App\Contracts\Translation\TranslationStrategyInterface;
use App\Exceptions\Translation\TranslationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Trait ManagesTranslations
 *
 * Gestion des traductions automatiques pour le formulaire Tag.
 *
 * IMPORTANT: Les propriétés publiques Livewire doivent être déclarées dans le composant principal.
 */
trait ManagesTranslations
{
    /**
     * Initiate automatic translation from current locale to target locale
     */
    public function initiateTranslation(string $sourceLocale): void
    {
        $this->resetErrorBag('translation');
        $this->translationSourceLocale = $sourceLocale;

        // Determine target locale (the other one)
        $targetLocale = $sourceLocale === 'fr' ? 'en' : 'fr';
        $this->translationTargetLocale = $targetLocale;

        // Get source texts (only non-empty fields from SOURCE locale)
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
                // Show confirmation modal with checkboxes
                $this->fieldsToOverwrite = $fieldsToOverwrite;
                $this->selectedFieldsToOverwrite = []; // Tous décochés par défaut
                $this->showTranslationConfirmation = true;
            } else {
                // Apply translations directly (all target fields are empty)
                $this->applyTranslations();

                // Switch to target locale tab after translation
                $this->activeTranslationTab = $targetLocale;
            }

        } catch (TranslationException $e) {
            session()->flash('error', $e->getDisplayMessage());
            Log::warning('Tag translation failed', [
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'technical_error' => $e->getTechnicalMessage(),
                'error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur inattendue est survenue lors de la traduction. Veuillez réessayer.');
            Log::error('Unexpected tag translation error', [
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Confirm and apply translations to selected fields + all empty fields
     */
    public function confirmTranslation(): void
    {
        $this->showTranslationConfirmation = false;
        $targetLocale = $this->translationTargetLocale;
        $this->applyTranslations();

        // Switch to target locale tab after translation
        $this->activeTranslationTab = $targetLocale;
    }

    /**
     * Apply translations only to empty fields (skip all filled fields)
     */
    public function confirmTranslationOnlyEmpty(): void
    {
        $this->showTranslationConfirmation = false;
        $targetLocale = $this->translationTargetLocale;
        $this->applyTranslations(onlyEmpty: true);

        // Switch to target locale tab after translation
        $this->activeTranslationTab = $targetLocale;
    }

    /**
     * Cancel translation (close modal)
     */
    public function cancelTranslation(): void
    {
        $this->showTranslationConfirmation = false;
        $this->pendingTranslations = [];
        $this->fieldsToOverwrite = [];
        $this->selectedFieldsToOverwrite = [];
    }

    /**
     * Get source texts from specified locale (only non-empty fields)
     *
     * @return array<string, string>
     */
    private function getSourceTexts(string $sourceLocale): array
    {
        $texts = [];

        // Name (always required for tags)
        if (! empty($this->translations[$sourceLocale]['name'])) {
            $texts['name'] = $this->translations[$sourceLocale]['name'];
        }

        // Description (optional)
        if (! empty($this->translations[$sourceLocale]['description'])) {
            $texts['description'] = $this->translations[$sourceLocale]['description'];
        }

        return $texts;
    }

    /**
     * Check which target fields are already filled and would be overwritten
     *
     * @param  array<int, string>  $translatedFields
     * @return array<int, string>
     */
    private function getFieldsToOverwrite(string $targetLocale, array $translatedFields): array
    {
        $fieldsToOverwrite = [];

        foreach ($translatedFields as $field) {
            $currentValue = $this->translations[$targetLocale][$field] ?? '';

            if (! empty($currentValue)) {
                $fieldsToOverwrite[] = $field;
            }
        }

        return $fieldsToOverwrite;
    }

    /**
     * Apply pending translations to target locale fields
     *
     * @param  bool  $onlyEmpty  Si true, ne traduit QUE les champs vides (ignore la sélection)
     */
    private function applyTranslations(bool $onlyEmpty = false): void
    {
        $targetLocale = $this->translationTargetLocale;
        $successCount = 0;
        $failedFields = [];

        foreach ($this->pendingTranslations as $field => $translation) {
            $currentValue = $this->translations[$targetLocale][$field] ?? '';
            $isEmpty = empty($currentValue);

            // Logique conditionnelle selon $onlyEmpty
            if ($onlyEmpty) {
                // Mode "champs vides uniquement" : skip les champs remplis
                if (! $isEmpty) {
                    continue;
                }
            } else {
                // Mode normal : champs vides OU champs sélectionnés
                if (! $isEmpty && ! in_array($field, $this->selectedFieldsToOverwrite)) {
                    continue;
                }
            }

            // Apply translation
            if (! empty($translation)) {
                $this->translations[$targetLocale][$field] = $translation;

                // Auto-generate slug from translated name
                if ($field === 'name') {
                    $this->translations[$targetLocale]['slug'] = Str::slug($translation);
                }

                $successCount++;
            } else {
                $failedFields[] = $field;
            }
        }

        // Clear pending translations
        $this->pendingTranslations = [];
        $this->fieldsToOverwrite = [];
        $this->selectedFieldsToOverwrite = [];

        // Show feedback
        if ($successCount > 0) {
            $message = $successCount === 1
                ? "{$successCount} champ traduit avec succès."
                : "{$successCount} champs traduits avec succès.";

            session()->flash('success', $message);
        }

        if (! empty($failedFields)) {
            $fieldsList = implode(', ', $failedFields);
            session()->flash('warning', "Certains champs n'ont pas pu être traduits : {$fieldsList}");
        }
    }

    /**
     * Update slug when name changes (real-time slug generation)
     */
    public function updatedTranslations(mixed $value, string $key): void
    {
        // Extract locale and field from key (format: "fr.name")
        $parts = explode('.', $key);

        if (count($parts) === 2 && $parts[1] === 'name') {
            $locale = $parts[0];
            $name = $this->translations[$locale]['name'] ?? '';

            if (! empty($name)) {
                $this->translations[$locale]['slug'] = Str::slug($name);
            }
        }
    }
}
