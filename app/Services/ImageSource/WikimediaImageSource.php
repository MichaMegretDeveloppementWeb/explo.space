<?php

namespace App\Services\ImageSource;

use App\Contracts\ImageSource\ImageSourceInterface;
use App\DTO\ImageSource\ImageResultData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WikimediaImageSource implements ImageSourceInterface
{
    private const API_URL = 'https://commons.wikimedia.org/w/api.php';

    private const MAX_RESULTS = 5;

    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function searchImages(string $placeName, ?string $placeDescription = null, ?string $location = null): Collection
    {
        try {
            $results = $this->executeSearch($placeName);

            // If few results and we have an alternative name (original, often in another language), retry
            if ($results->count() < 3 && $placeDescription !== null && $placeDescription !== $placeName) {
                $secondaryResults = $this->executeSearch($placeDescription);

                // Merge and deduplicate by URL
                $results = $results->merge($secondaryResults)
                    ->unique(fn (ImageResultData $r) => $r->url)
                    ->values();
            }

            return $results;
        } catch (\Throwable $e) {
            Log::warning('[Autofill:Images] Wikimedia exception', [
                'place' => $placeName,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Execute a single Wikimedia Commons API search.
     *
     * @return Collection<int, ImageResultData>
     */
    private function executeSearch(string $query): Collection
    {
        $response = Http::timeout(30)->withHeaders([
            'User-Agent' => config('autofill.image_sources.wikimedia_user_agent'),
        ])->get(self::API_URL, [
            'action' => 'query',
            'generator' => 'search',
            'gsrsearch' => $query,
            'gsrnamespace' => 6,
            'gsrlimit' => self::MAX_RESULTS,
            'prop' => 'imageinfo',
            'iiprop' => 'url|extmetadata|size|mime',
            'iiurlwidth' => 1200,
            'format' => 'json',
        ]);

        if ($response->failed()) {
            Log::warning('[Autofill:Images] Wikimedia HTTP failed', [
                'status' => $response->status(),
                'query' => $query,
            ]);

            return collect();
        }

        $pages = $response->json('query.pages', []);
        $filtered = collect($pages)->filter(fn (array $page) => $this->isValidImage($page));

        return $filtered
            ->map(fn (array $page) => $this->mapToResult($page))
            ->values();
    }

    public function sourceName(): string
    {
        return 'wikimedia';
    }

    private function isValidImage(array $page): bool
    {
        $imageInfo = $page['imageinfo'][0] ?? null;

        if (! $imageInfo) {
            return false;
        }

        $url = $imageInfo['url'] ?? '';
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return false;
        }

        $width = $imageInfo['width'] ?? 0;
        $height = $imageInfo['height'] ?? 0;

        if ($width < 200 || $height < 200) {
            return false;
        }

        $title = strtolower($page['title'] ?? '');
        if (str_contains($title, 'logo') || str_contains($title, 'icon') || str_contains($title, 'flag')) {
            return false;
        }

        return true;
    }

    private function mapToResult(array $page): ImageResultData
    {
        $imageInfo = $page['imageinfo'][0];
        $metadata = $imageInfo['extmetadata'] ?? [];

        $caption = $metadata['ImageDescription']['value'] ?? null;
        if ($caption) {
            $caption = strip_tags($caption);
            $caption = mb_substr($caption, 0, 500);
        }

        $license = $metadata['LicenseShortName']['value']
            ?? $metadata['License']['value']
            ?? 'Unknown';

        return new ImageResultData(
            url: $imageInfo['thumburl'] ?? $imageInfo['url'],
            source: 'wikimedia',
            license: $license,
            caption: $caption,
            width: $imageInfo['width'] ?? null,
            height: $imageInfo['height'] ?? null,
        );
    }
}
