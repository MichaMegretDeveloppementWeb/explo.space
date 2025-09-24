<?php

namespace Tests\Unit\Models;

use App\Models\EditRequest;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditRequestModelTest extends TestCase
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
}
