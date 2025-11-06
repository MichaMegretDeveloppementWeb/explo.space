<?php

namespace Tests\Unit\DTO\Web\Place;

use App\DTO\Web\Place\PlaceDetailDTO;
use Tests\TestCase;

class PlaceDetailDTOTest extends TestCase
{
    public function test_dto_can_be_created_with_all_properties(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'centre-spatial-kennedy',
            title: 'Centre spatial Kennedy',
            description: 'Description complète',
            practicalInfo: 'Horaires: 9h-17h',
            latitude: 28.5728,
            longitude: -80.6490,
            address: 'Kennedy Space Center, FL 32899, USA',
            tags: [['name' => 'NASA', 'slug' => 'nasa', 'color' => '#FF0000']],
            photos: [['url' => 'https://example.com/photo.jpg', 'medium_url' => 'https://example.com/photo-medium.jpg']],
            mainPhotoUrl: 'https://example.com/photo.jpg',
            createdAt: '15 janvier 2025',
            updatedAt: '16 janvier 2025',
        );

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('centre-spatial-kennedy', $dto->slug);
        $this->assertEquals('Centre spatial Kennedy', $dto->title);
        $this->assertEquals('Description complète', $dto->description);
        $this->assertEquals('Horaires: 9h-17h', $dto->practicalInfo);
        $this->assertEquals(28.5728, $dto->latitude);
        $this->assertEquals(-80.6490, $dto->longitude);
        $this->assertEquals('Kennedy Space Center, FL 32899, USA', $dto->address);
        $this->assertCount(1, $dto->tags);
        $this->assertCount(1, $dto->photos);
        $this->assertEquals('https://example.com/photo.jpg', $dto->mainPhotoUrl);
        $this->assertEquals('15 janvier 2025', $dto->createdAt);
        $this->assertEquals('16 janvier 2025', $dto->updatedAt);
    }

    public function test_dto_can_be_created_with_null_optional_fields(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'test',
            title: 'Test',
            description: 'Test description',
            practicalInfo: null,
            latitude: 0.0,
            longitude: 0.0,
            address: 'Test address',
            tags: [],
            photos: [],
            mainPhotoUrl: null,
            createdAt: '01 janvier 2025',
            updatedAt: '01 janvier 2025',
        );

        $this->assertNull($dto->practicalInfo);
        $this->assertNull($dto->mainPhotoUrl);
        $this->assertEmpty($dto->tags);
        $this->assertEmpty($dto->photos);
    }

    public function test_dto_properties_are_readonly(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'test',
            title: 'Test',
            description: 'Test',
            practicalInfo: null,
            latitude: 0.0,
            longitude: 0.0,
            address: 'Test',
            tags: [],
            photos: [],
            mainPhotoUrl: null,
            createdAt: '01 janvier 2025',
            updatedAt: '01 janvier 2025',
        );

        $this->expectException(\Error::class);
        $dto->title = 'Modified';
    }

    public function test_dto_tags_have_correct_structure(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'test',
            title: 'Test',
            description: 'Test',
            practicalInfo: null,
            latitude: 0.0,
            longitude: 0.0,
            address: 'Test',
            tags: [
                ['name' => 'NASA', 'slug' => 'nasa', 'color' => '#FF0000'],
                ['name' => 'SpaceX', 'slug' => 'spacex', 'color' => '#0000FF'],
            ],
            photos: [],
            mainPhotoUrl: null,
            createdAt: '01 janvier 2025',
            updatedAt: '01 janvier 2025',
        );

        $this->assertCount(2, $dto->tags);
        $this->assertArrayHasKey('name', $dto->tags[0]);
        $this->assertArrayHasKey('slug', $dto->tags[0]);
        $this->assertArrayHasKey('color', $dto->tags[0]);
    }

    public function test_dto_photos_have_correct_structure(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'test',
            title: 'Test',
            description: 'Test',
            practicalInfo: null,
            latitude: 0.0,
            longitude: 0.0,
            address: 'Test',
            tags: [],
            photos: [
                ['url' => 'https://example.com/1.jpg', 'medium_url' => 'https://example.com/1-medium.jpg'],
                ['url' => 'https://example.com/2.jpg', 'medium_url' => 'https://example.com/2-medium.jpg'],
            ],
            mainPhotoUrl: null,
            createdAt: '01 janvier 2025',
            updatedAt: '01 janvier 2025',
        );

        $this->assertCount(2, $dto->photos);
        $this->assertArrayHasKey('url', $dto->photos[0]);
        $this->assertArrayHasKey('medium_url', $dto->photos[0]);
    }

    public function test_dto_latitude_and_longitude_are_floats(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'test',
            title: 'Test',
            description: 'Test',
            practicalInfo: null,
            latitude: 28.5728,
            longitude: -80.6490,
            address: 'Test',
            tags: [],
            photos: [],
            mainPhotoUrl: null,
            createdAt: '01 janvier 2025',
            updatedAt: '01 janvier 2025',
        );

        $this->assertIsFloat($dto->latitude);
        $this->assertIsFloat($dto->longitude);
    }

    public function test_dto_dates_are_strings(): void
    {
        $dto = new PlaceDetailDTO(
            id: 1,
            slug: 'test',
            title: 'Test',
            description: 'Test',
            practicalInfo: null,
            latitude: 0.0,
            longitude: 0.0,
            address: 'Test',
            tags: [],
            photos: [],
            mainPhotoUrl: null,
            createdAt: '15 janvier 2025',
            updatedAt: '16 janvier 2025',
        );

        $this->assertIsString($dto->createdAt);
        $this->assertIsString($dto->updatedAt);
    }
}
