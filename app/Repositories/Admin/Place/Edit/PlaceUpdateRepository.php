<?php

namespace App\Repositories\Admin\Place\Edit;

use App\Contracts\Repositories\Admin\Place\Edit\PlaceUpdateRepositoryInterface;
use App\Models\Photo;
use App\Models\PhotoTranslation;
use App\Models\Place;
use App\Models\PlaceTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlaceUpdateRepository implements PlaceUpdateRepositoryInterface
{
    public function findForEdit(int $id): ?Place
    {
        return Place::with([
            'translations',
            'categories',
            'tags',
            'photos' => function ($query) {
                $query->orderBy('sort_order');
            },
            'photos.translations' => function ($query) {
                $query->whereIn('locale', ['fr', 'en']);
            },
        ])->find($id);
    }

    /**
     * @param  array{latitude?: float, longitude?: float, address?: ?string, is_featured?: bool}  $placeData
     */
    public function update(Place $place, array $placeData): bool
    {
        return $place->update([
            'latitude' => $placeData['latitude'] ?? $place->latitude,
            'longitude' => $placeData['longitude'] ?? $place->longitude,
            'address' => $placeData['address'] ?? $place->address,
            'is_featured' => $placeData['is_featured'] ?? $place->is_featured,
        ]);
    }

    public function updateTranslations(Place $place, array $translations): void
    {
        foreach ($translations as $locale => $translationData) {
            PlaceTranslation::updateOrCreate(
                [
                    'place_id' => $place->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $translationData['title'],
                    'slug' => $translationData['slug'] ?? Str::slug($translationData['title']),
                    'description' => $translationData['description'],
                    'practical_info' => $translationData['practical_info'] ?? null,
                    'status' => $translationData['status'] ?? 'published',
                ]
            );
        }
    }

    public function syncCategories(Place $place, array $categoryIds): void
    {
        $place->categories()->sync($categoryIds);
    }

    public function syncTags(Place $place, array $tagIds): void
    {
        $place->tags()->sync($tagIds);
    }

    public function addPhotos(Place $place, array $photoData): void
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
     * Add a single photo and return it.
     *
     * @param  array{filename: string, original_name: string, mime_type: string, size: int, is_main: bool, sort_order: int}  $photoData
     */
    public function addPhoto(Place $place, array $photoData): Photo
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

    public function updatePhotoTranslations(Photo $photo, array $translations): void
    {
        if (empty($translations)) {
            return;
        }

        foreach ($translations as $locale => $translationData) {
            PhotoTranslation::updateOrCreate(
                [
                    'photo_id' => $photo->id,
                    'locale' => $locale,
                ],
                [
                    'alt_text' => $translationData['alt_text'] ?? null,
                ]
            );
        }
    }

    /**
     * Update photo sort order in bulk using a single SQL query.
     *
     * Optimized to use CASE WHEN statement instead of N individual UPDATE queries.
     * Example SQL generated:
     * UPDATE photos
     * SET sort_order = CASE
     *   WHEN id = 1 THEN 0
     *   WHEN id = 2 THEN 1
     *   WHEN id = 3 THEN 2
     * END
     * WHERE id IN (1, 2, 3) AND place_id = ?
     *
     * @param  Place  $place  The place that owns the photos
     * @param  array<int, int>  $photoOrderMap  Mapping of photo IDs to their new sort_order values
     */
    public function updatePhotoOrder(Place $place, array $photoOrderMap): void
    {
        if (empty($photoOrderMap)) {
            return;
        }

        // Build CASE WHEN clauses and collect bindings
        $caseClauses = [];
        $bindings = [];
        $photoIds = [];

        foreach ($photoOrderMap as $photoId => $order) {
            $caseClauses[] = 'WHEN id = ? THEN ?';
            $bindings[] = $photoId;
            $bindings[] = $order;
            $photoIds[] = $photoId;
        }

        // Build and execute the bulk UPDATE query
        $caseStatement = implode(' ', $caseClauses);
        $placeholders = implode(',', array_fill(0, count($photoIds), '?'));

        $sql = "UPDATE photos
                SET sort_order = CASE {$caseStatement} END
                WHERE id IN ({$placeholders}) AND place_id = ?";

        // Merge bindings: CASE bindings + photo IDs for IN clause + place_id for WHERE
        $allBindings = array_merge($bindings, $photoIds, [$place->id]);

        DB::update($sql, $allBindings);
    }

    public function setMainPhoto(Place $place, int $photoId): void
    {
        // Unset all main photos for this place
        Photo::where('place_id', $place->id)
            ->update(['is_main' => false]);

        // Set the selected photo as main
        Photo::where('id', $photoId)
            ->where('place_id', $place->id)
            ->update(['is_main' => true]);
    }

    public function deletePhoto(int $photoId): bool
    {
        $photo = Photo::find($photoId);

        if (! $photo) {
            return false;
        }

        return $photo->delete();
    }

    public function findPhotoById(int $photoId): ?Photo
    {
        return Photo::find($photoId);
    }
}
