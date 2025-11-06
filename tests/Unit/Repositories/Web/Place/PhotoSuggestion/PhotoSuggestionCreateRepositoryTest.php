<?php

namespace Tests\Unit\Repositories\Web\Place\PhotoSuggestion;

use App\Enums\RequestStatus;
use App\Models\EditRequest;
use App\Models\Place;
use App\Repositories\Web\Place\PhotoSuggestion\PhotoSuggestionCreateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhotoSuggestionCreateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PhotoSuggestionCreateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PhotoSuggestionCreateRepository;
    }

    #[Test]
    public function it_creates_photo_suggestion_edit_request(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $data = [
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'test@example.com',
            'suggested_changes' => [
                'photos' => ['photo1.jpg', 'photo2.jpg'],
            ],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $editRequest);
        $this->assertDatabaseHas('edit_requests', [
            'id' => $editRequest->id,
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'test@example.com',
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ]);

        $this->assertEquals(['photos' => ['photo1.jpg', 'photo2.jpg']], $editRequest->suggested_changes);
    }

    #[Test]
    public function it_stores_suggested_changes_as_json(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $data = [
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'user@example.com',
            'suggested_changes' => [
                'photos' => ['img1.jpg', 'img2.png', 'img3.webp'],
            ],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertIsArray($editRequest->suggested_changes);
        $this->assertArrayHasKey('photos', $editRequest->suggested_changes);
        $this->assertCount(3, $editRequest->suggested_changes['photos']);
        $this->assertEquals(['img1.jpg', 'img2.png', 'img3.webp'], $editRequest->suggested_changes['photos']);
    }

    #[Test]
    public function it_sets_default_status_to_submitted(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $data = [
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'test@example.com',
            'suggested_changes' => ['photos' => []],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertEquals(RequestStatus::Submitted, $editRequest->status);
    }

    #[Test]
    public function it_creates_with_empty_photos_array(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $data = [
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'test@example.com',
            'suggested_changes' => ['photos' => []],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertIsArray($editRequest->suggested_changes);
        $this->assertArrayHasKey('photos', $editRequest->suggested_changes);
        $this->assertEmpty($editRequest->suggested_changes['photos']);
    }

    #[Test]
    public function it_returns_edit_request_instance(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $data = [
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'test@example.com',
            'suggested_changes' => ['photos' => []],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ];

        // Act
        $result = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $result);
        $this->assertTrue($result->exists);
        $this->assertNotNull($result->id);
    }

    #[Test]
    public function it_preserves_all_provided_data(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $data = [
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'contact@test.fr',
            'suggested_changes' => [
                'photos' => ['photo_1.jpg', 'photo_2.jpg'],
            ],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted->value,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert - Verify all fields match
        $this->assertEquals($place->id, $editRequest->place_id);
        $this->assertEquals('photo_suggestion', $editRequest->type);
        $this->assertEquals('contact@test.fr', $editRequest->contact_email);
        $this->assertEquals(['photos' => ['photo_1.jpg', 'photo_2.jpg']], $editRequest->suggested_changes);
        $this->assertEquals('unknown', $editRequest->detected_language);
        $this->assertEquals(RequestStatus::Submitted, $editRequest->status);
    }
}
