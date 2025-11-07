<?php

namespace App\Repositories\Web\Place\Index;

use App\Contracts\Repositories\Web\Place\Index\PlaceExplorationRepositoryInterface;
use App\Models\Place;
use App\Support\Config\PlaceSearchConfig;
use App\Support\Place\PlaceQueryBuilder;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Repository for Place exploration queries
 *
 * Handles two distinct query types:
 * - Detailed array queries for PlaceList (NO cache - viewport changes frequently)
 * - Lightweight coordinate queries for PlaceMap (lightweight data)
 *
 * Cache Strategy:
 * - Map coordinates: lightweight data (id, lat, lng only)
 * - Detailed list: NOT cached (viewport changes frequently with zoom/pan)
 *
 * Rationale for selective caching:
 * - Database queries are already optimized (indexes, eager loading, cursor pagination)
 * - Users frequently change filters/viewport (low cache hit rate for list)
 */
class PlaceExplorationRepository implements PlaceExplorationRepositoryInterface
{
    /**
     * Get coordinates only within bounding box (for map markers)
     *
     * Returns array of simple coordinate arrays for efficient caching and direct JSON serialization.
     * No Eloquent overhead - perfect for map display where we only need id, lat, lng.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<string, float|null>|null  $boundingBox
     * @return Collection<int, array{id: int, latitude: float, longitude: float, is_featured: bool}>
     */
    public function getPlacesCoordinates(array $filters, ?array $boundingBox = null): Collection
    {

        $query = Place::select('id', 'latitude', 'longitude', 'is_featured');

        // Apply all filters (mode, tags, proximity, etc.)
        PlaceQueryBuilder::applyFilters($query, $filters);

        // Critical: Apply bounding box to limit result set
        if ($boundingBox !== null) {
            PlaceQueryBuilder::applyBoundingBox($query, $boundingBox);
        }

        // Limite de sécurité pour éviter crash serveur (frontend clustering gérera l'affichage)
        $query->limit(PlaceSearchConfig::MAX_MAP_COORDINATES);

        // Transform to simple arrays BEFORE caching to avoid serializing heavy Eloquent objects
        // Cache size: 86 bytes/place instead of 1444 bytes/place (94% reduction)
        return $query->get()->map(fn ($place) => [
            'id' => $place->id,
            'latitude' => (float) $place->latitude,
            'longitude' => (float) $place->longitude,
            'is_featured' => (bool) $place->is_featured,
        ]);
    }

    /**
     * Get places within bounding box as arrays (for Livewire infinite scroll)
     *
     * Transforms Eloquent models to arrays to avoid Livewire serialization issues.
     *
     * NO CACHE: Viewport changes frequently with zoom/pan.
     *
     * @param  array<string, mixed>  $filters  Normalized filters
     * @param  array<string, float|null>  $boundingBox  ['north', 'south', 'east', 'west']
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
    ): array {
        $hasTagFilters = ! empty($filters['tags']);
        $mode = $filters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT;

        // Start with Place and apply filters
        $query = Place::query();

        // Apply all filters (mode, tags, proximity, etc.)
        PlaceQueryBuilder::applyFilters($query, $filters);

        // CRITICAL: Apply bounding box to limit result set to visible viewport
        PlaceQueryBuilder::applyBoundingBox($query, $boundingBox);

        // Eager load relations
        $query->with([
            'translations' => function ($q) use ($locale) {
                $q->where('locale', $locale)->where('status', 'published');
            },
            'photos' => function ($q) {
                $q->where('is_main', true)->limit(1);
            },
        ]);

        // Charger les tags selon le mode d'exploration
        if ($mode === 'worldwide') {
            // Mode worldwide : charger UNIQUEMENT les tags sélectionnés si filtres présents
            if ($hasTagFilters) {
                $query->with([
                    'tags' => function ($q) use ($filters, $locale) {
                        $q->whereHas('translations', function ($tq) use ($filters, $locale) {
                            $tq->whereIn('slug', $filters['tags'])
                                ->where('locale', $locale);
                        });
                    },
                    'tags.translations' => function ($q) use ($locale) {
                        $q->where('locale', $locale);
                    },
                ]);
            } else {
                // Pas de filtres tags (cas featured seul) : charger TOUS les tags pour éviter N+1
                $query->with([
                    'tags.translations' => function ($q) use ($locale) {
                        $q->where('locale', $locale);
                    },
                ]);
            }
        } else {
            // Mode proximity : toujours charger TOUS les tags
            $query->with([
                'tags.translations' => function ($q) use ($locale) {
                    $q->where('locale', $locale);
                },
            ]);
        }

        // Filter only places that have published translation in current locale
        $query->whereHas('translations', function ($q) use ($locale) {
            $q->where('locale', $locale)->where('status', 'published');
        });

        // Order by ID DESC for stable cursor pagination
        $query->orderBy('id', 'DESC');

        // Get cursor paginated results
        $paginator = $query->cursorPaginate(
            perPage: $perPage,
            cursor: $cursor
        );

        // Transform Eloquent models to arrays (avoid Livewire serialization issues)
        $placesArray = collect($paginator->items())->map(function ($place) {
            // Photo principale (une seule)
            $mainPhoto = $place->photos->first();

            return [
                'id' => $place->id,
                'latitude' => (float) $place->latitude,
                'longitude' => (float) $place->longitude,
                'address' => $place->address,
                'is_featured' => (bool) $place->is_featured,
                'distance' => isset($place->distance) ? (float) $place->distance : null,

                // Translation (première de la locale)
                'translation' => [
                    'title' => $place->translations->first()->title ?? '',
                    'description' => $place->translations->first()->description ?? '',
                    'slug' => $place->translations->first()->slug ?? '',
                ],

                // Photo principale uniquement (null si aucune)
                'main_photo' => $mainPhoto ? [
                    'thumb_url' => $mainPhoto->thumb_url,
                    'url' => $mainPhoto->url,
                ] : null,

                // Tags (tableau)
                'tags' => $place->tags->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->translations->first()->name ?? '',
                    'slug' => $tag->translations->first()->slug ?? '',
                ])->toArray(),
            ];
        })->toArray();

        return [
            'places' => $placesArray,
            'nextCursor' => $paginator->nextCursor()?->encode(),
            'hasMorePages' => $paginator->hasMorePages(),
        ];
    }
}
