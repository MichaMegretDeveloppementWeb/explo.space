<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

use App\Contracts\Repositories\Admin\Place\Edit\PlaceUpdateRepositoryInterface;
use App\Contracts\Translation\TranslationStrategyInterface;
use App\Exceptions\Translation\TranslationException;
use App\Models\Photo;
use Illuminate\Support\Facades\Log;

/**
 * Trait ManagesPhotoTranslations
 *
 * Gestion des traductions alt_text pour les photos dans le formulaire Place.
 *
 * IMPORTANT: Les propriétés publiques Livewire doivent être déclarées dans le composant principal,
 * pas dans le trait. Ce trait utilise les propriétés suivantes qui doivent être définies dans PlaceStoreForm:
 * - public ?string $currentPhotoId = null
 * - public string $currentPhotoSource = '' // 'existing', 'pending', 'placeRequest', 'editRequest'
 * - public array $currentPhotoTranslations = ['fr' => ['alt_text' => ''], 'en' => ['alt_text' => '']]
 * - public bool $showPhotoTranslationModal = false
 * - public string $currentPhotoPreviewUrl = ''
 */
trait ManagesPhotoTranslations
{
    /**
     * Open photo translation modal for a specific photo
     */
    public function openPhotoTranslationModal(string $photoId, string $source): void
    {
        $this->currentPhotoId = $photoId;
        $this->currentPhotoSource = $source;

        // Load existing translations if available
        $this->loadPhotoTranslations($photoId, $source);

        $this->showPhotoTranslationModal = true;
    }

    /**
     * Load existing translations for a photo
     */
    private function loadPhotoTranslations(string $photoId, string $source): void
    {
        // Reset translations
        $this->currentPhotoTranslations = [
            'fr' => ['alt_text' => ''],
            'en' => ['alt_text' => ''],
        ];
        $this->currentPhotoPreviewUrl = '';

        // Load based on source
        if ($source === 'existing') {
            // Extract numeric photo ID from "existing_123" format
            $numericId = (int) str_replace('existing_', '', $photoId);

            // Find photo in existingPhotos array
            $photoData = collect($this->existingPhotos)->firstWhere('id', $numericId);

            if ($photoData) {
                $this->currentPhotoPreviewUrl = $photoData['medium_url'] ?? $photoData['url'] ?? '';

                // Load translations from photoData if available
                if (isset($photoData['translations'])) {
                    foreach ($photoData['translations'] as $locale => $translation) {
                        if (isset($this->currentPhotoTranslations[$locale])) {
                            $this->currentPhotoTranslations[$locale]['alt_text'] = $translation['alt_text'] ?? '';
                        }
                    }
                }
            }
        } elseif ($source === 'pending') {
            // Extract index from "pending_0" format
            $index = (int) str_replace('pending_', '', $photoId);

            if (isset($this->photos[$index])) {
                $photoFile = $this->photos[$index];
                $this->currentPhotoPreviewUrl = $photoFile->temporaryUrl();

                // Load translations from photoTranslations if available
                $translationKey = "pending_{$index}";
                if (isset($this->photoTranslations[$translationKey])) {
                    $this->currentPhotoTranslations = array_merge(
                        $this->currentPhotoTranslations,
                        $this->photoTranslations[$translationKey]
                    );
                }
            }
        } elseif ($source === 'placeRequest') {
            // Extract index from "placeRequest_0" format
            $index = (int) str_replace('placeRequest_', '', $photoId);

            if (isset($this->placeRequestPhotos[$index])) {
                $photoData = $this->placeRequestPhotos[$index];
                $this->currentPhotoPreviewUrl = $photoData['medium_url'] ?? $photoData['url'] ?? '';

                // Load translations from photoTranslations if available
                $translationKey = "placeRequest_{$index}";
                if (isset($this->photoTranslations[$translationKey])) {
                    $this->currentPhotoTranslations = array_merge(
                        $this->currentPhotoTranslations,
                        $this->photoTranslations[$translationKey]
                    );
                }
            }
        } elseif (str_starts_with($source, 'editRequest')) {
            // Format: "editRequest-123-2" (editRequestId-index)
            $parts = explode('-', $source);
            $editRequestId = $parts[1] ?? null;
            $index = $parts[2] ?? null;

            if ($editRequestId && $index !== null && isset($this->editRequestPhotos[$index])) {
                $photoData = $this->editRequestPhotos[$index];
                $this->currentPhotoPreviewUrl = $photoData['medium_url'] ?? $photoData['url'] ?? '';

                // Load translations from photoTranslations if available
                $translationKey = "editRequest_{$editRequestId}_{$index}";
                if (isset($this->photoTranslations[$translationKey])) {
                    $this->currentPhotoTranslations = array_merge(
                        $this->currentPhotoTranslations,
                        $this->photoTranslations[$translationKey]
                    );
                }
            }
        }
    }

