<?php

namespace Tests\Unit\DTO\Domain\Seo;

use App\Domain\Seo\DTO\TwitterCardsData;
use Tests\TestCase;

class TwitterCardsDataTest extends TestCase
{
    public function test_can_be_instantiated_with_required_properties(): void
    {
        $data = new TwitterCardsData(
            card: 'summary_large_image',
            title: 'Explo.space - Discover space places',
            description: 'Explore the world of space exploration',
            url: 'https://explo.space/en'
        );

        $this->assertEquals('summary_large_image', $data->card);
        $this->assertEquals('Explo.space - Discover space places', $data->title);
        $this->assertEquals('Explore the world of space exploration', $data->description);
        $this->assertEquals('https://explo.space/en', $data->url);
    }

    public function test_can_be_instantiated_with_all_optional_properties(): void
    {
        $data = new TwitterCardsData(
            card: 'summary_large_image',
            title: 'Kennedy Space Center',
            description: 'NASA launch facility',
            url: 'https://explo.space/en/places/kennedy-space-center',
            image: 'https://explo.space/images/places/kennedy.jpg',
            imageAlt: 'Kennedy Space Center launch pad',
            site: '@explo_space',
            creator: '@nasa'
        );

        $this->assertEquals('https://explo.space/images/places/kennedy.jpg', $data->image);
        $this->assertEquals('Kennedy Space Center launch pad', $data->imageAlt);
        $this->assertEquals('@explo_space', $data->site);
        $this->assertEquals('@nasa', $data->creator);
    }

    public function test_optional_properties_default_to_null(): void
    {
        $data = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test description',
            url: 'https://example.com'
        );

        $this->assertNull($data->image);
        $this->assertNull($data->imageAlt);
        $this->assertNull($data->site);
        $this->assertNull($data->creator);
    }

    public function test_supports_summary_card_type(): void
    {
        $data = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test description',
            url: 'https://example.com'
        );

        $this->assertEquals('summary', $data->card);
    }

    public function test_supports_summary_large_image_card_type(): void
    {
        $data = new TwitterCardsData(
            card: 'summary_large_image',
            title: 'Test',
            description: 'Test description',
            url: 'https://example.com'
        );

        $this->assertEquals('summary_large_image', $data->card);
    }

    public function test_can_set_twitter_site_without_creator(): void
    {
        $data = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test description',
            url: 'https://example.com',
            site: '@explo_space'
        );

        $this->assertEquals('@explo_space', $data->site);
        $this->assertNull($data->creator);
    }

    public function test_can_set_twitter_creator_without_site(): void
    {
        $data = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test description',
            url: 'https://example.com',
            creator: '@author'
        );

        $this->assertEquals('@author', $data->creator);
        $this->assertNull($data->site);
    }

    public function test_all_properties_are_readonly(): void
    {
        $data = new TwitterCardsData(
            card: 'summary',
            title: 'Test',
            description: 'Test',
            url: 'https://example.com'
        );

        $reflection = new \ReflectionClass($data);
        $cardProperty = $reflection->getProperty('card');

        $this->assertTrue($cardProperty->isReadOnly());
    }
}
