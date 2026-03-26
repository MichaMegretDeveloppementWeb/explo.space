<?php

namespace Tests\Unit\Services\ImageSource;

use App\DTO\ImageSource\ImageResultData;
use App\Services\ImageSource\WikimediaImageSource;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WikimediaImageSourceTest extends TestCase
{
    private WikimediaImageSource $source;

    protected function setUp(): void
    {
        parent::setUp();
        $this->source = new WikimediaImageSource;
    }

    public function test_source_name(): void
    {
        $this->assertSame('wikimedia', $this->source->sourceName());
    }

    public function test_returns_results_for_valid_response(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '12345' => [
                            'title' => 'File:Kennedy Space Center.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/kennedy.jpg',
                                'thumburl' => 'https://upload.wikimedia.org/thumb/kennedy_1200px.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => [
                                    'ImageDescription' => ['value' => 'Kennedy Space Center aerial view'],
                                    'LicenseShortName' => ['value' => 'CC BY-SA 4.0'],
                                ],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(ImageResultData::class, $results->first());
        $this->assertSame('wikimedia', $results->first()->source);
        $this->assertSame('CC BY-SA 4.0', $results->first()->license);
        $this->assertSame('Kennedy Space Center aerial view', $results->first()->caption);
    }

    public function test_prefers_thumburl_over_original_url(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:Large Image.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/original_15mb.jpg',
                                'thumburl' => 'https://upload.wikimedia.org/thumb/resized_200kb.jpg',
                                'width' => 4000,
                                'height' => 3000,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Test Place');

        $this->assertSame('https://upload.wikimedia.org/thumb/resized_200kb.jpg', $results->first()->url);
    }

    public function test_falls_back_to_original_url_when_no_thumburl(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:No Thumb.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/original.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Test Place');

        $this->assertSame('https://upload.wikimedia.org/original.jpg', $results->first()->url);
    }

    public function test_filters_out_logos_and_icons(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:NASA logo.png',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/nasa_logo.png',
                                'width' => 500,
                                'height' => 500,
                                'extmetadata' => [],
                            ]],
                        ],
                        '2' => [
                            'title' => 'File:Kennedy Space Center.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/kennedy.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => [],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertStringContainsString('kennedy', $results->first()->url);
    }

    public function test_filters_out_small_images(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:Tiny thumbnail.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/tiny.jpg',
                                'width' => 50,
                                'height' => 50,
                                'extmetadata' => [],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_filters_out_svg_files(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:Map.svg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/map.svg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => [],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_api_failure(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response(null, 500),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_no_results(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => ['pages' => []],
            ]),
        ]);

        $results = $this->source->searchImages('Nonexistent Place XYZ');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_connection_error(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => fn () => throw new \Exception('Connection timeout'),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_performs_secondary_search_when_few_primary_results(): void
    {
        $callCount = 0;

        Http::fake([
            'commons.wikimedia.org/*' => function () use (&$callCount) {
                $callCount++;

                if ($callCount === 1) {
                    // First search (English name): only 1 result
                    return Http::response([
                        'query' => [
                            'pages' => [
                                '1' => [
                                    'title' => 'File:Air Museum English.jpg',
                                    'imageinfo' => [[
                                        'url' => 'https://upload.wikimedia.org/english.jpg',
                                        'width' => 1200,
                                        'height' => 800,
                                        'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                                    ]],
                                ],
                            ],
                        ],
                    ]);
                }

                // Second search (French name): additional results
                return Http::response([
                    'query' => [
                        'pages' => [
                            '2' => [
                                'title' => 'File:Musée Air Espace.jpg',
                                'imageinfo' => [[
                                    'url' => 'https://upload.wikimedia.org/french.jpg',
                                    'width' => 1200,
                                    'height' => 800,
                                    'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                                ]],
                            ],
                        ],
                    ],
                ]);
            },
        ]);

        $results = $this->source->searchImages(
            'Air and Space Museum',
            'Musée de l\'Air et de l\'Espace'
        );

        // Should have both English and French results
        $this->assertCount(2, $results);
        $this->assertSame(2, $callCount, 'Should have made 2 API calls');
    }

    public function test_skips_secondary_search_when_enough_primary_results(): void
    {
        Http::fake([
            'commons.wikimedia.org/*' => Http::response([
                'query' => [
                    'pages' => [
                        '1' => [
                            'title' => 'File:KSC 1.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc1.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                        '2' => [
                            'title' => 'File:KSC 2.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc2.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                        '3' => [
                            'title' => 'File:KSC 3.jpg',
                            'imageinfo' => [[
                                'url' => 'https://upload.wikimedia.org/ksc3.jpg',
                                'width' => 1200,
                                'height' => 800,
                                'extmetadata' => ['LicenseShortName' => ['value' => 'CC0']],
                            ]],
                        ],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages(
            'Kennedy Space Center',
            'Centre spatial Kennedy'
        );

        // 3 results from primary = no secondary search needed
        $this->assertCount(3, $results);

        // Should only have sent 1 API request (no secondary search)
        Http::assertSentCount(1);
    }

    public function test_skips_secondary_search_when_same_name(): void
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
        ]);

        $results = $this->source->searchImages(
            'Kennedy Space Center',
            'Kennedy Space Center' // Same name — no point in searching again
        );

        $this->assertCount(1, $results);
        Http::assertSentCount(1);
    }
}
