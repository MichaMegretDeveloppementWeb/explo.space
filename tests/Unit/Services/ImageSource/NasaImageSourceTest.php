<?php

namespace Tests\Unit\Services\ImageSource;

use App\DTO\ImageSource\ImageResultData;
use App\Services\ImageSource\NasaImageSource;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NasaImageSourceTest extends TestCase
{
    private NasaImageSource $source;

    protected function setUp(): void
    {
        parent::setUp();
        $this->source = new NasaImageSource;
    }

    public function test_source_name(): void
    {
        $this->assertSame('nasa', $this->source->sourceName());
    }

    public function test_returns_results_with_full_size_url_from_asset_manifest(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        [
                            'data' => [[
                                'title' => 'Kennedy Space Center Launch',
                                'description' => 'Space Shuttle Atlantis lifts off from Launch Pad 39A.',
                                'nasa_id' => 'KSC-20110708-PH_1234',
                                'media_type' => 'image',
                            ]],
                            'links' => [[
                                'href' => 'https://images-assets.nasa.gov/image/KSC-20110708-PH_1234/KSC-20110708-PH_1234~thumb.jpg',
                                'rel' => 'preview',
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/asset/KSC-20110708-PH_1234' => Http::response([
                'collection' => [
                    'items' => [
                        ['href' => 'https://images-assets.nasa.gov/image/KSC-20110708-PH_1234/KSC-20110708-PH_1234~thumb.jpg'],
                        ['href' => 'https://images-assets.nasa.gov/image/KSC-20110708-PH_1234/KSC-20110708-PH_1234~medium.jpg'],
                        ['href' => 'https://images-assets.nasa.gov/image/KSC-20110708-PH_1234/KSC-20110708-PH_1234~large.jpg'],
                        ['href' => 'https://images-assets.nasa.gov/image/KSC-20110708-PH_1234/KSC-20110708-PH_1234~orig.jpg'],
                        ['href' => 'https://images-assets.nasa.gov/image/KSC-20110708-PH_1234/metadata.json'],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(ImageResultData::class, $results->first());
        $this->assertSame('nasa', $results->first()->source);
        $this->assertSame('Public Domain', $results->first()->license);
        $this->assertStringContainsString('~large.jpg', $results->first()->url);
        $this->assertStringContainsString('Space Shuttle Atlantis', $results->first()->caption);
    }

    public function test_prefers_large_over_medium_over_orig(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        $this->makeSearchItem('TEST-001', 'Test Image'),
                    ],
                ],
            ]),
            'images-api.nasa.gov/asset/TEST-001' => Http::response([
                'collection' => [
                    'items' => [
                        ['href' => 'https://images-assets.nasa.gov/image/TEST-001/TEST-001~thumb.jpg'],
                        ['href' => 'https://images-assets.nasa.gov/image/TEST-001/TEST-001~medium.jpg'],
                        ['href' => 'https://images-assets.nasa.gov/image/TEST-001/TEST-001~orig.jpg'],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Test');

        // No ~large available, should pick ~medium
        $this->assertStringContainsString('~medium.jpg', $results->first()->url);
    }

    public function test_falls_back_to_preview_when_asset_manifest_fails(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        [
                            'data' => [[
                                'title' => 'Fallback Test',
                                'description' => 'Testing fallback behavior.',
                                'nasa_id' => 'FALLBACK-001',
                                'media_type' => 'image',
                            ]],
                            'links' => [[
                                'href' => 'https://images-assets.nasa.gov/image/FALLBACK-001/FALLBACK-001~thumb.jpg',
                                'rel' => 'preview',
                            ]],
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/asset/FALLBACK-001' => Http::response(null, 500),
        ]);

        $results = $this->source->searchImages('Fallback');

        $this->assertCount(1, $results);
        $this->assertStringContainsString('~thumb.jpg', $results->first()->url);
    }

    public function test_filters_items_without_nasa_id(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        [
                            'data' => [['title' => 'Missing ID', 'media_type' => 'image']],
                            'links' => [[
                                'href' => 'https://example.com/thumb.jpg',
                                'rel' => 'preview',
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Test');

        $this->assertCount(0, $results);
    }

    public function test_filters_non_image_media_types(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        [
                            'data' => [[
                                'title' => 'Audio clip',
                                'nasa_id' => 'AUDIO-001',
                                'media_type' => 'audio',
                            ]],
                            'links' => [],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Test');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_api_failure(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response(null, 500),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_no_results(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => ['items' => []],
            ]),
        ]);

        $results = $this->source->searchImages('Nonexistent Place XYZ');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_connection_error(): void
    {
        Http::fake([
            'images-api.nasa.gov/*' => fn () => throw new \Exception('Connection timeout'),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_truncates_long_descriptions(): void
    {
        $longDescription = str_repeat('A very long description. ', 100);

        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        $this->makeSearchItem('LONG-001', 'Test', $longDescription),
                    ],
                ],
            ]),
            'images-api.nasa.gov/asset/LONG-001' => Http::response([
                'collection' => [
                    'items' => [
                        ['href' => 'https://images-assets.nasa.gov/image/LONG-001/LONG-001~large.jpg'],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Test');

        $this->assertLessThanOrEqual(500, mb_strlen($results->first()->caption));
    }

    public function test_skips_item_when_no_url_resolvable(): void
    {
        Http::fake([
            'images-api.nasa.gov/search*' => Http::response([
                'collection' => [
                    'items' => [
                        [
                            'data' => [[
                                'title' => 'No links',
                                'nasa_id' => 'NOLINK-001',
                                'media_type' => 'image',
                            ]],
                            // No links at all
                        ],
                    ],
                ],
            ]),
            'images-api.nasa.gov/asset/NOLINK-001' => Http::response([
                'collection' => ['items' => [
                    ['href' => 'https://images-assets.nasa.gov/image/NOLINK-001/metadata.json'],
                ]],
            ]),
        ]);

        $results = $this->source->searchImages('Test');

        $this->assertCount(0, $results);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeSearchItem(string $nasaId, string $title, ?string $description = null): array
    {
        return [
            'data' => [[
                'title' => $title,
                'description' => $description ?? "Description for {$title}.",
                'nasa_id' => $nasaId,
                'media_type' => 'image',
            ]],
            'links' => [[
                'href' => "https://images-assets.nasa.gov/image/{$nasaId}/{$nasaId}~thumb.jpg",
                'rel' => 'preview',
            ]],
        ];
    }
}
