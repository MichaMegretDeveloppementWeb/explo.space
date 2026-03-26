<?php

namespace Tests\Unit\Services\ImageSource;

use App\DTO\ImageSource\ImageResultData;
use App\Services\ImageSource\UnsplashImageSource;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UnsplashImageSourceTest extends TestCase
{
    private UnsplashImageSource $source;

    protected function setUp(): void
    {
        parent::setUp();
        config(['autofill.image_sources.unsplash_key' => 'test-key']);
        $this->source = new UnsplashImageSource;
    }

    public function test_source_name(): void
    {
        $this->assertSame('unsplash', $this->source->sourceName());
    }

    public function test_returns_empty_without_api_key(): void
    {
        config(['autofill.image_sources.unsplash_key' => null]);
        $source = new UnsplashImageSource;

        $results = $source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_relevant_results(): void
    {
        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    [
                        'urls' => ['regular' => 'https://images.unsplash.com/photo-1.jpg', 'full' => 'https://images.unsplash.com/photo-1-full.jpg'],
                        'description' => 'Kennedy Space Center rocket launch',
                        'alt_description' => 'a rocket launching from Kennedy Space Center',
                        'width' => 4000,
                        'height' => 3000,
                        'tags' => [['title' => 'space'], ['title' => 'rocket']],
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(ImageResultData::class, $results->first());
        $this->assertSame('unsplash', $results->first()->source);
        $this->assertSame('Unsplash License', $results->first()->license);
        $this->assertSame('https://images.unsplash.com/photo-1.jpg', $results->first()->url);
    }

    public function test_filters_irrelevant_results(): void
    {
        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    [
                        'urls' => ['regular' => 'https://images.unsplash.com/generic.jpg'],
                        'description' => 'beautiful sunset over ocean',
                        'alt_description' => 'orange sky',
                        'width' => 4000,
                        'height' => 3000,
                        'tags' => [['title' => 'sunset'], ['title' => 'ocean']],
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
            'api.unsplash.com/*' => Http::response(null, 429),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_connection_error(): void
    {
        Http::fake([
            'api.unsplash.com/*' => fn () => throw new \Exception('Connection timeout'),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_sends_authorization_header(): void
    {
        Http::fake([
            'api.unsplash.com/*' => Http::response(['results' => []]),
        ]);

        $this->source->searchImages('Test');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Client-ID test-key');
        });
    }
}
