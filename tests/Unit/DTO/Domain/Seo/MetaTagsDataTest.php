<?php

namespace Tests\Unit\DTO\Domain\Seo;

use App\Domain\Seo\DTO\MetaTagsData;
use Tests\TestCase;

class MetaTagsDataTest extends TestCase
{
    public function test_can_be_instantiated_with_required_properties(): void
    {
        $data = new MetaTagsData(
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            keywords: 'space, nasa, exploration',
            robots: 'index, follow',
            canonical: 'https://explo.space/en'
        );

        $this->assertEquals('Explo.space - Discover space places', $data->title);
        $this->assertEquals('Explore the world of space exploration', $data->description);
        $this->assertEquals('space, nasa, exploration', $data->keywords);
        $this->assertEquals('index, follow', $data->robots);
        $this->assertEquals('https://explo.space/en', $data->canonical);
    }

    public function test_can_be_instantiated_with_geo_coordinates(): void
    {
        $data = new MetaTagsData(
            title: 'Kennedy Space Center',
            description: 'NASA launch facility',
            keywords: 'nasa, kennedy, space center',
            robots: 'index, follow',
            canonical: 'https://explo.space/en/places/kennedy-space-center',
            geoLatitude: 28.5728,
            geoLongitude: -80.6490
        );

        $this->assertEquals(28.5728, $data->geoLatitude);
        $this->assertEquals(-80.6490, $data->geoLongitude);
    }

    public function test_geo_coordinates_default_to_null(): void
    {
        $data = new MetaTagsData(
            title: 'Homepage',
            description: 'Main page',
            keywords: 'space',
            robots: 'index, follow',
            canonical: 'https://explo.space'
        );

        $this->assertNull($data->geoLatitude);
        $this->assertNull($data->geoLongitude);
    }

    public function test_all_properties_are_readonly(): void
    {
        $data = new MetaTagsData(
            title: 'Test',
            description: 'Test description',
            keywords: 'test',
            robots: 'noindex',
            canonical: 'https://example.com'
        );

        // PHP 8.1+ readonly properties cannot be modified
        $reflection = new \ReflectionClass($data);
        $titleProperty = $reflection->getProperty('title');

        $this->assertTrue($titleProperty->isReadOnly());
    }

    public function test_handles_empty_strings_for_optional_seo_fields(): void
    {
        $data = new MetaTagsData(
            title: 'Page without keywords',
            description: '',
            keywords: '',
            robots: 'index, follow',
            canonical: 'https://explo.space/test'
        );

        $this->assertEquals('', $data->description);
        $this->assertEquals('', $data->keywords);
    }
}
