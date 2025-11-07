<?php

namespace App\Contracts\Repositories\Web\Place\Index;

use Illuminate\Support\Collection;

/**
 * Repository interface for Place exploration/discovery functionality
 */
interface PlaceExplorationRepositoryInterface
{
    /**
     * Get coordinates only (id, lat, lng, is_featured) within bounding box
     * Used by PlaceMap component for marker display
     *
     * Critical: Uses bounding box to limit query size for 100K+ places
     *
     * @param  array<string, mixed>  $filters  Applied filters (mode, tags, radius, etc.)
     * @param  array<string, float|null>|null  $boundingBox  ['north' => float, 'south' => float, 'east' => float, 'west' => float]
     * @return Collection<int, array{id: int, latitude: float, longitude: float, is_featured: bool}>
     */
    public function getPlacesCoordinates(array $filters, ?array $boundingBox = null): Collection;

    /**
     * Get places within bounding box as arrays (for Livewire infinite scroll)
     * Transforms Eloquent models to arrays to avoid Livewire serialization issues.
     *
     * Returns structured arrays with:
     * - Place data (id, coordinates, address)
     * - Translation (title, description, slug)
     * - Main photo only (thumb_url, url)
     * - Tags with translations
     *
     * Uses CURSOR pagination for optimal infinite scroll performance.
     *
     * @param  array<string, mixed>  $filters  Applied filters (mode, tags, radius, etc.)
     * @param  array<string, float|null>  $boundingBox  ['north' => float, 'south' => float, 'east' => float, 'west' => float]
     * @param  string  $locale  Current locale for translations
     * @param  int  $perPage  Number of items per page (default: 20)
     * @param  string|null  $cursor  Encoded cursor for pagination (null for first page)
     * @return array{places: array<int, array<string, mixed>>, nextCursor: string|null, hasMorePages: bool}
     */
    public function getPlacesInBoundingBoxAsArrays(
        array $filters,
        array $boundingBox,
        string $locale,
        int $perPage = 20,
        ?string $cursor = null
    ): array;
}
