<?php

namespace Tests\Unit\Services\ImageSource;

use App\DTO\ImageSource\ImageResultData;
use App\Services\ImageSource\PexelsImageSource;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PexelsImageSourceTest extends TestCase
{
    private PexelsImageSource $source;

    protected function setUp(): void
    {
        parent::setUp();
        config(['autofill.image_sources.pexels_key' => 'test-key']);
        $this->source = new PexelsImageSource;
    }

    public function test_source_name(): void
    {
        $this->assertSame('pexels', $this->source->sourceName());
    }

    public function test_returns_empty_without_api_key(): void
    {
        config(['autofill.image_sources.pexels_key' => null]);
        $source = new PexelsImageSource;

        $results = $source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_results_for_valid_response(): void
    {
        Http::fake([
            'api.pexels.com/*' => Http::response([
                'photos' => [
                    [
                        'src' => [
                            'large' => 'https://images.pexels.com/photo-1-large.jpg',
                            'original' => 'https://images.pexels.com/photo-1-original.jpg',
                        ],
                        'alt' => 'Space center with rocket',
                        'width' => 3000,
                        'height' => 2000,
                    ],
                ],
            ]),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(ImageResultData::class, $results->first());
        $this->assertSame('pexels', $results->first()->source);
        $this->assertSame('Pexels License', $results->first()->license);
        $this->assertSame('https://images.pexels.com/photo-1-large.jpg', $results->first()->url);
        $this->assertSame('Space center with rocket', $results->first()->caption);
    }

    public function test_returns_empty_on_api_failure(): void
    {
        Http::fake([
            'api.pexels.com/*' => Http::response(null, 500),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_returns_empty_on_connection_error(): void
    {
        Http::fake([
            'api.pexels.com/*' => fn () => throw new \Exception('Connection timeout'),
        ]);

        $results = $this->source->searchImages('Kennedy Space Center');

        $this->assertCount(0, $results);
    }

    public function test_sends_authorization_header(): void
    {
        Http::fake([
            'api.pexels.com/*' => Http::response(['photos' => []]),
        ]);

        $this->source->searchImages('Test');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'test-key');
        });
    }
}
