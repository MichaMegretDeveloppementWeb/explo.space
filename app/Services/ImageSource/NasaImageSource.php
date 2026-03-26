<?php

namespace App\Services\ImageSource;

use App\Contracts\ImageSource\ImageSourceInterface;
use App\DTO\ImageSource\ImageResultData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NasaImageSource implements ImageSourceInterface
{
    private const SEARCH_URL = 'https://images-api.nasa.gov/search';

    private const ASSET_URL = 'https://images-api.nasa.gov/asset';

    private const MAX_RESULTS = 5;

    public function searchImages(string $placeName, ?string $placeDescription = null, ?string $location = null): Collection
    {
        try {
            $response = Http::timeout(30)->get(self::SEARCH_URL, [
                'q' => $placeName,
                'media_type' => 'image',
                'page_size' => self::MAX_RESULTS,
            ]);

            if ($response->failed()) {
                Log::warning('[Autofill:Images] NASA HTTP failed', [
                    'status' => $response->status(),
                    'place' => $placeName,
                ]);

                return collect();
            }

            $items = $response->json('collection.items', []);
            $filtered = collect($items)->filter(fn (array $item) => $this->isValidItem($item));

            return $filtered
                ->map(fn (array $item) => $this->mapToResult($item))
                ->filter()
                ->values();
        } catch (\Throwable $e) {
            Log::warning('[Autofill:Images] NASA exception', [
                'place' => $placeName,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    public function sourceName(): string
    {
        return 'nasa';
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function isValidItem(array $item): bool
    {
        $data = $item['data'][0] ?? null;

        if (! $data || empty($data['nasa_id'])) {
            return false;
        }

        return ($data['media_type'] ?? '') === 'image';
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function mapToResult(array $item): ?ImageResultData
    {
        $data = $item['data'][0];
        $nasaId = $data['nasa_id'];

        $fullSizeUrl = $this->resolveFullSizeUrl($nasaId, $item);

        if (! $fullSizeUrl) {
            return null;
        }

        $description = $data['description'] ?? null;
        if ($description) {
            $description = strip_tags($description);
            $description = mb_substr($description, 0, 500);
        }

        return new ImageResultData(
            url: $fullSizeUrl,
            source: 'nasa',
            license: 'Public Domain',
            caption: $description,
            width: null,
            height: null,
        );
    }

    /**
     * Resolve a full-size image URL for a NASA item.
     *
     * Fetches the asset manifest to find the largest available image,
     * preferring ~large.jpg over ~medium.jpg over ~orig.jpg.
     * Falls back to the preview thumbnail if the manifest is unavailable.
     *
     * @param  array<string, mixed>  $item
     */
    private function resolveFullSizeUrl(string $nasaId, array $item): ?string
    {
        try {
            $response = Http::timeout(15)->get(self::ASSET_URL.'/'.urlencode($nasaId));

            if ($response->successful()) {
                $assets = $response->json('collection.items', []);
                $urls = array_column($assets, 'href');

                // Prefer large > medium > orig (orig can be very large files)
                foreach (['~large.jpg', '~medium.jpg', '~orig.jpg', '~large.png', '~medium.png'] as $suffix) {
                    foreach ($urls as $url) {
                        if (str_ends_with(strtolower($url), $suffix)) {
                            return $url;
                        }
                    }
                }

                // Fallback: any JPEG/PNG URL that is not a thumb or metadata
                foreach ($urls as $url) {
                    $lower = strtolower($url);
                    if (
                        (str_ends_with($lower, '.jpg') || str_ends_with($lower, '.png'))
                        && ! str_contains($lower, '~thumb')
                        && ! str_contains($lower, 'metadata')
                    ) {
                        return $url;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Asset manifest unavailable — fall back to preview
        }

        // Fallback to preview thumbnail from search results
        $links = $item['links'] ?? [];
        $imageLink = collect($links)->firstWhere('rel', 'preview');

        return $imageLink['href'] ?? null;
    }
}
