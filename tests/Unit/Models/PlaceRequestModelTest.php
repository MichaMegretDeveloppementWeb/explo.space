<?php

namespace Tests\Unit\Models;

use App\Models\PlaceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceRequestModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_request_has_required_relations(): void
    {
        $viewedByAdmin = User::factory()->create(['role' => 'admin']);
        $processedByAdmin = User::factory()->create(['role' => 'super_admin']);
        $placeRequest = PlaceRequest::factory()->create([
            'contact_email' => 'test@example.com',
            'viewed_by_admin_id' => $viewedByAdmin->id,
            'processed_by_admin_id' => $processedByAdmin->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $placeRequest->viewedByAdmin());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $placeRequest->processedByAdmin());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $placeRequest->place());
    }

    public function test_place_request_status_helpers(): void
    {
        $submittedRequest = PlaceRequest::factory()->create([
            'contact_email' => 'test1@example.com',
            'status' => 'submitted',
        ]);
        $this->assertTrue($submittedRequest->isSubmitted());
        $this->assertFalse($submittedRequest->isPending());
        $this->assertFalse($submittedRequest->isAccepted());
        $this->assertFalse($submittedRequest->isRefused());

        $pendingRequest = PlaceRequest::factory()->create([
            'contact_email' => 'test2@example.com',
            'status' => 'pending',
        ]);
        $this->assertTrue($pendingRequest->isPending());
        $this->assertFalse($pendingRequest->isSubmitted());

        $acceptedRequest = PlaceRequest::factory()->create([
            'contact_email' => 'test3@example.com',
            'status' => 'accepted',
        ]);
        $this->assertTrue($acceptedRequest->isAccepted());

        $refusedRequest = PlaceRequest::factory()->create([
            'contact_email' => 'test4@example.com',
            'status' => 'refused',
        ]);
        $this->assertTrue($refusedRequest->isRefused());
    }

    public function test_place_request_casts_correctly(): void
    {
        $placeRequest = PlaceRequest::factory()->create([
            'contact_email' => 'test@example.com',
            'latitude' => 28.5721022,
            'longitude' => -80.6480131,
        ]);

        $this->assertIsNumeric($placeRequest->latitude);
        $this->assertIsNumeric($placeRequest->longitude);
        $this->assertEquals('test@example.com', $placeRequest->contact_email);
    }

    public function test_place_request_requires_contact_email(): void
    {
        $placeRequest = PlaceRequest::factory()->create([
            'contact_email' => 'visitor@example.com',
        ]);

        $this->assertNotEmpty($placeRequest->contact_email);
        $this->assertStringContainsString('@', $placeRequest->contact_email);
    }
}
