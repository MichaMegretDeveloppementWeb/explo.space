<?php

namespace Tests\Unit\Models;

use App\Models\EditRequest;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_request_has_required_relations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewedByAdmin = User::factory()->create(['role' => 'admin']);
        $processedByAdmin = User::factory()->create(['role' => 'super_admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $editRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
            'place_id' => $place->id,
            'viewed_by_admin_id' => $viewedByAdmin->id,
            'processed_by_admin_id' => $processedByAdmin->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $editRequest->place());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $editRequest->viewedByAdmin());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $editRequest->processedByAdmin());
    }

    public function test_edit_request_type_helpers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $modificationRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor1@example.com',
            'place_id' => $place->id,
            'type' => 'modification',
        ]);
        $this->assertTrue($modificationRequest->isModification());
        $this->assertFalse($modificationRequest->isSignalement());

        $signalementRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor2@example.com',
            'place_id' => $place->id,
            'type' => 'signalement',
        ]);
        $this->assertTrue($signalementRequest->isSignalement());
        $this->assertFalse($signalementRequest->isModification());
    }

    public function test_edit_request_status_helpers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $submittedRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor1@example.com',
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);
        $this->assertTrue($submittedRequest->isSubmitted());
        $this->assertFalse($submittedRequest->isAccepted());

        $acceptedRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor2@example.com',
            'place_id' => $place->id,
            'status' => 'accepted',
        ]);
        $this->assertTrue($acceptedRequest->isAccepted());
        $this->assertFalse($acceptedRequest->isSubmitted());
    }

    public function test_edit_request_casts_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $editRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
            'place_id' => $place->id,
            'suggested_changes' => ['title' => 'New Title', 'description' => 'New Description'],
        ]);

        $this->assertIsArray($editRequest->suggested_changes);
        $this->assertEquals(['title' => 'New Title', 'description' => 'New Description'], $editRequest->suggested_changes);
        $this->assertEquals('visitor@example.com', $editRequest->contact_email);
    }

    public function test_edit_request_handles_photo_suggestion_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $photoSuggestion = EditRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'suggested_changes' => ['photos' => ['photo1.jpg', 'photo2.jpg']],
        ]);

        $this->assertEquals('photo_suggestion', $photoSuggestion->type);
        $this->assertFalse($photoSuggestion->isModification());
        $this->assertFalse($photoSuggestion->isSignalement());
        $this->assertArrayHasKey('photos', $photoSuggestion->suggested_changes);
    }

    public function test_edit_request_additional_status_helpers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $pendingRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor1@example.com',
            'place_id' => $place->id,
            'status' => 'pending',
        ]);
        $this->assertTrue($pendingRequest->isPending());
        $this->assertFalse($pendingRequest->isAccepted());

        $refusedRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor2@example.com',
            'place_id' => $place->id,
            'status' => 'refused',
        ]);
        $this->assertTrue($refusedRequest->isRefused());
        $this->assertFalse($refusedRequest->isAccepted());
    }

    public function test_edit_request_stores_detected_language(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $editRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
            'place_id' => $place->id,
            'detected_language' => 'fr',
        ]);

        $this->assertEquals('fr', $editRequest->detected_language);

        $unknownLanguage = EditRequest::factory()->create([
            'contact_email' => 'visitor2@example.com',
            'place_id' => $place->id,
            'detected_language' => 'unknown',
        ]);

        $this->assertEquals('unknown', $unknownLanguage->detected_language);
    }

    public function test_edit_request_casts_status_to_enum(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $editRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $this->assertInstanceOf(\App\Enums\RequestStatus::class, $editRequest->status);
        $this->assertEquals(\App\Enums\RequestStatus::Submitted, $editRequest->status);
    }

    public function test_edit_request_stores_timestamps(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewedByAdmin = User::factory()->create(['role' => 'admin']);
        $processedByAdmin = User::factory()->create(['role' => 'super_admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $viewedAt = now()->subHours(2);
        $processedAt = now()->subHour();

        $editRequest = EditRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
            'place_id' => $place->id,
            'viewed_by_admin_id' => $viewedByAdmin->id,
            'viewed_at' => $viewedAt,
            'processed_by_admin_id' => $processedByAdmin->id,
            'processed_at' => $processedAt,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $editRequest->viewed_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $editRequest->processed_at);
        $this->assertEquals($viewedAt->timestamp, $editRequest->viewed_at->timestamp);
        $this->assertEquals($processedAt->timestamp, $editRequest->processed_at->timestamp);
    }
}
