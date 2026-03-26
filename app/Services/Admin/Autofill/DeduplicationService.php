<?php

namespace App\Services\Admin\Autofill;

use App\Models\Place;
use App\Models\PlaceTranslation;

class DeduplicationService
{
    /**
     * Filter out places that already exist in the database.
     *
     * Uses a coordinate-first strategy (no bulk load):
     * 1. Spatial query: check if a place exists within a configurable radius
     * 2. Targeted name search: SQL query for similar names (fallback when coordinates are off)
     *
     * @param  array<int, array<string, mixed>>  $places  Each place should have 'name' and optionally 'latitude', 'longitude'.
     * @return array{unique: array<int, array<string, mixed>>, duplicateNames: array<int, string>}
     */
    public function filterDuplicates(array $places): array
    {
        $unique = [];
        $duplicateNames = [];

        foreach ($places as $place) {
            if ($this->isDuplicate($place)) {
                $duplicateNames[] = $place['name'];
            } else {
                $unique[] = $place;
            }
        }

        return [
            'unique' => $unique,
            'duplicateNames' => $duplicateNames,
        ];
    }

    /**
     * Check if a place is a duplicate using coordinates-first, then targeted name search.
     *
     * @param  array<string, mixed>  $place
     */
    private function isDuplicate(array $place): bool
    {
        $name = $place['name'] ?? '';

        if ($name === '') {
            return false;
        }

        // Pass 1: Coordinate-based deduplication (spatial index, ~1ms per query)
        $latitude = (float) ($place['latitude'] ?? 0);
        $longitude = (float) ($place['longitude'] ?? 0);

        if ($latitude !== 0.0 || $longitude !== 0.0) {
            if ($this->hasNearbyPlace($latitude, $longitude)) {
                return true;
            }
        }

        // Pass 2: Targeted name search in DB (no bulk load)
        return $this->hasMatchingNameInDb($name);
    }

    /**
     * Check if any place exists near the given coordinates.
     *
     * Uses the spatial index on the places table for efficient lookups.
     * A place within the radius is considered a duplicate regardless of name,
     * since different names for the same physical location are common
     * (e.g., "Kennedy Space Center" and "KSC Visitor Complex").
     */
    private function hasNearbyPlace(float $latitude, float $longitude): bool
    {
        $radiusMeters = (int) config('autofill.deduplication_radius', 2000);
        $radiusKm = $radiusMeters / 1000;

        return Place::query()
            ->withinRadius($latitude, $longitude, $radiusKm)
            ->exists();
    }

    /**
     * Search for matching place names directly in the database.
     *
     * Instead of loading all titles into memory, this runs a targeted SQL query
     * using the collation (utf8mb4_general_ci) for case-insensitive matching,
     * then confirms with PHP-level normalized comparison on the small result set.
     */
    private function hasMatchingNameInDb(string $name): bool
    {
        if (mb_strlen($name) <= 3) {
            return false;
        }

        $threshold = (int) config('autofill.deduplication_name_threshold', 60);
        $lowerName = mb_strtolower($name);

        // SQL-level search: exact match or containment (case-insensitive via collation)
        $potentialMatches = PlaceTranslation::query()
            ->select('title')
            ->where(function ($query) use ($lowerName) {
                $query->whereRaw('LOWER(title) LIKE ?', ['%'.$lowerName.'%'])
                    ->orWhereRaw('? LIKE CONCAT(\'%\', LOWER(title), \'%\')', [$lowerName]);
            })
            ->limit(20)
            ->pluck('title');

        foreach ($potentialMatches as $existingTitle) {
            if ($this->namesMatch($name, $existingTitle, $threshold)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compare two place names using normalized similarity.
     */
    public function namesMatch(string $a, string $b, int $threshold = 60): bool
    {
        $normalizedA = $this->normalizeName($a);
        $normalizedB = $this->normalizeName($b);

        if ($normalizedA === $normalizedB) {
            return true;
        }

        // Check if one name contains the other (handles "Kennedy Space Center" vs "Kennedy Space Center Visitor Complex")
        if (mb_strlen($normalizedA) > 5 && mb_strlen($normalizedB) > 5) {
            if (str_contains($normalizedA, $normalizedB) || str_contains($normalizedB, $normalizedA)) {
                return true;
            }
        }

        $percent = 0;
        similar_text($normalizedA, $normalizedB, $percent);

        return $percent >= $threshold;
    }

    /**
     * Normalize a place name for comparison: lowercase, no accents, no punctuation.
     */
    public function normalizeName(string $name): string
    {
        $name = mb_strtolower($name);

        // Transliterate accents
        if (function_exists('transliterator_transliterate')) {
            $name = transliterator_transliterate('Any-Latin; Latin-ASCII', $name);
        }

        // Remove punctuation and extra whitespace
        $name = (string) preg_replace('/[^\p{L}\p{N}\s]/u', '', $name);
        $name = (string) preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }
}
