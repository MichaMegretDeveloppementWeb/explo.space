<?php

namespace Tests\Unit\Repositories\Web\Place\PlaceRequest;

use App\Models\PlaceRequest;
use App\Models\PlaceRequestPhoto;
use App\Repositories\Web\Place\PlaceRequest\PlaceRequestCreateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceRequestCreateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlaceRequestCreateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PlaceRequestCreateRepository;
    }

    public function test_it_creates_a_place_request(): void
    {
        // Arrange
        $data = [
            'title' => 'Centre Spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
            'description' => 'Description du centre spatial',
            'practical_info' => 'Infos pratiques',
            'latitude' => 28.5721,
            'longitude' => -80.6480,
            'address' => 'Kennedy Space Center, FL, USA',
            'contact_email' => 'test@example.com',
            'detected_language' => 'fr',
            'status' => 'submitted',
        ];

        // Act
        $placeRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(PlaceRequest::class, $placeRequest);
        $this->assertDatabaseHas('place_requests', [
            'title' => 'Centre Spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
            'description' => 'Description du centre spatial',
            'practical_info' => 'Infos pratiques',
            'latitude' => 28.5721,
            'longitude' => -80.6480,
            'address' => 'Kennedy Space Center, FL, USA',
            'contact_email' => 'test@example.com',
            'detected_language' => 'fr',
            'status' => 'submitted',
        ]);
    }

    public function test_it_creates_a_place_request_with_minimal_data(): void
    {
        // Arrange
        $data = [
            'title' => 'Test Place',
            'slug' => 'test-place',
            'description' => null,
            'practical_info' => null,
            'latitude' => null,
            'longitude' => null,
            'address' => null,
            'contact_email' => 'test@example.com',
            'detected_language' => 'unknown',
            'status' => 'submitted',
        ];

        // Act
        $placeRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(PlaceRequest::class, $placeRequest);
        $this->assertEquals('Test Place', $placeRequest->title);
        $this->assertEquals('test-place', $placeRequest->slug);
        $this->assertNull($placeRequest->description);
        $this->assertNull($placeRequest->latitude);
        $this->assertEquals('unknown', $placeRequest->detected_language);
    }

    public function test_it_creates_a_photo_for_a_place_request(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();

        $photoData = [
            'filename' => 'test-photo.jpg',
            'original_name' => 'original-photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024000,
            'sort_order' => 0,
        ];

        // Act
        $photo = $this->repository->createPhoto($placeRequest, $photoData);

        // Assert
        $this->assertInstanceOf(PlaceRequestPhoto::class, $photo);
        $this->assertDatabaseHas('place_request_photos', [
            'place_request_id' => $placeRequest->id,
            'filename' => 'test-photo.jpg',
            'original_name' => 'original-photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024000,
            'sort_order' => 0,
        ]);
        $this->assertEquals($placeRequest->id, $photo->place_request_id);
    }

    public function test_it_creates_multiple_photos_with_correct_sort_order(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();

        // Act
        $photo1 = $this->repository->createPhoto($placeRequest, [
            'filename' => 'photo1.jpg',
            'original_name' => 'original1.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1000000,
            'sort_order' => 0,
        ]);

        $photo2 = $this->repository->createPhoto($placeRequest, [
            'filename' => 'photo2.jpg',
            'original_name' => 'original2.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1500000,
            'sort_order' => 1,
        ]);

        $photo3 = $this->repository->createPhoto($placeRequest, [
            'filename' => 'photo3.jpg',
            'original_name' => 'original3.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 2000000,
            'sort_order' => 2,
        ]);

        // Assert
        $this->assertEquals(0, $photo1->sort_order);
        $this->assertEquals(1, $photo2->sort_order);
        $this->assertEquals(2, $photo3->sort_order);
        $this->assertCount(3, $placeRequest->fresh()->photos);
    }

    public function test_it_returns_fresh_instance_with_relations(): void
    {
        // Arrange
        $data = [
            'title' => 'Test',
            'slug' => 'test',
            'contact_email' => 'test@example.com',
            'detected_language' => 'fr',
            'status' => 'submitted',
        ];

        // Act
        $placeRequest = $this->repository->create($data);

        // Assert
        $this->assertTrue($placeRequest->exists);
        $this->assertNotNull($placeRequest->id);
        $this->assertNotNull($placeRequest->created_at);
        $this->assertNotNull($placeRequest->updated_at);
    }

    public function test_it_stores_coordinates_with_correct_precision(): void
    {
        // Arrange
        $data = [
            'title' => 'Test',
            'slug' => 'test',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'contact_email' => 'test@example.com',
            'detected_language' => 'fr',
            'status' => 'submitted',
        ];

        // Act
        $placeRequest = $this->repository->create($data);

        // Assert
        $this->assertEquals(48.8566, $placeRequest->latitude);
        $this->assertEquals(2.3522, $placeRequest->longitude);
    }
}
