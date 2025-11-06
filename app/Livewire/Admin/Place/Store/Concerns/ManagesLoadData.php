<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

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

        // Load photos
        $this->existingPhotos = $place->photos->sortBy('sort_order')->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'medium_url' => $photo->medium_url,
                'is_main' => $photo->is_main,
                'sort_order' => $photo->sort_order,
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
}
