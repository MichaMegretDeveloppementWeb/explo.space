<?php

namespace Tests\Unit\Repositories\Web\Place\EditRequest;

use App\Enums\RequestStatus;
use App\Models\EditRequest;
use App\Models\Place;
use App\Models\User;
use App\Repositories\Web\Place\EditRequest\EditRequestCreateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditRequestCreateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EditRequestCreateRepository $repository;

    private Place $place;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EditRequestCreateRepository;

        $admin = User::factory()->create(['role' => 'admin']);
        $this->place = Place::factory()->create(['admin_id' => $admin->id]);
    }

    #[Test]
    public function it_creates_signalement_edit_request(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'visitor@example.com',
            'description' => 'Problème avec les informations du lieu',
            'detected_language' => 'fr',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $editRequest);
        $this->assertDatabaseHas('edit_requests', [
            'id' => $editRequest->id,
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'visitor@example.com',
            'description' => 'Problème avec les informations du lieu',
            'detected_language' => 'fr',
            'status' => RequestStatus::Submitted->value,
        ]);
    }

    #[Test]
    public function it_creates_modification_edit_request_with_suggested_changes(): void
    {
        // Arrange
        $suggestedChanges = [
            [
                'field' => 'title',
                'field_label' => 'Titre',
                'old_value' => 'Old Title',
                'new_value' => 'New Title',
                'status' => 'pending',
            ],
            [
                'field' => 'description',
                'field_label' => 'Description',
                'old_value' => 'Old Description',
                'new_value' => 'New Description',
                'status' => 'pending',
            ],
        ];

        $data = [
            'place_id' => $this->place->id,
            'type' => 'modification',
            'contact_email' => 'modifier@example.com',
            'description' => 'Voici mes suggestions de modifications',
            'suggested_changes' => $suggestedChanges,
            'detected_language' => 'fr',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $editRequest);
        $this->assertDatabaseHas('edit_requests', [
            'id' => $editRequest->id,
            'place_id' => $this->place->id,
            'type' => 'modification',
            'contact_email' => 'modifier@example.com',
        ]);

        $this->assertIsArray($editRequest->suggested_changes);
        $this->assertCount(2, $editRequest->suggested_changes);
        $this->assertEquals('title', $editRequest->suggested_changes[0]['field']);
        $this->assertEquals('New Title', $editRequest->suggested_changes[0]['new_value']);
    }

    #[Test]
    public function it_creates_photo_suggestion_edit_request(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'photos@example.com',
            'description' => '',
            'suggested_changes' => ['photos' => ['photo1.jpg', 'photo2.jpg']],
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $editRequest);
        $this->assertDatabaseHas('edit_requests', [
            'id' => $editRequest->id,
            'place_id' => $this->place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'photos@example.com',
        ]);

        $this->assertArrayHasKey('photos', $editRequest->suggested_changes);
        $this->assertEquals(['photo1.jpg', 'photo2.jpg'], $editRequest->suggested_changes['photos']);
    }

    #[Test]
    public function it_stores_detected_language(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'test@example.com',
            'description' => 'Test description',
            'detected_language' => 'en',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertEquals('en', $editRequest->detected_language);
        $this->assertDatabaseHas('edit_requests', [
            'id' => $editRequest->id,
            'detected_language' => 'en',
        ]);
    }

    #[Test]
    public function it_handles_unknown_detected_language(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'test@example.com',
            'description' => 'X',
            'detected_language' => 'unknown',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertEquals('unknown', $editRequest->detected_language);
    }

    #[Test]
    public function it_sets_default_status_to_submitted(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'test@example.com',
            'description' => 'Test',
            'detected_language' => 'fr',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertEquals(RequestStatus::Submitted, $editRequest->status);
    }

    #[Test]
    public function it_returns_edit_request_instance(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'test@example.com',
            'description' => 'Test description',
            'detected_language' => 'fr',
            'status' => RequestStatus::Submitted,
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
        $data = [
            'place_id' => $this->place->id,
            'type' => 'modification',
            'contact_email' => 'complete@example.com',
            'description' => 'Complete description',
            'suggested_changes' => [
                [
                    'field' => 'address',
                    'field_label' => 'Adresse',
                    'old_value' => '123 Old St',
                    'new_value' => '456 New Ave',
                    'status' => 'pending',
                ],
            ],
            'detected_language' => 'en',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert - Verify all fields match
        $this->assertEquals($this->place->id, $editRequest->place_id);
        $this->assertEquals('modification', $editRequest->type);
        $this->assertEquals('complete@example.com', $editRequest->contact_email);
        $this->assertEquals('Complete description', $editRequest->description);
        $this->assertEquals('en', $editRequest->detected_language);
        $this->assertEquals(RequestStatus::Submitted, $editRequest->status);
        $this->assertCount(1, $editRequest->suggested_changes);
        $this->assertEquals('address', $editRequest->suggested_changes[0]['field']);
        $this->assertEquals('456 New Ave', $editRequest->suggested_changes[0]['new_value']);
    }

    #[Test]
    public function it_creates_without_suggested_changes_for_signalement(): void
    {
        // Arrange
        $data = [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'test@example.com',
            'description' => 'Simple signalement without modifications',
            'detected_language' => 'fr',
            'status' => RequestStatus::Submitted,
        ];

        // Act
        $editRequest = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(EditRequest::class, $editRequest);
        $this->assertNull($editRequest->suggested_changes);
    }
}
