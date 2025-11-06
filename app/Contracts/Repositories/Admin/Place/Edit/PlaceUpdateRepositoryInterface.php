<?php

namespace App\Contracts\Repositories\Admin\Place\Edit;

use App\Models\Photo;
use App\Models\Place;

interface PlaceUpdateRepositoryInterface
{
    /**
     * Find a place by ID with all necessary relations for editing.
     */
    public function findForEdit(int $id): ?Place;

    /**
     * Update place base data.
     *
     * @param  array{latitude?: float, longitude?: float, address?: ?string, is_featured?: bool}  $placeData
     */
    public function update(Place $place, array $placeData): bool;

    /**
     * Update translations for a place.
     *
     * @param  array<string, array{title: string, slug: string, description: string, practical_info: ?string, status: string}>  $translations
     */
    public function updateTranslations(Place $place, array $translations): void;

    /**
     * Sync categories for a place.
     *
     * @param  array<int>  $categoryIds
     */
    public function syncCategories(Place $place, array $categoryIds): void;

    /**
     * Sync tags for a place.
     *
     * @param  array<int>  $tagIds
     */
    public function syncTags(Place $place, array $tagIds): void;

    /**
     * Add photos to a place.
     *
     * @param  array<int, array{filename: string, original_name: string, mime_type: string, size: int, alt_text: ?string, is_main: bool, sort_order: int}>  $photoData
     */
    public function addPhotos(Place $place, array $photoData): void;

    /**
     * Update photo order for a place.
     *
     * @param  array<int, int>  $photoOrderMap  Map of photo_id => order
     */
    public function updatePhotoOrder(Place $place, array $photoOrderMap): void;

    /**
     * Set main photo for a place.
     */
    public function setMainPhoto(Place $place, int $photoId): void;

    /**
     * Delete a photo.
     */
    public function deletePhoto(int $photoId): bool;

    /**
     * Find a photo by ID.
     */
    public function findPhotoById(int $photoId): ?Photo;
}
