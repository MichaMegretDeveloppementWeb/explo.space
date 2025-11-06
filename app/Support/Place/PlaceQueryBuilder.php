<?php

namespace App\Support\Place;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Centralized query builder for Place filtering logic
 *
 * Responsible for applying all common filtering rules to Place queries.
 * Used by both PlaceList (paginated) and PlaceMap (coordinates only).
 */
class PlaceQueryBuilder
{
    /**
     * Apply all filters to the given query
     *
     * @param  Builder<\App\Models\Place>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<\App\Models\Place>
     */
    public static function applyFilters(Builder $query, array $filters): Builder
    {
        $mode = $filters['mode'] ?? 'proximity';

        if ($mode === 'proximity') {
            self::applyProximityMode($query, $filters);
        } elseif ($mode === 'worldwide') {
            self::applyWorldwideMode($query, $filters);
        }

        return $query;
    }

    /**
     * Apply proximity mode filters (center point + radius)
     *
     * Uses MySQL spatial function ST_Distance_Sphere() with spatial index
     * Much faster than Haversine formula for large datasets (100K+ places)
     *
     * @param  Builder<\App\Models\Place>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<\App\Models\Place>
     */
    private static function applyProximityMode(Builder $query, array $filters): Builder
    {
        $latitude = $filters['latitude'] ?? null;
        $longitude = $filters['longitude'] ?? null;
        $radius = $filters['radius'] ?? 200000; // Default 200km in meters

        // Si pas de coordonnées, retourner aucun résultat
        if (empty($latitude) || empty($longitude)) {
            return $query->whereRaw('1 = 0');
        }

        // Check if columns are already selected (for coordinate-only queries)
        $hasExistingSelect = ! empty($query->getQuery()->columns);

        // Utiliser ST_Distance_Sphere avec index spatial (optimal pour MySQL 5.7+)
        // POINT(longitude, latitude) - attention à l'ordre !
        if ($hasExistingSelect) {
            // Add distance column to existing selection
            $query->addSelect(DB::raw('ST_Distance_Sphere(
                coordinates,
                POINT(?, ?)
            ) AS distance'))
                ->addBinding([$longitude, $latitude], 'select');
        } else {
            // Select all columns + distance
            $query->selectRaw(
                '*, ST_Distance_Sphere(
                    coordinates,
                    POINT(?, ?)
                ) AS distance',
                [$longitude, $latitude]
            );
        }

        $query->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance', 'asc');

        // Appliquer filtres tags optionnels
        if (! empty($filters['tags'])) {
            self::applyTagsFilter($query, $filters['tags']);
        }

        return $query;
    }

    /**
     * Apply worldwide mode filters (tags required)
     *
     * @param  Builder<\App\Models\Place>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<\App\Models\Place>
     */
    private static function applyWorldwideMode(Builder $query, array $filters): Builder
    {
        $tags = $filters['tags'] ?? [];

        // En mode worldwide, au moins un tag requis
        if (empty($tags)) {
            return $query->whereRaw('1 = 0');
        }

        self::applyTagsFilter($query, $tags);

        // Tri par date de création (plus récents en premier)
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Apply bounding box filter (for map viewport limitation)
     *
     * Uses MBRContains() with spatial index for optimal performance
     * Critical for 100K+ places - leverages R-tree index
     *
     * @param  Builder<\App\Models\Place>  $query
     * @param  array<string, float|null>  $boundingBox  ['north' => float, 'south' => float, 'east' => float, 'west' => float]
     * @return Builder<\App\Models\Place>
     */
    public static function applyBoundingBox(Builder $query, array $boundingBox): Builder
    {
        $north = $boundingBox['north'] ?? null;
        $south = $boundingBox['south'] ?? null;
        $east = $boundingBox['east'] ?? null;
        $west = $boundingBox['west'] ?? null;

        if ($north === null || $south === null || $east === null || $west === null) {
            return $query;
        }

        // Utiliser MBRContains avec POLYGON pour bénéficier de l'index spatial
        // Format: POLYGON((west south, east south, east north, west north, west south))
        $query->whereRaw(
            'MBRContains(
                ST_GeomFromText(?),
                coordinates
            )',
            ["POLYGON(({$west} {$south}, {$east} {$south}, {$east} {$north}, {$west} {$north}, {$west} {$south}))"]
        );

        return $query;
    }

    /**
     * Apply tags filter (many-to-many relationship)
     *
     * @param  Builder<\App\Models\Place>  $query
     * @param  array<int, int|string>  $tags
     * @return Builder<\App\Models\Place>
     */
    private static function applyTagsFilter(Builder $query, array $tags): Builder
    {
        if (empty($tags)) {
            return $query;
        }

        // Normaliser les tags (peuvent être IDs ou slugs)
        $tagIds = [];
        $tagSlugs = [];

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                $tagIds[] = (int) $tag;
            } else {
                $tagSlugs[] = $tag;
            }
        }

        // Filtrer par IDs ou slugs
        $query->where(function ($q) use ($tagIds, $tagSlugs) {
            if (! empty($tagIds)) {
                $q->orWhereHas('tags', function ($tagQuery) use ($tagIds) {
                    $tagQuery->whereIn('tags.id', $tagIds);
                });
            }

            if (! empty($tagSlugs)) {
                $q->orWhereHas('tags.translations', function ($tagTranslationQuery) use ($tagSlugs) {
                    $tagTranslationQuery->whereIn('slug', $tagSlugs)
                        ->where('locale', app()->getLocale());
                });
            }
        });

        return $query;
    }
}
