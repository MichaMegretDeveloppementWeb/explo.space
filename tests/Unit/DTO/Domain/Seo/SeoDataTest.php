<?php

namespace Tests\Unit\DTO\Domain\Seo;

use App\Domain\Seo\DTO\SeoData;
use Tests\TestCase;

class SeoDataTest extends TestCase
{
    public function test_can_be_instantiated_with_required_properties(): void
    {
        $data = new SeoData(
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            keywords: 'space, nasa, exploration',
            robots: 'index, follow',
            canonical: 'https://explo.space/en',
            ogTitle: 'Explo.space - Discover space places',
            ogDescription: 'Explore the world of space exploration',
            ogType: 'website',
            ogUrl: 'https://explo.space/en',
            ogSiteName: 'Explo.space',
            ogImage: 'https://explo.space/images/og-default.jpg',
            ogImageAlt: null,
            ogLocale: 'en_US',
            ogLocaleAlternates: [],
            twitterCard: 'summary_large_image',
            twitterTitle: 'Explo.space - Discover space places',
            twitterDescription: 'Explore the world of space exploration',
            twitterImage: 'https://explo.space/images/twitter-default.jpg'
        );

        // Meta tags
        $this->assertEquals('Explo.space - Discover space places', $data->title);
        $this->assertEquals('Explore the world of space exploration', $data->description);
        $this->assertEquals('space, nasa, exploration', $data->keywords);
        $this->assertEquals('index, follow', $data->robots);
        $this->assertEquals('https://explo.space/en', $data->canonical);

        // Open Graph
        $this->assertEquals('Explo.space - Discover space places', $data->ogTitle);
        $this->assertEquals('Explore the world of space exploration', $data->ogDescription);
        $this->assertEquals('website', $data->ogType);
        $this->assertEquals('https://explo.space/en', $data->ogUrl);
        $this->assertEquals('Explo.space', $data->ogSiteName);
        $this->assertEquals('https://explo.space/images/og-default.jpg', $data->ogImage);

        // Twitter Cards
        $this->assertEquals('summary_large_image', $data->twitterCard);
        $this->assertEquals('Explo.space - Discover space places', $data->twitterTitle);
        $this->assertEquals('Explore the world of space exploration', $data->twitterDescription);
        $this->assertEquals('https://explo.space/images/twitter-default.jpg', $data->twitterImage);
    }

    public function test_optional_image_alt_properties_can_be_null(): void
    {
        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg',
            twitterImageAlt: null
        );

        $this->assertNull($data->ogImageAlt);
        $this->assertNull($data->twitterImageAlt);
    }

    public function test_optional_twitter_properties_default_to_null(): void
    {
        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg'
        );

        $this->assertNull($data->twitterSite);
        $this->assertNull($data->twitterCreator);
    }

    public function test_array_properties_default_to_empty_arrays(): void
    {
        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg'
        );

        $this->assertIsArray($data->hreflangs);
        $this->assertEmpty($data->hreflangs);

        $this->assertIsArray($data->jsonLdSchemas);
        $this->assertEmpty($data->jsonLdSchemas);

        $this->assertIsArray($data->breadcrumbs);
        $this->assertEmpty($data->breadcrumbs);

        $this->assertIsArray($data->ogLocaleAlternates);
        $this->assertEmpty($data->ogLocaleAlternates);
    }

    public function test_can_provide_hreflang_alternates(): void
    {
        $hreflangs = [
            ['hreflang' => 'fr', 'href' => 'https://explo.space/fr'],
            ['hreflang' => 'en', 'href' => 'https://explo.space/en'],
        ];

        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg',
            hreflangs: $hreflangs
        );

        $this->assertEquals($hreflangs, $data->hreflangs);
        $this->assertCount(2, $data->hreflangs);
    }

    public function test_can_provide_json_ld_schemas(): void
    {
        $schemas = [
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

        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg',
            jsonLdSchemas: $schemas
        );

        $this->assertEquals($schemas, $data->jsonLdSchemas);
        $this->assertCount(2, $data->jsonLdSchemas);
    }

    public function test_can_provide_breadcrumbs(): void
    {
        $breadcrumbs = [
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => 'https://explo.space',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Places',
                    'item' => 'https://explo.space/places',
                ],
            ],
        ];

        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg',
            breadcrumbs: $breadcrumbs
        );

        $this->assertEquals($breadcrumbs, $data->breadcrumbs);
        $this->assertArrayHasKey('itemListElement', $data->breadcrumbs);
    }

    public function test_geo_coordinates_default_to_null(): void
    {
        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg'
        );

        $this->assertNull($data->geo_latitude);
        $this->assertNull($data->geo_longitude);
    }

    public function test_can_provide_geo_coordinates(): void
    {
        $data = new SeoData(
            title: 'Kennedy Space Center',
            description: 'NASA launch facility',
            keywords: 'nasa, kennedy, space center',
            robots: 'index, follow',
            canonical: 'https://explo.space/places/kennedy-space-center',
            ogTitle: 'Kennedy Space Center',
            ogDescription: 'NASA launch facility',
            ogType: 'place',
            ogUrl: 'https://explo.space/places/kennedy-space-center',
            ogSiteName: 'Explo.space',
            ogImage: 'https://explo.space/images/kennedy.jpg',
            ogImageAlt: null,
            ogLocale: 'en_US',
            ogLocaleAlternates: [],
            twitterCard: 'summary_large_image',
            twitterTitle: 'Kennedy Space Center',
            twitterDescription: 'NASA launch facility',
            twitterImage: 'https://explo.space/images/kennedy.jpg',
            geo_latitude: 28.5728,
            geo_longitude: -80.6490
        );

        $this->assertEquals(28.5728, $data->geo_latitude);
        $this->assertEquals(-80.6490, $data->geo_longitude);
    }

    public function test_all_properties_are_readonly(): void
    {
        $data = new SeoData(
            title: 'Test',
            description: 'Test',
            keywords: 'test',
            robots: 'index, follow',
            canonical: 'https://example.com',
            ogTitle: 'Test',
            ogDescription: 'Test',
            ogType: 'website',
            ogUrl: 'https://example.com',
            ogSiteName: 'Example',
            ogImage: 'https://example.com/og.jpg',
            ogImageAlt: null,
            ogLocale: 'fr_FR',
            ogLocaleAlternates: [],
            twitterCard: 'summary',
            twitterTitle: 'Test',
            twitterDescription: 'Test',
            twitterImage: 'https://example.com/twitter.jpg'
        );

        $reflection = new \ReflectionClass($data);
        $titleProperty = $reflection->getProperty('title');

        $this->assertTrue($titleProperty->isReadOnly());
    }
}
