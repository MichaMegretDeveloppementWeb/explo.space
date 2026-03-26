<?php

namespace App\Services\Admin\Autofill;

use App\Models\Place;

class DeduplicationService
{
    /**
     * Filter out places that already exist in the database.
     *
     * @param  array<int, array<string, mixed>>  $places  Each place must have 'name', 'latitude', 'longitude'.
     * @return array{unique: array<int, array<string, mixed>>, duplicateNames: array<int, string>}
     */
    public function filterDuplicates(array $places): array
    {
        $unique = [];
        $duplicateNames = [];

        foreach ($places as $place) {
            if ($this->checkSinglePlace($place)) {
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
     * Check if a single place already exists in the database.
     *
     * @param  array<string, mixed>  $place  Must have 'name', 'latitude', 'longitude'.
     * @return bool true if the place is a duplicate
     */
    public function checkSinglePlace(array $place): bool
    {
        $name = $place['name'] ?? '';
        $latitude = (float) ($place['latitude'] ?? 0);
        $longitude = (float) ($place['longitude'] ?? 0);

        if ($name === '' || ($latitude === 0.0 && $longitude === 0.0)) {
            return false;
        }

        $radiusMeters = (int) config('autofill.deduplication_radius', 500);
        $radiusKm = $radiusMeters / 1000;

        $nearbyPlaces = Place::query()
            ->withinRadius($latitude, $longitude, $radiusKm)
            ->with(['translations:id,place_id,title,locale'])
            ->get();

        if ($nearbyPlaces->isEmpty()) {
            return false;
        }

        $threshold = (int) config('autofill.deduplication_name_threshold', 60);

        foreach ($nearbyPlaces as $existingPlace) {
            foreach ($existingPlace->translations as $translation) {
                if ($this->namesMatch($name, $translation->title, $threshold)) {
                    return true;
                }
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
