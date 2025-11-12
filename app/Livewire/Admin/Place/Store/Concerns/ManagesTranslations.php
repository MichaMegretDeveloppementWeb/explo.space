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
 * - public string $translationSourceLocale = ''
 * - public string $translationTargetLocale = ''
 * - public array $fieldsToOverwrite = []
 * - public array $selectedFieldsToOverwrite = []
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

            $this->dispatch('flash-message',
                type: 'translation_success',
                message: "Contenu traduit avec succès depuis {$this->detectedLanguageName}."
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
     * Traduire un champ individuel depuis sa langue détectée vers le français
     * Utilisé pour les EditRequest avec des champs en langues "autres" (ni FR ni EN)
     */
    public function translateFieldFromSource(string $field): void
    {
        // Sécurité: Vérifier que nous sommes bien en mode édition depuis EditRequest
        if (! $this->editRequestId) {
            $this->addError('translation', 'Action non autorisée.');

            return;
        }

        // Vérifier que le champ a une langue détectée
        if (! isset($this->fieldLanguages[$field])) {
            $this->addError('translation', 'Langue du champ non détectée.');

            return;
        }

        $sourceLang = $this->fieldLanguages[$field];

        // Vérifier que c'est bien une langue "autre" (ni fr ni en)
        if ($sourceLang === 'fr' || $sourceLang === 'en' || $sourceLang === 'unknown' || $sourceLang === 'none') {
            $this->addError('translation', 'Ce champ ne nécessite pas de traduction.');

            return;
        }

        // Récupérer le texte actuel du champ dans l'onglet FR
        $sourceText = $this->translations['fr'][$field] ?? '';

        if (empty($sourceText)) {
            $this->addError('translation', 'Aucun contenu à traduire.');

            return;
        }

        try {
            // Utiliser le service Strategy existant
            $translationService = app(\App\Contracts\Translation\TranslationStrategyInterface::class);

            // Vérifier l'usage DeepL
            $translationService->checkUsage();

            // Traduire le texte
            $translatedText = $translationService->translate(
                $sourceText,
                $sourceLang,
                'fr'
            );

            if (! empty($translatedText)) {
                // Remplacer le champ FR avec la traduction
                $this->translations['fr'][$field] = $translatedText;

                // Auto-generate slug from title si c'est le titre
                if ($field === 'title') {
                    $this->translations['fr']['slug'] = \Illuminate\Support\Str::slug($translatedText);
                }

                // Marquer le champ comme traduit
                $this->fieldTranslatedFrom[$field] = $sourceLang;

                // Récupérer le nom de la langue depuis la config
                $languageNames = config(
                    'translation.providers.'.config('translation.default_provider').'.language_names'
                );
                $languageName = $languageNames[$sourceLang] ?? strtoupper($sourceLang);

                $this->dispatch('flash-message',
                    type: 'translation_success',
                    message: "Champ traduit avec succès depuis {$languageName}."
                );
            } else {
                $this->addError('translation', 'La traduction a échoué.');
            }

        } catch (\App\Exceptions\Translation\TranslationException $e) {
            \Illuminate\Support\Facades\Log::warning('Field translation from source language failed', [
                'edit_request_id' => $this->editRequestId,
                'field' => $field,
                'detected_language' => $sourceLang,
                'technical_error' => $e->getTechnicalMessage(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('translation', $e->getDisplayMessage());

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Unexpected field translation error', [
                'edit_request_id' => $this->editRequestId,
                'field' => $field,
                'detected_language' => $sourceLang,
                'error' => $e->getMessage(),
            ]);

            $this->addError('translation',
                'Une erreur inattendue est survenue lors de la traduction. Les données originales ont été conservées.'
            );
        }
    }

    /**
     * Initiate automatic translation from current locale to target locale
     *
     * Logique inversée : on traduit DEPUIS la locale actuelle VERS l'autre locale
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
            $this->dispatch('flash-message', type: 'warning', message: 'Aucun champ à traduire dans la langue source.');

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
            $this->dispatch('flash-message', type: 'error', message: $e->getDisplayMessage());
            Log::warning('Translation failed', [
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'technical_error' => $e->getTechnicalMessage(),
                'error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('flash-message', type: 'error', message: 'Une erreur inattendue est survenue lors de la traduction. Veuillez réessayer.');
            Log::error('Unexpected translation error', [
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
     * Apply pending translations to target locale fields
     *
     * @param  bool  $onlyEmpty  Si true, ne traduit QUE les champs vides (ignore la sélection)
     *
     * Logique normale ($onlyEmpty = false) :
     * - Tous les champs VIDES sont automatiquement traduits
     * - Les champs REMPLIS ne sont traduits QUE s'ils sont dans $selectedFieldsToOverwrite
     *
     * Logique "champs vides uniquement" ($onlyEmpty = true) :
     * - SEULEMENT les champs VIDES sont traduits
     * - Les champs REMPLIS sont ignorés (même s'ils sont sélectionnés)
     */
    private function applyTranslations(bool $onlyEmpty = false): void
    {
        $targetLocale = $this->translationTargetLocale;
        $successCount = 0;
        $failedFields = [];

        foreach ($this->pendingTranslations as $field => $translation) {
            $currentValue = $this->translations[$targetLocale][$field] ?? '';
            $isFieldEmpty = empty(trim($currentValue));
            $isFieldSelected = in_array($field, $this->selectedFieldsToOverwrite);

            // Déterminer si on doit traduire ce champ
            $shouldTranslate = false;

            if ($onlyEmpty) {
                // Mode "champs vides uniquement" : traduire SEULEMENT les champs vides
                $shouldTranslate = $isFieldEmpty;
            } else {
                // Mode normal : traduire les champs vides OU les champs sélectionnés
                $shouldTranslate = $isFieldEmpty || $isFieldSelected;
            }

            if ($shouldTranslate) {
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
        }

        // Reset pending state
        $this->pendingTranslations = [];
        $this->fieldsToOverwrite = [];
        $this->selectedFieldsToOverwrite = [];

        // Show appropriate message
        if ($successCount > 0 && empty($failedFields)) {
            $this->dispatch('flash-message', type: 'success', message: "{$successCount} champ(s) traduit(s) avec succès.");
        } elseif ($successCount > 0 && ! empty($failedFields)) {
            $fieldsList = implode(', ', $failedFields);
            $this->dispatch('flash-message', type: 'warning', message: "{$successCount} champ(s) traduit(s). Échec pour : {$fieldsList}");
        } else {
            $this->dispatch('flash-message', type: 'error', message: 'Aucun champ n\'a pu être traduit.');
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
