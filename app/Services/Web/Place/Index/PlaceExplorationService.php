<?php

namespace App\Services\Web\Place\Index;

use App\Contracts\Repositories\Web\Place\Index\PlaceExplorationRepositoryInterface;
use App\Support\Config\PlaceSearchConfig;

/**
 * Service for Place exploration/discovery
 *
 * Orchestrates data retrieval for Place exploration.
 * Cache management is handled by the Repository layer.
 *
 * Provides two distinct data retrieval methods:
 * - getPlacesForList(): Paginated detailed data for PlaceList component
 * - getPlacesForMap(): Lightweight coordinates for PlaceMap component
 */
class PlaceExplorationService
{
    public function __construct(
        private PlaceExplorationRepositoryInterface $repository
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, float|null>|null  $boundingBox
     * @return array{coordinates: \Illuminate\Support\Collection<int, array{id: int, latitude: float, longitude: float}>, count: int, bounding_box: array<string, float|null>|null}
     */
    public function getPlacesForMap(array $filters, ?array $boundingBox = null): array
    {
        try {
            // Normalize filters
            $normalizedFilters = $this->normalizeFilters($filters);

            // Get coordinates (cache handled by Repository)
            $coordinates = $this->repository->getPlacesCoordinates($normalizedFilters, $boundingBox);

            return [
                'coordinates' => $coordinates,
                'count' => $coordinates->count(),
                'bounding_box' => $boundingBox,
            ];
        } catch (\Exception $e) {
            // Wrapper l'erreur technique dans une RuntimeException pour clarifier
            throw new \RuntimeException(
                'Error retrieving coordinates for map: '.$e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get places within bounding box as arrays (for Livewire infinite scroll)
     *
     * Returns structured arrays instead of Eloquent models to avoid Livewire serialization issues.
     * Optimal for infinite scroll where data accumulates across multiple requests.
     *
     * @param  array<string, mixed>  $filters  Search filters
     * @param  array<string, float|null>  $boundingBox  ['north', 'south', 'east', 'west']
     * @param  int  $perPage  Number of items per page (default: 20)
     * @param  string|null  $cursor  Encoded cursor for pagination (null for first page)
     * @return array{places: array<int, array<string, mixed>>, nextCursor: string|null, hasMorePages: bool}
     */
    public function getPlacesForList(
        array $filters,
        array $boundingBox,
        int $perPage = 20,
        ?string $cursor = null
    ): array {
        try {
            // Normalize filters
            $normalizedFilters = $this->normalizeFilters($filters);

            // Get current locale for translations
            $locale = app()->getLocale();

            // Get places as arrays from repository
            return $this->repository->getPlacesInBoundingBoxAsArrays(
                $normalizedFilters,
                $boundingBox,
                $locale,
                $perPage,
                $cursor
            );
        } catch (\Exception $e) {
            // Wrapper l'erreur technique dans une RuntimeException pour clarifier
            throw new \RuntimeException(
                'Error retrieving places as arrays in bounding box: '.$e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Normalize filters to consistent format
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function normalizeFilters(array $filters): array
    {
        return [
            'mode' => $filters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
            'latitude' => isset($filters['latitude']) ? (float) $filters['latitude'] : null,
            'longitude' => isset($filters['longitude']) ? (float) $filters['longitude'] : null,
            'radius' => isset($filters['radius']) ? (int) $filters['radius'] : PlaceSearchConfig::RADIUS_DEFAULT,
            'tags' => $this->parseTags($filters['tags'] ?? []),
        ];
    }

    /**
     * Parse tags from string or array format
     *
     * @return array<int, string>
     */
    private function parseTags(mixed $tags): array
    {
        if (is_string($tags)) {
            return array_filter(
                array_map('trim', explode(',', $tags)),
                fn ($tag) => ! empty($tag)
            );
        }

        if (is_array($tags)) {
            return array_filter($tags, fn ($tag) => ! empty($tag));
        }

        return [];
    }
}
