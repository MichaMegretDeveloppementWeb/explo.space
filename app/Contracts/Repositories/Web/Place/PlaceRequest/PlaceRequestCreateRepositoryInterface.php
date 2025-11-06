<?php

namespace App\Contracts\Repositories\Web\Place\PlaceRequest;

use App\Models\PlaceRequest;
use App\Models\PlaceRequestPhoto;

interface PlaceRequestCreateRepositoryInterface
{
    /**
     * Créer une nouvelle demande de lieu
     *
     * @param  array{title: string, slug: string, description: ?string, practical_info: ?string, latitude: ?float, longitude: ?float, address: ?string, contact_email: string, detected_language: string, status: string}  $data
     */
    public function create(array $data): PlaceRequest;

    /**
     * Créer une photo pour une demande de lieu
     *
     * @param  array{filename: string, original_name: string, mime_type: string, size: int, sort_order: int}  $photoData
     */
    public function createPhoto(PlaceRequest $placeRequest, array $photoData): PlaceRequestPhoto;
}
