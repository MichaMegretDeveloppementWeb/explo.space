<?php

namespace App\Services\ImageSource;

use App\Contracts\ImageSource\ImageSourceInterface;
use App\DTO\ImageSource\ImageResultData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageSearchService
{
    /** @var list<ImageSourceInterface> */
    private array $primarySources;

    /** @var list<ImageSourceInterface> */
    private array $fallbackSources;

    public function __construct(
        WikimediaImageSource $wikimedia,
        NasaImageSource $nasa,
        UnsplashImageSource $unsplash,
        PexelsImageSource $pexels,
    ) {
        $this->primarySources = [$wikimedia, $nasa];
        $this->fallbackSources = [$unsplash, $pexels];
    }

    /**
     * Search for images across all configured sources.
     *
     * @return Collection<int, ImageResultData>
     */
    public function search(string $placeName, ?string $placeDescription = null, ?string $location = null): Collection
    {
        $maxImages = (int) config('autofill.images.max_per_place', 5);

        $results = $this->searchPrimarySources($placeName, $placeDescription, $location);

        if ($results->isEmpty()) {
            $results = $this->searchFallbackSources($placeName, $placeDescription, $location);
        }

        $final = $this->deduplicateByUrl($results)->take($maxImages)->values();

        return $final;
    }

    /**
     * Search and download images to temporary storage.
     *
     * Falls back to secondary sources if all primary downloads fail.
     *
     * @return Collection<int, array{path: string, result: ImageResultData}>
     */
    public function searchAndDownload(string $placeName, int $workflowId, int $itemId, ?string $placeDescription = null, ?string $location = null): Collection
    {
        $maxImages = (int) config('autofill.images.max_per_place', 5);

        // 1. Search primary sources
        $primaryResults = $this->searchPrimarySources($placeName, $placeDescription, $location);
        $primaryResults = $this->deduplicateByUrl($primaryResults)->take($maxImages)->values();

        // 2. Download primary results
        $downloaded = $primaryResults->map(
            fn (ImageResultData $result, int $index) => $this->downloadImage($result, $workflowId, $itemId, $index)
        )->filter()->values();

        // 3. If no downloads succeeded (empty search OR all downloads failed), try fallback
        if ($downloaded->isEmpty()) {
            $fallbackResults = $this->searchFallbackSources($placeName, $placeDescription, $location);
            $fallbackResults = $this->deduplicateByUrl($fallbackResults)->take($maxImages)->values();

            $downloaded = $fallbackResults->map(
                fn (ImageResultData $result, int $index) => $this->downloadImage($result, $workflowId, $itemId, $index)
            )->filter()->values();
        }

        return $downloaded;
    }

    /**
     * Search primary sources and interleave results so every source gets fair representation.
     *
     * @return Collection<int, ImageResultData>
     */
    private function searchPrimarySources(string $placeName, ?string $placeDescription, ?string $location): Collection
    {
        /** @var list<Collection<int, ImageResultData>> */
        $sourceResults = [];

        foreach ($this->primarySources as $source) {
            $results = $source->searchImages($placeName, $placeDescription, $location);
            if ($results->isNotEmpty()) {
                $sourceResults[] = $results->values();
            }
        }

        if (count($sourceResults) === 0) {
            return collect();
        }

        if (count($sourceResults) === 1) {
            return $sourceResults[0];
        }

        // Interleave results: take from each source in round-robin order
        $interleaved = collect();
        $maxLength = max(array_map(fn ($c) => $c->count(), $sourceResults));

        for ($i = 0; $i < $maxLength; $i++) {
            foreach ($sourceResults as $collection) {
                if ($i < $collection->count()) {
                    $interleaved->push($collection[$i]);
                }
            }
        }

        return $interleaved;
    }

    /**
     * @return Collection<int, ImageResultData>
     */
    private function searchFallbackSources(string $placeName, ?string $placeDescription, ?string $location): Collection
    {
        $results = collect();

        foreach ($this->fallbackSources as $source) {
            $sourceResults = $source->searchImages($placeName, $placeDescription, $location);
            $results = $results->merge($sourceResults);

            if ($results->isNotEmpty()) {
                break;
            }
        }

        return $results;
    }

    /**
     * @param  Collection<int, ImageResultData>  $results
     * @return Collection<int, ImageResultData>
     */
    private function deduplicateByUrl(Collection $results): Collection
    {
        return $results->unique(fn (ImageResultData $result) => $result->url);
    }

    /**
     * @return array{path: string, result: ImageResultData}|null
     */
    private function downloadImage(ImageResultData $result, int $workflowId, int $itemId, int $index): ?array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => config('autofill.image_sources.wikimedia_user_agent')])
                ->get($result->url);

            if ($response->failed()) {
                Log::warning('[Autofill:Images] Download HTTP failed', [
                    'source' => $result->source,
                    'url' => mb_substr($result->url, 0, 200),
                    'status' => $response->status(),
                ]);

                return null;
            }

            $extension = $this->guessExtension($response->header('Content-Type'), $result->url);
            $filename = sprintf('image_%03d.%s', $index + 1, $extension);
            $path = sprintf('%d/%d/%s', $workflowId, $itemId, $filename);

            $disk = config('autofill.images.temp_disk', 'autofill_temp');
            $written = Storage::disk($disk)->put($path, $response->body());

            if (! $written) {
                Log::warning('[Autofill:Images] Storage put() returned false', [
                    'path' => $path,
                    'disk' => $disk,
                    'body_size' => strlen($response->body()),
                ]);

                return null;
            }

            return [
                'path' => $path,
                'result' => $result,
            ];
        } catch (\Throwable $e) {
            Log::warning('[Autofill:Images] Download exception', [
                'source' => $result->source,
                'url' => mb_substr($result->url, 0, 200),
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function guessExtension(string $contentType, string $url): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        if (isset($mimeMap[$contentType])) {
            return $mimeMap[$contentType];
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return $extension;
        }

        return 'jpg';
    }
}
