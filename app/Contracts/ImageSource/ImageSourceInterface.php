<?php

namespace App\Contracts\ImageSource;

use Illuminate\Support\Collection;

interface ImageSourceInterface
{
    /**
     * Search for images related to a specific place.
     *
     * @return Collection<int, \App\DTO\ImageSource\ImageResultData>
     */
    public function searchImages(string $placeName, ?string $placeDescription = null, ?string $location = null): Collection;

    /**
     * Get the name of this image source.
     */
    public function sourceName(): string;
}
