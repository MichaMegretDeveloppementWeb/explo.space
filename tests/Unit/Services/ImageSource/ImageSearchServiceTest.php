<?php

namespace Tests\Unit\Services\ImageSource;

use App\DTO\ImageSource\ImageResultData;
use App\Services\ImageSource\ImageSearchService;
use App\Services\ImageSource\NasaImageSource;
use App\Services\ImageSource\PexelsImageSource;
use App\Services\ImageSource\UnsplashImageSource;
use App\Services\ImageSource\WikimediaImageSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageSearchServiceTest extends TestCase
{
    private ImageSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['autofill.images.max_per_place' => 5]);
        config(['autofill.image_sources.unsplash_key' => 'test-unsplash']);
        config(['autofill.image_sources.pexels_key' => 'test-pexels']);

        $this->service = new ImageSearchService(
            new WikimediaImageSource,
            new NasaImageSource,
            new UnsplashImageSource,
            new PexelsImageSource,
        );
    }

    public function test_combines_primary_sources(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:KSC from wiki.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC BY-SA 4.0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        [
                            'data' => [[
                                'title' => 'KSC Launch',
                                'description' => 'Launch from KSC',
                                'nasa_id' => 'KSC-001',
                                'media_type' => 'image',
                            ]],
                            'links' => [['href' => 'https://images-assets.nasa.gov/image/KSC-001/KSC-001~thumb.jpg', 'rel' => 'preview']],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/asset/KSC-001' => Http::response([
                'collection' => [
                    'items' => [
                        ['href' => 'https://images-assets.nasa.gov/image/KSC-001/KSC-001~large.jpg'],
                    ],
                ],
            ]),
        ]);

        $results = $this->service->search('Kennedy Space Center');

        $this->assertCount(2, $results);
        $sources = $results->pluck('source')->toArray();
        $this->assertContains('wikimedia', $sources);
        $this->assertContains('nasa', $sources);
    }

    public function test_falls_back_to_unsplash_when_primary_empty(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response(['query' => ['pages' => []]]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    [
                        'urls' => ['regular' => 'https://unsplash.com/ksc.jpg'],
                        'description' => 'Kennedy Space Center building',
                        'alt_description' => null,
                        'width' => 4000,
                        'height' => 3000,
                        'tags' => [['title' => 'kennedy']],
                    ],
                ],
            ]),
        ]);

        $results = $this->service->search('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertSame('unsplash', $results->first()->source);
    }

    public function test_falls_back_to_pexels_when_unsplash_empty(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response(['query' => ['pages' => []]]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            'api.unsplash.com/*' => Http::response(['results' => []]),
            'api.pexels.com/*' => Http::response([
                'photos' => [
                    [
                        'src' => ['large' => 'https://pexels.com/ksc.jpg'],
                        'alt' => 'Rocket at space center',
                        'width' => 3000,
                        'height' => 2000,
                    ],
                ],
            ]),
        ]);

        $results = $this->service->search('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertSame('pexels', $results->first()->source);
    }

    public function test_does_not_call_fallback_when_primary_has_results(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:KSC.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            'api.unsplash.com/*' => Http::response(['results' => []]),
            'api.pexels.com/*' => Http::response(['photos' => []]),
        ]);

        $this->service->search('Kennedy Space Center');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'unsplash'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'pexels'));
    }

    public function test_deduplicates_by_url(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:KSC.jpg',
                            'imageinfo' => [[
                                'url' => 'https://example.com/same.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                        '2' => [
                            'title' => 'File:KSC duplicate.jpg',
                            'imageinfo' => [[
                                'url' => 'https://example.com/same.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
        ]);

        $results = $this->service->search('Kennedy Space Center');

        $this->assertCount(1, $results);
    }

    public function test_limits_to_max_per_place(): void
    {
        config(['autofill.images.max_per_place' => 2]);

        $this->service = new ImageSearchService(
            new WikimediaImageSource,
            new NasaImageSource,
            new UnsplashImageSource,
            new PexelsImageSource,
        );

        $pages = [];
        for ($i = 1; $i <= 5; $i++) {
            $pages[(string) $i] = [
                'title' => "File:KSC {$i}.jpg",
                'imageinfo' => [[
                    'url' => "https://upload.wikimedia.org/ksc_{$i}.jpg",
                    'width' => 1200,
                    'height' => 800,
                    'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                ]],
            ];
        }

        Http::fake([
            'commons.wikimedia.org/*' => Http::response(['query' => ['pages' => $pages]]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
        ]);

        $results = $this->service->search('Kennedy Space Center');

        $this->assertCount(2, $results);
    }

    public function test_returns_empty_when_all_sources_fail(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response(null, 500),
            'images-api.nasa.gov/*' => Http::response(null, 500),
            'api.unsplash.com/*' => Http::response(null, 500),
            'api.pexels.com/*' => Http::response(null, 500),
        ]);

        $results = $this->service->search('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_search_and_download_stores_files(): void
    {
        Storage::fake('autofill_temp');

        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:KSC.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            'https://upload.wikimedia.org/ksc.jpg' => Http::response('fake-image-content', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $results = $this->service->searchAndDownload('Kennedy Space Center', 1, 42);

        $this->assertCount(1, $results);
        $this->assertSame('1/42/image_001.jpg', $results->first()['path']);
        $this->assertInstanceOf(ImageResultData::class, $results->first()['result']);

        Storage::disk('autofill_temp')->assertExists('1/42/image_001.jpg');
    }

    public function test_search_and_download_skips_failed_downloads(): void
    {
        Storage::fake('autofill_temp');

        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:KSC.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            'https://upload.wikimedia.org/ksc.jpg' => Http::response(null, 404),
            'api.unsplash.com/*' => Http::response(['results' => []]),
            'api.pexels.com/*' => Http::response(['photos' => []]),
        ]);

        $results = $this->service->searchAndDownload('Kennedy Space Center', 1, 42);

        $this->assertCount(0, $results);
    }

    public function test_search_and_download_falls_back_when_all_primary_downloads_fail(): void
    {
        Storage::fake('autofill_temp');

        Http::fake([
            // Primary: Wikimedia finds images but download returns 403
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:Museum.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/museum.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            // Primary download fails
            'https://upload.wikimedia.org/museum.jpg' => Http::response(null, 403),
            // Fallback: Unsplash works
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    [
                        'urls' => ['regular' => 'https://images.unsplash.com/museum.jpg'],
                        'description' => 'A museum building',
                        'alt_description' => null,
                        'width' => 4000,
                        'height' => 3000,
                        'tags' => [],
                    ],
                ],
            ]),
            'https://images.unsplash.com/museum.jpg' => Http::response('unsplash-image', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $results = $this->service->searchAndDownload('Air and Space Museum', 1, 10);

        $this->assertCount(1, $results);
        $this->assertSame('unsplash', $results->first()['result']->source);
        Storage::disk('autofill_temp')->assertExists('1/10/image_001.jpg');
    }

    public function test_download_sends_user_agent_header(): void
    {
        Storage::fake('autofill_temp');
        config(['autofill.image_sources.wikimedia_user_agent' => 'TestBot/1.0']);

        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:Test.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/test.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/*' => Http::response(['collection' => ['items' => []]]),
            'https://upload.wikimedia.org/test.jpg' => Http::response('image-data', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $this->service->searchAndDownload('Test Place', 1, 1);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'upload.wikimedia.org/test.jpg')
                && $request->header('User-Agent')[0] === 'TestBot/1.0';
        });
    }
}
