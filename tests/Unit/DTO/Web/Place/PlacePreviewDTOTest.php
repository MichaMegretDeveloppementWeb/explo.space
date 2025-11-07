<?php

namespace Tests\Unit\DTO\Web\Place;

use App\DTO\Web\Place\PlacePreviewDTO;
use Tests\TestCase;

class PlacePreviewDTOTest extends TestCase
{
    public function test_dto_can_be_instantiated_with_all_properties(): void
    {
        $dto = new PlacePreviewDTO(
            id: 1,
            slug: 'test-place',
            title: 'Test Place',
            descriptionExcerpt: 'This is a test description that is exactly 200 characters long...',
            mainPhotoUrl: 'https://example.com/photo.jpg',
            isFeatured: true,
            tags: [
                ['name' => 'NASA', 'slug' => 'nasa', 'color' => '#FF0000'],
                ['name' => 'SpaceX', 'slug' => 'spacex', 'color' => '#0000FF'],
            ]
        );

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('test-place', $dto->slug);
        $this->assertEquals('Test Place', $dto->title);
        $this->assertEquals('This is a test description that is exactly 200 characters long...', $dto->descriptionExcerpt);
        $this->assertEquals('https://example.com/photo.jpg', $dto->mainPhotoUrl);
        $this->assertTrue($dto->isFeatured);
        $this->assertCount(2, $dto->tags);
    }

    public function test_dto_can_be_instantiated_with_null_photo(): void
    {
        $dto = new PlacePreviewDTO(
            id: 1,
            slug: 'test-place',
            title: 'Test Place',
            descriptionExcerpt: 'Test description',
            mainPhotoUrl: null,
            isFeatured: false,
            tags: []
        );

        $this->assertNull($dto->mainPhotoUrl);
        $this->assertFalse($dto->isFeatured);
    }

    public function test_dto_can_be_instantiated_with_empty_tags(): void
    {
        $dto = new PlacePreviewDTO(
            id: 1,
            slug: 'test-place',
            title: 'Test Place',
            descriptionExcerpt: 'Test description',
            mainPhotoUrl: 'https://example.com/photo.jpg',
            isFeatured: false,
            tags: []
        );

        $this->assertEmpty($dto->tags);
    }

    public function test_dto_is_readonly(): void
    {
        $dto = new PlacePreviewDTO(
            id: 1,
            slug: 'test-place',
            title: 'Test Place',
            descriptionExcerpt: 'Test description',
            mainPhotoUrl: null,
            isFeatured: false,
            tags: []
        );

        $this->expectException(\Error::class);
        $dto->id = 2; // Should fail because DTO is readonly
    }

    public function test_to_livewire_serializes_correctly(): void
    {
        $dto = new PlacePreviewDTO(
            id: 1,
            slug: 'test-place',
            title: 'Test Place',
            descriptionExcerpt: 'Test description',
            mainPhotoUrl: 'https://example.com/photo.jpg',
            isFeatured: true,
            tags: [
                ['name' => 'NASA', 'slug' => 'nasa', 'color' => '#FF0000'],
            ]
        );

        $serialized = $dto->toLivewire();

        $this->assertIsArray($serialized);
        $this->assertArrayHasKey('id', $serialized);
        $this->assertArrayHasKey('slug', $serialized);
        $this->assertArrayHasKey('title', $serialized);
        $this->assertArrayHasKey('descriptionExcerpt', $serialized);
        $this->assertArrayHasKey('mainPhotoUrl', $serialized);
        $this->assertArrayHasKey('isFeatured', $serialized);
        $this->assertArrayHasKey('tags', $serialized);

        $this->assertEquals(1, $serialized['id']);
        $this->assertEquals('test-place', $serialized['slug']);
        $this->assertEquals('Test Place', $serialized['title']);
        $this->assertEquals('Test description', $serialized['descriptionExcerpt']);
        $this->assertEquals('https://example.com/photo.jpg', $serialized['mainPhotoUrl']);
        $this->assertTrue($serialized['isFeatured']);
        $this->assertCount(1, $serialized['tags']);
    }

    public function test_from_livewire_deserializes_correctly(): void
    {
        $data = [
            'id' => 1,
            'slug' => 'test-place',
            'title' => 'Test Place',
            'descriptionExcerpt' => 'Test description',
            'mainPhotoUrl' => 'https://example.com/photo.jpg',
            'isFeatured' => false,
            'tags' => [
                ['name' => 'NASA', 'slug' => 'nasa', 'color' => '#FF0000'],
            ],
        ];

        $dto = PlacePreviewDTO::fromLivewire($data);

        $this->assertInstanceOf(PlacePreviewDTO::class, $dto);
        $this->assertEquals(1, $dto->id);
        $this->assertEquals('test-place', $dto->slug);
        $this->assertEquals('Test Place', $dto->title);
        $this->assertEquals('Test description', $dto->descriptionExcerpt);
        $this->assertEquals('https://example.com/photo.jpg', $dto->mainPhotoUrl);
        $this->assertFalse($dto->isFeatured);
        $this->assertCount(1, $dto->tags);
    }

    public function test_from_livewire_handles_missing_tags(): void
    {
        $data = [
            'id' => 1,
            'slug' => 'test-place',
            'title' => 'Test Place',
            'descriptionExcerpt' => 'Test description',
            'mainPhotoUrl' => null,
            'isFeatured' => false,
            // tags is missing intentionally
        ];

        $dto = PlacePreviewDTO::fromLivewire($data);

        $this->assertEmpty($dto->tags);
    }

    public function test_serialization_roundtrip_maintains_data(): void
    {
        $original = new PlacePreviewDTO(
            id: 42,
            slug: 'kennedy-space-center',
            title: 'Kennedy Space Center',
            descriptionExcerpt: 'The Kennedy Space Center is one of the most famous space launch facilities...',
            mainPhotoUrl: 'https://example.com/ksc.jpg',
            isFeatured: true,
            tags: [
                ['name' => 'NASA', 'slug' => 'nasa', 'color' => '#FF0000'],
                ['name' => 'USA', 'slug' => 'usa', 'color' => '#0000FF'],
            ]
        );

        $serialized = $original->toLivewire();
        $deserialized = PlacePreviewDTO::fromLivewire($serialized);

        $this->assertEquals($original->id, $deserialized->id);
        $this->assertEquals($original->slug, $deserialized->slug);
        $this->assertEquals($original->title, $deserialized->title);
        $this->assertEquals($original->descriptionExcerpt, $deserialized->descriptionExcerpt);
        $this->assertEquals($original->mainPhotoUrl, $deserialized->mainPhotoUrl);
        $this->assertEquals($original->isFeatured, $deserialized->isFeatured);
        $this->assertEquals($original->tags, $deserialized->tags);
    }
}
