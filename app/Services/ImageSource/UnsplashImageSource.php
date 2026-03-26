<?php

namespace App\Services\ImageSource;

use App\Contracts\ImageSource\ImageSourceInterface;
use App\DTO\ImageSource\ImageResultData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnsplashImageSource implements ImageSourceInterface
{
    private const API_URL = 'https://api.unsplash.com/search/photos';

    private const MAX_RESULTS = 5;

    public function searchImages(string $placeName, ?string $placeDescription = null, ?string $location = null): Collection
    {
        $apiKey = config('autofill.image_sources.unsplash_key');

        if (! $apiKey) {
            return collect();
        }

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Client-ID '.$apiKey,
            ])->get(self::API_URL, [
                'query' => $placeName,
                'per_page' => self::MAX_RESULTS,
                'orientation' => 'landscape',
            ]);

            if ($response->failed()) {
                Log::warning('[Autofill:Images] Unsplash HTTP failed', [
                    'status' => $response->status(),
                    'place' => $placeName,
                ]);

                return collect();
            }

            $results = $response->json('results', []);
            $filtered = collect($results)->filter(fn (array $photo) => $this->isRelevant($photo, $placeName));

            return $filtered
                ->map(fn (array $photo) => $this->mapToResult($photo))
                ->values();
        } catch (\Throwable $e) {
            Log::warning('[Autofill:Images] Unsplash exception', [
                'place' => $placeName,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    public function sourceName(): string
    {
        return 'unsplash';
    }

    private function isRelevant(array $photo, string $placeName): bool
    {
        $searchTerms = array_filter(explode(' ', strtolower($placeName)));
        $description = strtolower($photo['description'] ?? '');
        $altDescription = strtolower($photo['alt_description'] ?? '');
        $tags = collect($photo['tags'] ?? [])->pluck('title')->map(fn ($t) => strtolower($t))->join(' ');

        $haystack = $description.' '.$altDescription.' '.$tags;

        $matchCount = 0;
        foreach ($searchTerms as $term) {
            if (mb_strlen($term) >= 3 && str_contains($haystack, $term)) {
                $matchCount++;
            }
        }

        return $matchCount >= 1;
    }

    private function mapToResult(array $photo): ImageResultData
    {
        $caption = $photo['description'] ?? $photo['alt_description'] ?? null;

        return new ImageResultData(
            url: $photo['urls']['regular'] ?? $photo['urls']['full'],
            source: 'unsplash',
            license: 'Unsplash License',
            caption: $caption,
            width: $photo['width'] ?? null,
            height: $photo['height'] ?? null,
        );
    }
}
