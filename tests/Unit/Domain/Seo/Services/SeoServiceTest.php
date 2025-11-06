<?php

namespace Tests\Unit\Domain\Seo\Services;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\SeoData;
use App\Domain\Seo\DTO\TwitterCardsData;
use App\Domain\Seo\Services\SeoService;
use Mockery;
use Tests\TestCase;

class SeoServiceTest extends TestCase
{
    private SeoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SeoService;
    }

    public function test_generate_assembles_complete_seo_data_from_strategy(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        // Mock MetaTagsData
        $metaData = new MetaTagsData(
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            keywords: 'space, nasa, exploration',
            robots: 'index, follow',
            canonical: 'https://explo.space/en',
            geoLatitude: 28.5728,
            geoLongitude: -80.6490
        );

        // Mock OpenGraphData
        $openGraphData = new OpenGraphData(
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            type: 'website',
            url: 'https://explo.space/en',
            siteName: 'Explo.space',
            image: 'https://explo.space/images/og-default.jpg',
            imageAlt: 'Explo.space default image',
            locale: 'en_US',
            localeAlternates: ['fr_FR']
        );

        // Mock TwitterCardsData
        $twitterCardsData = new TwitterCardsData(
            card: 'summary_large_image',
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            url: 'https://explo.space/en',
            image: 'https://explo.space/images/twitter-default.jpg',
            imageAlt: 'Explo.space default image',
            site: '@explospace',
            creator: '@explospace'
        );

        // Mock JSON-LD data
        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Explo.space',
            ],
        ];

        // Mock hreflang data
        $hreflangData = [
            ['hreflang' => 'fr', 'href' => 'https://explo.space/fr'],
            ['hreflang' => 'en', 'href' => 'https://explo.space/en'],
        ];

        // Configure mock expectations
        $strategy->shouldReceive('getMetaTagsData')->once()->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->once()->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->once()->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->once()->andReturn($jsonLdData);
        $strategy->shouldReceive('getHreflangData')->once()->andReturn($hreflangData);

        // Execute
        $result = $this->service->generate($strategy);

        // Assert SeoData type
        $this->assertInstanceOf(SeoData::class, $result);

        // Assert meta tags
        $this->assertEquals('Explo.space - Discover space places', $result->title);
        $this->assertEquals('Explore the world of space exploration', $result->description);
        $this->assertEquals('space, nasa, exploration', $result->keywords);
        $this->assertEquals('index, follow', $result->robots);
        $this->assertEquals('https://explo.space/en', $result->canonical);
        $this->assertEquals(28.5728, $result->geo_latitude);
        $this->assertEquals(-80.6490, $result->geo_longitude);

        // Assert Open Graph
        $this->assertEquals('Explo.space - Discover space places', $result->ogTitle);
        $this->assertEquals('Explore the world of space exploration', $result->ogDescription);
        $this->assertEquals('website', $result->ogType);
        $this->assertEquals('https://explo.space/en', $result->ogUrl);
        $this->assertEquals('Explo.space', $result->ogSiteName);
        $this->assertEquals('https://explo.space/images/og-default.jpg', $result->ogImage);
        $this->assertEquals('Explo.space default image', $result->ogImageAlt);
        $this->assertEquals('en_US', $result->ogLocale);
        $this->assertEquals(['fr_FR'], $result->ogLocaleAlternates);

        // Assert Twitter Cards
        $this->assertEquals('summary_large_image', $result->twitterCard);
        $this->assertEquals('Explo.space - Discover space places', $result->twitterTitle);
        $this->assertEquals('Explore the world of space exploration', $result->twitterDescription);
        $this->assertEquals('https://explo.space/images/twitter-default.jpg', $result->twitterImage);
        $this->assertEquals('Explo.space default image', $result->twitterImageAlt);
        $this->assertEquals('@explospace', $result->twitterSite);
        $this->assertEquals('@explospace', $result->twitterCreator);

        // Assert JSON-LD
        $this->assertEquals($jsonLdData, $result->jsonLdSchemas);

        // Assert hreflang
        $this->assertEquals($hreflangData, $result->hreflangs);
    }

    public function test_generate_calls_all_strategy_methods(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $metaData = new MetaTagsData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index',
            canonical: 'https://example.com'
        );

        $openGraphData = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Test',
            image: 'https://example.com/image.jpg'
        );

        $twitterCardsData = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com',
            image: 'https://example.com/twitter.jpg'
        );

        // Verify each method is called exactly once
        $strategy->shouldReceive('getMetaTagsData')->once()->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->once()->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->once()->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->once()->andReturn([]);
        $strategy->shouldReceive('getHreflangData')->once()->andReturn([]);

        $this->service->generate($strategy);

        // Mockery will automatically verify expectations
    }

    public function test_generate_with_empty_json_ld_and_hreflang(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $metaData = new MetaTagsData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index',
            canonical: 'https://example.com'
        );

        $openGraphData = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Test',
            image: 'https://example.com/image.jpg'
        );

        $twitterCardsData = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com',
            image: 'https://example.com/twitter.jpg'
        );

        $strategy->shouldReceive('getMetaTagsData')->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->andReturn([]);
        $strategy->shouldReceive('getHreflangData')->andReturn([]);

        $result = $this->service->generate($strategy);

        $this->assertIsArray($result->jsonLdSchemas);
        $this->assertEmpty($result->jsonLdSchemas);
        $this->assertIsArray($result->hreflangs);
        $this->assertEmpty($result->hreflangs);
    }

    public function test_generate_without_geo_coordinates(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $metaData = new MetaTagsData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index',
            canonical: 'https://example.com'
        );

        $openGraphData = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Test',
            image: 'https://example.com/image.jpg'
        );

        $twitterCardsData = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com',
            image: 'https://example.com/twitter.jpg'
        );

        $strategy->shouldReceive('getMetaTagsData')->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->andReturn([]);
        $strategy->shouldReceive('getHreflangData')->andReturn([]);

        $result = $this->service->generate($strategy);

        $this->assertNull($result->geo_latitude);
        $this->assertNull($result->geo_longitude);
    }

    public function test_generate_with_multiple_json_ld_schemas(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $metaData = new MetaTagsData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index',
            canonical: 'https://example.com'
        );

        $openGraphData = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Test',
            image: 'https://example.com/image.jpg'
        );

        $twitterCardsData = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com',
            image: 'https://example.com/twitter.jpg'
        );

        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Explo.space',
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'url' => 'https://explo.space',
            ],
        ];

        $strategy->shouldReceive('getMetaTagsData')->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->andReturn($jsonLdData);
        $strategy->shouldReceive('getHreflangData')->andReturn([]);

        $result = $this->service->generate($strategy);

        $this->assertCount(2, $result->jsonLdSchemas);
        $this->assertEquals('Organization', $result->jsonLdSchemas[0]['@type']);
        $this->assertEquals('WebSite', $result->jsonLdSchemas[1]['@type']);
    }

    public function test_generate_with_multiple_hreflang_alternates(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $metaData = new MetaTagsData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index',
            canonical: 'https://example.com'
        );

        $openGraphData = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Test',
            image: 'https://example.com/image.jpg'
        );

        $twitterCardsData = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com',
            image: 'https://example.com/twitter.jpg'
        );

        $hreflangData = [
            ['hreflang' => 'fr', 'href' => 'https://explo.space/fr'],
            ['hreflang' => 'en', 'href' => 'https://explo.space/en'],
            ['hreflang' => 'x-default', 'href' => 'https://explo.space/fr'],
        ];

        $strategy->shouldReceive('getMetaTagsData')->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->andReturn([]);
        $strategy->shouldReceive('getHreflangData')->andReturn($hreflangData);

        $result = $this->service->generate($strategy);

        $this->assertCount(3, $result->hreflangs);
        $this->assertEquals('fr', $result->hreflangs[0]['hreflang']);
        $this->assertEquals('en', $result->hreflangs[1]['hreflang']);
        $this->assertEquals('x-default', $result->hreflangs[2]['hreflang']);
    }

    public function test_generate_preserves_breadcrumbs_as_empty_array(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $metaData = new MetaTagsData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index',
            canonical: 'https://example.com'
        );

        $openGraphData = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Test',
            image: 'https://example.com/image.jpg'
        );

        $twitterCardsData = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com',
            image: 'https://example.com/twitter.jpg'
        );

        $strategy->shouldReceive('getMetaTagsData')->andReturn($metaData);
        $strategy->shouldReceive('getOpenGraphData')->andReturn($openGraphData);
        $strategy->shouldReceive('getTwitterCardsData')->andReturn($twitterCardsData);
        $strategy->shouldReceive('getJsonLdData')->andReturn([]);
        $strategy->shouldReceive('getHreflangData')->andReturn([]);

        $result = $this->service->generate($strategy);

        $this->assertIsArray($result->breadcrumbs);
        $this->assertEmpty($result->breadcrumbs);
    }
}
