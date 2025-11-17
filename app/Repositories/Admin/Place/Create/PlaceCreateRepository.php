<?php

namespace App\Repositories\Admin\Place\Create;

use App\Contracts\Repositories\Admin\Place\Create\PlaceCreateRepositoryInterface;
use App\Enums\RequestStatus;
use App\Models\Photo;
use App\Models\PhotoTranslation;
use App\Models\Place;
use App\Models\PlaceRequest;
use App\Models\PlaceTranslation;
use Illuminate\Support\Str;

class PlaceCreateRepository implements PlaceCreateRepositoryInterface
{
    /**
     * @param  array{latitude: float, longitude: float, address: ?string, admin_id: int, is_featured?: bool, request_id?: ?int}  $placeData
     */
    public function create(array $placeData): Place
    {
        return Place::create([
            'latitude' => $placeData['latitude'],
            'longitude' => $placeData['longitude'],
            'address' => $placeData['address'] ?? null,
            'admin_id' => $placeData['admin_id'],
            'is_featured' => $placeData['is_featured'] ?? false,
            'request_id' => $placeData['request_id'] ?? null,
        ]);
    }

    public function createTranslations(Place $place, array $translations): void
    {
        foreach ($translations as $locale => $translationData) {
            PlaceTranslation::create([
                'place_id' => $place->id,
                'locale' => $locale,
                'title' => $translationData['title'],
                'slug' => $translationData['slug'] ?? Str::slug($translationData['title']),
                'description' => $translationData['description'],
                'practical_info' => $translationData['practical_info'] ?? null,
                'status' => $translationData['status'] ?? 'published',
            ]);
        }
    }

    public function attachCategories(Place $place, array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $place->categories()->attach($categoryIds);
    }

    public function attachTags(Place $place, array $tagIds): void
    {
        if (empty($tagIds)) {
            return;
        }

        $place->tags()->attach($tagIds);
    }

    public function createPhotos(Place $place, array $photoData): void
    {
        if (empty($photoData)) {
            return;
        }

        foreach ($photoData as $photo) {
            Photo::create([
                'place_id' => $place->id,
                'filename' => $photo['filename'],
                'original_name' => $photo['original_name'],
                'mime_type' => $photo['mime_type'],
                'size' => $photo['size'],
                'is_main' => $photo['is_main'] ?? false,
                'sort_order' => $photo['sort_order'],
            ]);
        }
    }

    /**
     * Create a single photo and return it.
     *
     * @param  array{filename: string, original_name: string, mime_type: string, size: int, is_main: bool, sort_order: int}  $photoData
     */
    public function createPhoto(Place $place, array $photoData): Photo
    {
        return Photo::create([
            'place_id' => $place->id,
            'filename' => $photoData['filename'],
            'original_name' => $photoData['original_name'],
            'mime_type' => $photoData['mime_type'],
            'size' => $photoData['size'],
            'is_main' => $photoData['is_main'] ?? false,
            'sort_order' => $photoData['sort_order'],
        ]);
    }

    public function createPhotoTranslations(Photo $photo, array $translations): void
    {
        if (empty($translations)) {
            return;
        }

        foreach ($translations as $locale => $translationData) {
            if (empty($translationData['alt_text'])) {
                continue; // Skip if no alt_text provided (nullable)
            }

            PhotoTranslation::create([
                'photo_id' => $photo->id,
                'locale' => $locale,
                'alt_text' => $translationData['alt_text'],
            ]);
        }
    }

    public function markPlaceRequestAsAccepted(PlaceRequest $placeRequest, int $adminId): void
    {
        $placeRequest->update([
            'status' => RequestStatus::Accepted,
            'processed_by_admin_id' => $adminId,
            'processed_at' => now(),
            'admin_reason' => null, // Nettoyer la raison de refus si elle existait
        ]);
    }

    public function findPlaceRequestById(int $id): ?PlaceRequest
    {
        return PlaceRequest::find($id);
    }
}
