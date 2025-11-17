<?php

namespace App\Contracts\Repositories\Admin\Place\Create;

use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceRequest;

interface PlaceCreateRepositoryInterface
{
    /**
     * Create a new place with base data.
     *
     * @param  array{latitude: float, longitude: float, address: ?string, admin_id: int, is_featured?: bool, request_id?: ?int}  $placeData
     */
    public function create(array $placeData): Place;

    /**
     * Create translations for a place.
     *
     * @param  array<string, array{title: string, slug: string, description: string, practical_info: ?string, status: string}>  $translations
     */
    public function createTranslations(Place $place, array $translations): void;

    /**
     * Attach categories to a place.
     *
     * @param  array<int>  $categoryIds
     */
    public function attachCategories(Place $place, array $categoryIds): void;

    /**
     * Attach tags to a place.
     *
     * @param  array<int>  $tagIds
     */
    public function attachTags(Place $place, array $tagIds): void;

    /**
     * Create photos for a place.
     *
     * @param  array<int, array{filename: string, original_name: string, mime_type: string, size: int, is_main: bool, sort_order: int}>  $photoData
     */
    public function createPhotos(Place $place, array $photoData): void;

    /**
     * Create a single photo and return it (for mapping).
     *
     * @param  array{filename: string, original_name: string, mime_type: string, size: int, is_main: bool, sort_order: int}  $photoData
     */
    public function createPhoto(Place $place, array $photoData): Photo;

    /**
     * Create photo translations.
     *
     * @param  array<string, array{alt_text: ?string}>  $translations
     */
    public function createPhotoTranslations(Photo $photo, array $translations): void;

    /**
     * Mark a PlaceRequest as accepted and link it to the created place.
     */
    public function markPlaceRequestAsAccepted(PlaceRequest $placeRequest, int $adminId): void;

    /**
     * Find a PlaceRequest by ID.
     */
    public function findPlaceRequestById(int $id): ?PlaceRequest;
}
