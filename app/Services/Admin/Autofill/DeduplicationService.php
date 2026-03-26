<?php

namespace App\Services\Admin\Autofill;

use App\Models\Place;
use App\Models\PlaceTranslation;

class DeduplicationService
{
    /**
     * Filter out places that already exist in the database.
     *
     * Uses a two-pass strategy:
     * 1. Name-based: compare against ALL place translations (language-independent)
     * 2. Coordinate-based: if coordinates are available, check within a radius
     *
     * @param  array<int, array<string, mixed>>  $places  Each place should have 'name' and optionally 'latitude', 'longitude'.
     * @return array{unique: array<int, array<string, mixed>>, duplicateNames: array<int, string>}
     */
    public function filterDuplicates(array $places): array
    {
        $unique = [];
        $duplicateNames = [];

        // Pre-load all existing place titles for efficient name comparison
        $existingTitles = PlaceTranslation::query()
            ->select('title')
            ->pluck('title')
            ->toArray();

        foreach ($places as $place) {
            if ($this->isDuplicate($place, $existingTitles)) {
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
     * Check if a place is a duplicate using name-first, then coordinates.
     *
     * @param  array<string, mixed>  $place
     * @param  array<int, string>  $existingTitles  All existing place titles from DB
     */
    private function isDuplicate(array $place, array $existingTitles): bool
    {
        $name = $place['name'] ?? '';

        if ($name === '') {
            return false;
        }

        $nameThreshold = (int) config('autofill.deduplication_name_threshold', 60);

        // Pass 1: Name-based deduplication against ALL place translations
        foreach ($existingTitles as $existingTitle) {
            if ($this->namesMatch($name, $existingTitle, $nameThreshold)) {
                return true;
            }
        }

        // Pass 2: Coordinate-based deduplication (catches renamed/translated places nearby)
        $latitude = (float) ($place['latitude'] ?? 0);
        $longitude = (float) ($place['longitude'] ?? 0);

        if ($latitude === 0.0 && $longitude === 0.0) {
            return false;
        }

        return $this->hasNearbyPlace($latitude, $longitude);
    }

    /**
     * Check if any place exists near the given coordinates.
     *
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
