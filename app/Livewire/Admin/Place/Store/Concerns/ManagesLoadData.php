<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

use App\Models\EditRequest;
use App\Models\PlaceRequest;

trait ManagesLoadData
{
    private function loadPlaceForEdit(int $placeId): void
    {
        $service = app(\App\Services\Admin\Place\Edit\PlaceUpdateService::class);
        $place = $service->loadForEdit($placeId);

        if (! $place) {
            session()->flash('error', 'Lieu non trouvé.');
            $this->redirect(route('admin.places.index'));

            return;
        }

        // Load base data avec normalisation des coordonnées
        $this->latitude = $this->normalizeCoordinate($place->latitude);
        $this->longitude = $this->normalizeCoordinate($place->longitude);
        $this->address = $place->address;
        $this->placeAddress = $place->address;
        $this->queryAddress = $place->address ?? '';
        $this->is_featured = $place->is_featured;

        // Load translations
        foreach ($place->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'slug' => $translation->slug,
                'description' => $translation->description,
                'practical_info' => $translation->practical_info ?? '',
                'status' => $translation->status,
            ];
        }

        // Load relations
        $this->categoryIds = $place->categories->pluck('id')->toArray();
        $this->tagIds = $place->tags->pluck('id')->toArray();

        // Load photos with translations
        $this->existingPhotos = $place->photos->sortBy('sort_order')->map(function ($photo) {
            $translations = [];
            foreach ($photo->translations as $translation) {
                $translations[$translation->locale] = [
                    'alt_text' => $translation->alt_text,
                ];
            }

            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'medium_url' => $photo->medium_url,
                'is_main' => $photo->is_main,
                'sort_order' => $photo->sort_order,
                'translations' => $translations,
            ];
        })->values()->toArray();

        $mainPhoto = collect($this->existingPhotos)->firstWhere('is_main', true);
        $this->mainPhotoId = $mainPhoto['id'] ?? null;
    }

    private function loadFromPlaceRequest(int $placeRequestId): void
    {
        /** @var PlaceRequest|null $placeRequest */
        $placeRequest = PlaceRequest::query()->with('photos')->find($placeRequestId);

        if (! $placeRequest) {
            session()->flash('error', 'Demande de lieu non trouvée.');

            return;
        }

        /** @var \App\Enums\RequestStatus $status */
        $status = $placeRequest->status;

        if (! $status->canBeModerated()) {
            session()->flash('error', 'Demande de lieu déjà traitée.');

            return;
        }

        // Charger les coordonnées avec normalisation à 6 décimales
        $this->latitude = $placeRequest->latitude ? $this->normalizeCoordinate($placeRequest->latitude) : null;
        $this->longitude = $placeRequest->longitude ? $this->normalizeCoordinate($placeRequest->longitude) : null;
        $this->address = $placeRequest->address;
        $this->placeAddress = $placeRequest->address;
        $this->queryAddress = $placeRequest->address ?? '';

        // Initialiser la détection de langue
        $this->detectedLanguage = $placeRequest->detected_language;

        // Récupérer le nom en français depuis la config
        if ($this->detectedLanguage !== 'unknown') {
            $languageNames = config(
                'translation.providers.'.config('translation.default_provider').'.language_names'
            );
            $this->detectedLanguageName = $languageNames[$this->detectedLanguage] ?? strtoupper($this->detectedLanguage);
        }

        // Initialiser l'onglet actif avec la langue détectée si FR ou EN
        if ($this->detectedLanguage === 'fr' || $this->detectedLanguage === 'en') {
            $this->activeTranslationTab = $this->detectedLanguage;
        }

        // Appliquer la logique selon le scénario de détection
        if ($this->detectedLanguage === 'fr' || $this->detectedLanguage === 'en') {
            // Scénario 1: FR ou EN détecté - remplir l'onglet correspondant
            $targetLocale = $this->detectedLanguage;
            if (isset($this->translations[$targetLocale])) {
                $this->translations[$targetLocale] = [
                    'title' => $placeRequest->title,
                    'slug' => $placeRequest->slug,
                    'description' => $placeRequest->description,
                    'practical_info' => $placeRequest->practical_info ?? '',
                    'status' => 'published',
                ];
            }
        } elseif ($this->detectedLanguage !== 'unknown') {
            // Scénario 2: Autre langue détectée - remplir FR avec données originales + bouton traduction
            $this->translations['fr'] = [
                'title' => $placeRequest->title,
                'slug' => $placeRequest->slug,
                'description' => $placeRequest->description,
                'practical_info' => $placeRequest->practical_info ?? '',
                'status' => 'published',
            ];
            $this->showSpecialTranslateButton = true;
        } else {
            // Scénario 3: Unknown - remplir FR avec données originales, sans bouton
            $this->translations['fr'] = [
                'title' => $placeRequest->title,
                'slug' => $placeRequest->slug,
                'description' => $placeRequest->description,
                'practical_info' => $placeRequest->practical_info ?? '',
                'status' => 'published',
            ];
        }

        // Load photos from PlaceRequest
        if ($placeRequest->photos->isNotEmpty()) {
            $this->placeRequestPhotos = $placeRequest->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'source' => 'place_request',
                ];
            })->toArray();
        }
    }

    /**
     * Load EditRequest overlay for edit mode
     *
     * Called after loadPlaceForEdit() to overlay proposed changes from EditRequest
     *
     * @param  array<int, string>  $selectedFields  Fields selected by admin to apply
     * @param  array<int, int>  $selectedPhotos  Photos selected by admin to apply
     */
    private function loadFromEditRequest(int $editRequestId, array $selectedFields, array $selectedPhotos): void
    {
        /** @var EditRequest|null $editRequest */
        $editRequest = EditRequest::query()->find($editRequestId);

        if (! $editRequest) {
            session()->flash('error', 'Demande de modification non trouvée.');

            return;
        }

        /** @var \App\Enums\RequestStatus $status */
        $status = $editRequest->status;

        if (! $status->canBeModerated()) {
            session()->flash('error', 'Demande de modification déjà traitée.');

            return;
        }

        // Initialiser la détection de langue (comme PlaceRequest)
        $this->detectedLanguage = $editRequest->detected_language;

        if ($this->detectedLanguage !== 'unknown') {
            $languageNames = config(
                'translation.providers.'.config('translation.default_provider').'.language_names'
            );
            $this->detectedLanguageName = $languageNames[$this->detectedLanguage] ?? strtoupper($this->detectedLanguage);
        }

        // Store selected fields/photos for service layer (needed for applied_changes tracking)
        $this->selectedFields = $selectedFields;
        $this->selectedPhotos = $selectedPhotos;

        // Get proposed data (used by both modification and GPS changes)
        $proposedData = $editRequest->proposed_data ?? [];

        // Type: modification - Apply selected fields as overlay with per-field language detection
        if ($editRequest->isModification() && ! empty($selectedFields)) {
            // Store which fields are highlighted (for visual indication)
            $this->highlightedFields = $selectedFields;

            // Créer un index pour retrouver rapidement les champs par nom
            $proposedDataByField = [];
            foreach ($proposedData as $fieldData) {
                if (isset($fieldData['field'])) {
                    $proposedDataByField[$fieldData['field']] = $fieldData;
                }
            }

            // Tracking pour sélection intelligente de l'onglet
            $hasFieldInFrOrOther = false;
            $hasFieldInEn = false;

            foreach ($selectedFields as $field) {
                // Trouver le fieldData correspondant au champ sélectionné
                if (! isset($proposedDataByField[$field])) {
                    continue;
                }

                $fieldData = $proposedDataByField[$field];
                $newValue = $fieldData['new_value'] ?? null;
                $detectedLang = $fieldData['detected_language'] ?? 'unknown';

                if ($newValue === null) {
                    continue;
                }

                // Stocker la langue détectée pour ce champ
                if (in_array($field, ['title', 'description', 'practical_info'])) {
                    $this->fieldLanguages[$field] = $detectedLang;
                }

                // Déterminer la locale cible selon la langue détectée
                // FR : langue FR OU autre langue (ni FR ni EN)
                // EN : langue EN uniquement
                $targetLocale = 'fr'; // Par défaut
                if ($detectedLang === 'en') {
                    $targetLocale = 'en';
                    $hasFieldInEn = true;
                } else {
                    // 'fr' ou autre langue (pl, es, etc.) → onglet FR
                    $hasFieldInFrOrOther = true;
                }

                // Store old value for comparison (before overlay)
                if (in_array($field, ['title', 'description', 'practical_info'])) {
                    $oldValue = $this->translations[$targetLocale][$field] ?? '';
                    // Stocker avec le path complet pour l'affichage dans la vue
                    $this->oldValues["translations.{$targetLocale}.{$field}"] = $oldValue;
                    // Marquer le champ comme highlighted avec path complet
                    $this->highlightedFields[] = "translations.{$targetLocale}.{$field}";
                } elseif ($field === 'address') {
                    $this->oldValues['address'] = $this->address;
                    $this->highlightedFields[] = 'address';
                }

                // Apply overlay
                if ($field === 'title') {
                    $this->translations[$targetLocale]['title'] = $newValue;
                } elseif ($field === 'description') {
                    $this->translations[$targetLocale]['description'] = $newValue;
                } elseif ($field === 'practical_info') {
                    $this->translations[$targetLocale]['practical_info'] = $newValue;
                } elseif ($field === 'address') {
                    $this->address = $newValue;
                    $this->placeAddress = $newValue;
                    $this->queryAddress = $newValue;
                }
            }

            // Sélection intelligente de l'onglet par défaut
            if ($hasFieldInFrOrOther) {
                $this->activeTranslationTab = 'fr';
            } elseif ($hasFieldInEn) {
                $this->activeTranslationTab = 'en';
            }
        }

        // Handle GPS modification (chercher "coordinates" dans suggested_changes)
        // Structure: [{"field": "coordinates", "new_value": {"lat": 44.164505, "lng": 3.289591}}]
        // IMPORTANT: N'appliquer QUE si "coordinates" est dans selectedFields
        if (in_array('coordinates', $selectedFields)) {
            $coordinatesField = collect($editRequest->suggested_changes ?? [])
                ->firstWhere('field', 'coordinates');

            if ($coordinatesField && isset($coordinatesField['new_value'])) {
                $newCoords = $coordinatesField['new_value'];
                $proposedLat = $newCoords['lat'] ?? null;
                $proposedLng = $newCoords['lng'] ?? null;

                if ($proposedLat !== null && $proposedLng !== null) {
                    // Store original coords for double marker visualization
                    $this->originalLatitude = $this->latitude;
                    $this->originalLongitude = $this->longitude;

                    // Apply proposed coords
                    $this->latitude = $this->normalizeCoordinate((float) $proposedLat);
                    $this->longitude = $this->normalizeCoordinate((float) $proposedLng);

                    // Mark as highlighted
                    $this->highlightedFields[] = 'latitude';
                    $this->highlightedFields[] = 'longitude';

                    // Store old values
                    $this->oldValues['latitude'] = $this->originalLatitude;
                    $this->oldValues['longitude'] = $this->originalLongitude;
                }
            }
        }

        // Type: photo_suggestion - Load selected photos
        if ($editRequest->isPhotoSuggestion() && ! empty($selectedPhotos)) {
            // Extraire correctement la structure ['photos' => ['file1.jpg', ...]]
            $suggestedPhotoData = $editRequest->suggested_photo_paths ?? [];
            $photos = $suggestedPhotoData['photos'] ?? [];

            foreach ($selectedPhotos as $index) {
                if (! isset($photos[$index])) {
                    continue;
                }

                $filename = $photos[$index]; // Raw filename (ex: 'photo.jpg')

                $this->editRequestPhotos[] = [
                    'id' => $editRequest->id.'-'.$index, // Unique ID combining editRequestId and photo index
                    'url' => \Storage::disk('edit_request_photos')->url($editRequest->id.'/'.$filename),
                    'source' => 'edit_request',
                    'filename' => $filename, // Raw filename pour copyEditRequestPhotoWithThumbnails()
                    'edit_request_id' => $editRequest->id, // ID pour construire le chemin source
                ];
            }
        }

        // Note: La sélection de l'onglet actif est gérée par la logique intelligente
        // dans la section "Type: modification" ci-dessus (lignes 264-269)
        // qui détermine l'onglet selon les champs réellement présents (FR/autres vs EN)
    }
}