    /**
     * Save photo translations from modal
     */
    public function savePhotoTranslations(): void
    {
        // Validate alt_text max length (125 characters)
        $this->validate([
            'currentPhotoTranslations.fr.alt_text' => ['nullable', 'string', 'max:125'],
            'currentPhotoTranslations.en.alt_text' => ['nullable', 'string', 'max:125'],
        ]);

        // Determine translation key based on source
        $translationKey = $this->getPhotoTranslationKey($this->currentPhotoId, $this->currentPhotoSource);

        // Store translations in photoTranslations array
        $this->photoTranslations[$translationKey] = [
            'fr' => [
                'alt_text' => $this->currentPhotoTranslations['fr']['alt_text'] ?? null,
            ],
            'en' => [
                'alt_text' => $this->currentPhotoTranslations['en']['alt_text'] ?? null,
            ],
        ];

        // If editing existing photo, save immediately to database
        if ($this->currentPhotoSource === 'existing') {
            $this->saveExistingPhotoTranslations();
        }

        $this->dispatch('flash-message', type: 'success', message: 'Texte SEO mis à jour avec succès.');
        $this->closePhotoTranslationModal();
    }

    /**
     * Save translations for existing photo immediately to database
     */
    private function saveExistingPhotoTranslations(): void
    {
        if (! $this->placeId) {
            return;
        }

        try {
            $numericId = (int) str_replace('existing_', '', $this->currentPhotoId);

            // Find photo
            $photo = Photo::find($numericId);

            if (! $photo || $photo->place_id !== $this->placeId) {
                Log::warning('Photo not found or does not belong to place', [
                    'photo_id' => $numericId,
                    'place_id' => $this->placeId,
                ]);

                return;
            }

            // Update translations via repository
            $repository = app(PlaceUpdateRepositoryInterface::class);
            $repository->updatePhotoTranslations($photo, $this->currentPhotoTranslations);

            Log::info('Photo translations updated', [
                'photo_id' => $photo->id,
                'place_id' => $this->placeId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save photo translations', [
                'photo_id' => $this->currentPhotoId,
                'place_id' => $this->placeId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('flash-message', type: 'error', message: 'Erreur lors de la sauvegarde des traductions.');
        }
    }

    /**
     * Translate photo alt_text automatically
     */
    public function translatePhotoAltText(string $sourceLocale): void
    {
        $targetLocale = $sourceLocale === 'fr' ? 'en' : 'fr';
        $sourceText = $this->currentPhotoTranslations[$sourceLocale]['alt_text'] ?? '';

        // Vérifier si le champ source est vide
        if (empty($sourceText)) {
            $this->dispatch('flash-message',
                type: 'warning',
                message: 'Le champ source est vide. Veuillez d\'abord saisir un texte avant de traduire.'
            );

            return;
        }

        try {
            $translationService = app(TranslationStrategyInterface::class);

            // Vérifier l'usage DeepL avant de traduire
            $translationService->checkUsage();

            // Traduire le texte
            $translatedText = $translationService->translate(
                $sourceText,
                $sourceLocale,
                $targetLocale
            );

            // Vérifier que la traduction n'est pas vide
            if (empty($translatedText)) {
                $this->dispatch('flash-message',
                    type: 'warning',
                    message: 'La traduction a retourné un résultat vide.'
                );

                Log::warning('Photo alt_text translation returned empty result', [
                    'photo_id' => $this->currentPhotoId,
                    'source_locale' => $sourceLocale,
                    'target_locale' => $targetLocale,
                    'source_text' => $sourceText,
                ]);

                return;
            }

            // Appliquer la traduction
            $this->currentPhotoTranslations[$targetLocale]['alt_text'] = $translatedText;

            // Message de succès
            $targetLanguageName = $targetLocale === 'fr' ? 'français' : 'anglais';
            $this->dispatch('flash-message',
                type: 'translation_success',
                message: "Texte traduit avec succès vers {$targetLanguageName}."
            );

            // Dispatcher un événement JavaScript pour switcher l'onglet
            $this->dispatch('switch-photo-translation-tab', locale: $targetLocale);

            Log::info('Photo alt_text translated successfully', [
                'photo_id' => $this->currentPhotoId,
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
            ]);

        } catch (TranslationException $e) {
            $this->dispatch('flash-message',
                type: 'error',
                message: $e->getDisplayMessage()
            );

            Log::warning('Photo alt_text translation failed', [
                'photo_id' => $this->currentPhotoId,
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'technical_error' => $e->getTechnicalMessage(),
                'error' => $e->getMessage(),
            ]);

        } catch (\Exception $e) {
            $this->dispatch('flash-message',
                type: 'error',
                message: 'Une erreur inattendue est survenue lors de la traduction. Veuillez réessayer.'
            );

            Log::error('Unexpected photo translation error', [
                'photo_id' => $this->currentPhotoId,
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Close photo translation modal
     */
    public function closePhotoTranslationModal(): void
    {
        $this->showPhotoTranslationModal = false;
        $this->currentPhotoId = null;
        $this->currentPhotoSource = '';
        $this->currentPhotoTranslations = [
            'fr' => ['alt_text' => ''],
            'en' => ['alt_text' => ''],
        ];
        $this->currentPhotoPreviewUrl = '';
    }

    /**
     * Get translation key for photo based on ID and source
     */
    private function getPhotoTranslationKey(string $photoId, string $source): string
    {
        // Return key as-is for pending/placeRequest/editRequest (already prefixed)
        // For existing, return with prefix
        if ($source === 'existing') {
            return $photoId; // Already in "existing_123" format
        } elseif ($source === 'pending') {
            return $photoId; // Already in "pending_0" format
        } elseif ($source === 'placeRequest') {
            return $photoId; // Already in "placeRequest_0" format
        } else {
            // editRequest format: convert source to key
            return str_replace('-', '_', $photoId); // e.g., "editRequest_123_2"
        }
    }
}
