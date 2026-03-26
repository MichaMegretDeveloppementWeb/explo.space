<?php

namespace Tests\Unit\DTO\ImageSource;

use App\DTO\ImageSource\ImageResultData;
use PHPUnit\Framework\TestCase;

class ImageResultDataTest extends TestCase
{
    public function test_can_create_with_all_fields(): void
    {
        $data = new ImageResultData(
            url: 'https://example.com/image.jpg',
            source: 'wikimedia',
            license: 'CC-BY-SA 4.0',
            caption: 'Kennedy Space Center',
            width: 1200,
            height: 800,
        );

        $this->assertSame('https://example.com/image.jpg', $data->url);
        $this->assertSame('wikimedia', $data->source);
        $this->assertSame('CC-BY-SA 4.0', $data->license);
        $this->assertSame('Kennedy Space Center', $data->caption);
        $this->assertSame(1200, $data->width);
        $this->assertSame(800, $data->height);
    }

    public function test_can_create_with_minimal_fields(): void
    {
        $data = new ImageResultData(
            url: 'https://example.com/image.jpg',
            source: 'nasa',
            license: 'Public Domain',
        );

        $this->assertNull($data->caption);
        $this->assertNull($data->width);
        $this->assertNull($data->height);
    }

    public function test_from_array_with_all_fields(): void
    {
        $data = ImageResultData::fromArray([
            'url' => 'https://example.com/image.jpg',
            'source' => 'unsplash',
            'license' => 'Unsplash License',
            'caption' => 'A rocket launch',
            'width' => 4000,
            'height' => 3000,
        ]);

        $this->assertSame('https://example.com/image.jpg', $data->url);
        $this->assertSame('unsplash', $data->source);
        $this->assertSame('Unsplash License', $data->license);
        $this->assertSame('A rocket launch', $data->caption);
        $this->assertSame(4000, $data->width);
        $this->assertSame(3000, $data->height);
    }

    public function test_from_array_with_minimal_fields(): void
    {
        $data = ImageResultData::fromArray([
            'url' => 'https://example.com/image.jpg',
            'source' => 'pexels',
            'license' => 'Pexels License',
        ]);

        $this->assertNull($data->caption);
        $this->assertNull($data->width);
        $this->assertNull($data->height);
    }

    public function test_to_array(): void
    {
        $data = new ImageResultData(
            url: 'https://example.com/image.jpg',
            source: 'wikimedia',
            license: 'CC0',
            caption: 'Space Shuttle',
            width: 800,
            height: 600,
        );

        $array = $data->toArray();

        $this->assertSame([
            'url' => 'https://example.com/image.jpg',
            'source' => 'wikimedia',
            'license' => 'CC0',
            'caption' => 'Space Shuttle',
            'width' => 800,
            'height' => 600,
        ], $array);
    }

    public function test_to_array_with_null_optionals(): void
    {
        $data = new ImageResultData(
            url: 'https://example.com/image.jpg',
            source: 'nasa',
            license: 'Public Domain',
        );

        $array = $data->toArray();

        $this->assertNull($array['caption']);
        $this->assertNull($array['width']);
        $this->assertNull($array['height']);
    }
}
