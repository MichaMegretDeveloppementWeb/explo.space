<?php

namespace Tests\Unit\DTO\Domain\Seo;

use App\Domain\Seo\DTO\OpenGraphData;
use Tests\TestCase;

class OpenGraphDataTest extends TestCase
{
    public function test_can_be_instantiated_with_required_properties(): void
    {
        $data = new OpenGraphData(
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            type: 'website',
            url: 'https://explo.space/en',
            siteName: 'Explo.space',
            image: 'https://explo.space/images/og-default.jpg'
        );

        $this->assertEquals('Explo.space - Discover space places', $data->title);
        $this->assertEquals('Explore the world of space exploration', $data->description);
        $this->assertEquals('website', $data->type);
        $this->assertEquals('https://explo.space/en', $data->url);
        $this->assertEquals('Explo.space', $data->siteName);
        $this->assertEquals('https://explo.space/images/og-default.jpg', $data->image);
    }

    public function test_can_be_instantiated_with_optional_image_alt(): void
    {
        $data = new OpenGraphData(
            title: 'Kennedy Space Center',
            description: 'NASA launch facility',
            type: 'article',
            url: 'https://explo.space/en/places/kennedy-space-center',
            siteName: 'Explo.space',
            image: 'https://explo.space/images/places/kennedy.jpg',
            imageAlt: 'Kennedy Space Center launch pad'
        );

        $this->assertEquals('Kennedy Space Center launch pad', $data->imageAlt);
    }

    public function test_image_alt_defaults_to_null(): void
    {
        $data = new OpenGraphData(
            title: 'Test',
            description: 'Test description',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Example',
            image: 'https://example.com/image.jpg'
        );

        $this->assertNull($data->imageAlt);
    }

    public function test_locale_defaults_to_fr_fr(): void
    {
        $data = new OpenGraphData(
            title: 'Test',
            description: 'Test description',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Example',
            image: 'https://example.com/image.jpg'
        );

        $this->assertEquals('fr_FR', $data->locale);
    }

    public function test_can_override_default_locale(): void
    {
        $data = new OpenGraphData(
            title: 'Test',
            description: 'Test description',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Example',
            image: 'https://example.com/image.jpg',
            locale: 'en_US'
        );

        $this->assertEquals('en_US', $data->locale);
    }

    public function test_locale_alternates_default_to_empty_array(): void
    {
        $data = new OpenGraphData(
            title: 'Test',
            description: 'Test description',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Example',
            image: 'https://example.com/image.jpg'
        );

        $this->assertIsArray($data->localeAlternates);
        $this->assertEmpty($data->localeAlternates);
    }

    public function test_can_provide_locale_alternates(): void
    {
        $alternates = [
            'en_US' => 'https://explo.space/en',
            'fr_FR' => 'https://explo.space/fr',
        ];

        $data = new OpenGraphData(
            title: 'Test',
            description: 'Test description',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Example',
            image: 'https://example.com/image.jpg',
            localeAlternates: $alternates
        );

        $this->assertEquals($alternates, $data->localeAlternates);
        $this->assertCount(2, $data->localeAlternates);
    }

    public function test_all_properties_are_readonly(): void
    {
        $data = new OpenGraphData(
            title: 'Test',
            description: 'Test',
            type: 'website',
            url: 'https://example.com',
            siteName: 'Example',
            image: 'https://example.com/image.jpg'
        );

        $reflection = new \ReflectionClass($data);
        $titleProperty = $reflection->getProperty('title');

        $this->assertTrue($titleProperty->isReadOnly());
    }
}
