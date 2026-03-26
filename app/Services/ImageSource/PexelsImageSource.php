<?php

namespace App\Services\ImageSource;

use App\Contracts\ImageSource\ImageSourceInterface;
use App\DTO\ImageSource\ImageResultData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PexelsImageSource implements ImageSourceInterface
{
    private const API_URL = 'https://api.pexels.com/v1/search';

    private const MAX_RESULTS = 5;

    public function searchImages(string $placeName, ?string $placeDescription = null, ?string $location = null): Collection
    {
        $apiKey = config('autofill.image_sources.pexels_key');

        if (! $apiKey) {
            return collect();
        }

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => $apiKey,
            ])->get(self::API_URL, [
                'query' => $placeName,
                'per_page' => self::MAX_RESULTS,
                'orientation' => 'landscape',
            ]);

            if ($response->failed()) {
                Log::warning('[Autofill:Images] Pexels HTTP failed', [
                    'status' => $response->status(),
                    'place' => $placeName,
                ]);

                return collect();
            }

            $photos = $response->json('photos', []);

            return collect($photos)
                ->map(fn (array $photo) => $this->mapToResult($photo))
                ->values();
        } catch (\Throwable $e) {
            Log::warning('[Autofill:Images] Pexels exception', [
                'place' => $placeName,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    public function sourceName(): string
    {
        return 'pexels';
    }

    private function mapToResult(array $photo): ImageResultData
    {
        return new ImageResultData(
            url: $photo['src']['large'] ?? $photo['src']['original'],
            source: 'pexels',
            license: 'Pexels License',
            caption: $photo['alt'] ?? null,
            width: $photo['width'] ?? null,
            height: $photo['height'] ?? null,
        );
    }
}
