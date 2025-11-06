<?php

namespace App\Repositories\Web\Place\PlaceRequest;

use App\Contracts\Repositories\Web\Place\PlaceRequest\PlaceRequestCreateRepositoryInterface;
use App\Models\PlaceRequest;
use App\Models\PlaceRequestPhoto;

class PlaceRequestCreateRepository implements PlaceRequestCreateRepositoryInterface
{
    /**
     * Créer une nouvelle demande de lieu
     *
     * @param  array{title: string, slug: string, description: ?string, practical_info: ?string, latitude: ?float, longitude: ?float, address: ?string, contact_email: string, detected_language: string, status: string}  $data
     */
    public function create(array $data): PlaceRequest
    {
        return PlaceRequest::create($data);
    }

    /**
     * Créer une photo pour une demande de lieu
     *
     * @param  array{filename: string, original_name: string, mime_type: string, size: int, sort_order: int}  $photoData
     */
    public function createPhoto(PlaceRequest $placeRequest, array $photoData): PlaceRequestPhoto
    {
        return PlaceRequestPhoto::create([
            'place_request_id' => $placeRequest->id,
            'filename' => $photoData['filename'],
            'original_name' => $photoData['original_name'],
            'mime_type' => $photoData['mime_type'],
            'size' => $photoData['size'],
            'sort_order' => $photoData['sort_order'],
        ]);
    }
}
