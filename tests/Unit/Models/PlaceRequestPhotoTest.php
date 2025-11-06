<?php

namespace Tests\Unit\Models;

use App\Models\PlaceRequest;
use App\Models\PlaceRequestPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceRequestPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_a_place_request(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();
        $photo = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
        ]);

        // Act
        $relation = $photo->placeRequest;

        // Assert
        $this->assertInstanceOf(PlaceRequest::class, $relation);
        $this->assertEquals($placeRequest->id, $relation->id);
    }

    public function test_it_has_fillable_attributes(): void
    {
        // Arrange
        $attributes = [
            'place_request_id' => 1,
            'filename' => 'test-photo.jpg',
            'original_name' => 'original.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024000,
            'sort_order' => 0,
        ];

        // Act
        $photo = new PlaceRequestPhoto($attributes);

        // Assert
        $this->assertEquals(1, $photo->place_request_id);
        $this->assertEquals('test-photo.jpg', $photo->filename);
        $this->assertEquals('original.jpg', $photo->original_name);
        $this->assertEquals('image/jpeg', $photo->mime_type);
        $this->assertEquals(1024000, $photo->size);
        $this->assertEquals(0, $photo->sort_order);
    }

    public function test_it_has_url_accessor(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create(['id' => 123]);
        $photo = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'filename' => 'test-photo.jpg',
        ]);

        // Act
        $url = $photo->url;

        // Assert
        // URL format: /storage/photos/place-requests/{place_request_id}/{filename}
        $this->assertStringContainsString('123', $url);
        $this->assertStringContainsString('test-photo.jpg', $url);
        $this->assertStringContainsString('place-requests', $url);
    }

    public function test_it_can_be_ordered_by_sort_order(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();

        $photo3 = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'sort_order' => 2,
        ]);

        $photo1 = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'sort_order' => 0,
        ]);

        $photo2 = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'sort_order' => 1,
        ]);

        // Act
        $orderedPhotos = PlaceRequestPhoto::where('place_request_id', $placeRequest->id)
            ->orderBy('sort_order')
            ->get();

        // Assert
        $this->assertEquals($photo1->id, $orderedPhotos[0]->id);
        $this->assertEquals($photo2->id, $orderedPhotos[1]->id);
        $this->assertEquals($photo3->id, $orderedPhotos[2]->id);
    }

    public function test_it_stores_file_size_in_bytes(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();
        $sizeInBytes = 2048000; // 2MB

        // Act
        $photo = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'size' => $sizeInBytes,
        ]);

        // Assert
        $this->assertEquals(2048000, $photo->size);
    }

    public function test_it_stores_mime_type(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();

        // Act
        $photo = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'mime_type' => 'image/png',
        ]);

        // Assert
        $this->assertEquals('image/png', $photo->mime_type);
    }

    public function test_it_preserves_original_filename(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();
        $originalName = 'My Amazing Space Photo 2024.jpg';

        // Act
        $photo = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
            'original_name' => $originalName,
        ]);

        // Assert
        $this->assertEquals($originalName, $photo->original_name);
    }

    public function test_it_can_have_multiple_photos_per_place_request(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();

        // Act
        PlaceRequestPhoto::factory()->count(5)->create([
            'place_request_id' => $placeRequest->id,
        ]);

        // Assert
        $this->assertCount(5, $placeRequest->fresh()->photos);
    }

    public function test_it_cascades_delete_with_place_request(): void
    {
        // Arrange
        $placeRequest = PlaceRequest::factory()->create();
        $photo = PlaceRequestPhoto::factory()->create([
            'place_request_id' => $placeRequest->id,
        ]);

        // Act
        $placeRequest->delete();

        // Assert
        $this->assertDatabaseMissing('place_request_photos', [
            'id' => $photo->id,
        ]);
    }
}
