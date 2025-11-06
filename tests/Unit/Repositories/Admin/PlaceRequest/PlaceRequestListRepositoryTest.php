<?php

namespace Tests\Unit\Repositories\Admin\PlaceRequest;

use App\Models\PlaceRequest;
use App\Models\PlaceRequestPhoto;
use App\Models\User;
use App\Repositories\Admin\PlaceRequest\PlaceRequestListRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceRequestListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PlaceRequestListRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PlaceRequestListRepository;
    }

    // ========================================
    // Basic Retrieval
    // ========================================

    public function test_get_paginated_place_requests_returns_paginator(): void
    {
        PlaceRequest::factory()->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_returns_all_when_no_filters(): void
    {
        PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'accepted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertEquals(3, $result->total());
    }

    // ========================================
    // Filtering by Status
    // ========================================

    public function test_filters_by_single_status(): void
    {
        PlaceRequest::factory()->create(['title' => 'Lieu 1', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Lieu 2', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Lieu 3', 'status' => 'accepted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['pending']],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertEquals(1, $result->total());
        $this->assertEquals('Lieu 1', $result->first()->title);
    }

    public function test_filters_by_multiple_statuses(): void
    {
        PlaceRequest::factory()->create(['title' => 'Lieu 1', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Lieu 2', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Lieu 3', 'status' => 'accepted']);
        PlaceRequest::factory()->create(['title' => 'Lieu 4', 'status' => 'refused']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['pending', 'submitted']],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertEquals(2, $result->total());
        $this->assertTrue($result->contains('title', 'Lieu 1'));
        $this->assertTrue($result->contains('title', 'Lieu 2'));
    }

    public function test_filters_by_all_statuses(): void
    {
        PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'accepted']);
        PlaceRequest::factory()->create(['status' => 'refused']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['submitted', 'pending', 'accepted', 'refused']],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertEquals(4, $result->total());
    }

    public function test_returns_empty_when_no_matching_status(): void
    {
        PlaceRequest::factory()->create(['status' => 'accepted']);
        PlaceRequest::factory()->create(['status' => 'refused']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['pending']],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertEquals(0, $result->total());
    }

    // ========================================
    // Sorting
    // ========================================

    public function test_sorts_by_title_ascending(): void
    {
        PlaceRequest::factory()->create(['title' => 'Zebra', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Alpha', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Beta', 'status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'title', 'sortDirection' => 'asc'],
            20
        );

        $items = $result->items();
        $this->assertEquals('Alpha', $items[0]->title);
        $this->assertEquals('Beta', $items[1]->title);
        $this->assertEquals('Zebra', $items[2]->title);
    }

    public function test_sorts_by_title_descending(): void
    {
        PlaceRequest::factory()->create(['title' => 'Zebra', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Alpha', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Beta', 'status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'title', 'sortDirection' => 'desc'],
            20
        );

        $items = $result->items();
        $this->assertEquals('Zebra', $items[0]->title);
        $this->assertEquals('Beta', $items[1]->title);
        $this->assertEquals('Alpha', $items[2]->title);
    }

    public function test_sorts_by_status_ascending(): void
    {
        // Create records with different statuses
        PlaceRequest::factory()->create([
            'status' => 'accepted',
            'created_at' => now()->subMinutes(3),
        ]);
        PlaceRequest::factory()->create([
            'status' => 'pending',
            'created_at' => now()->subMinutes(2),
        ]);
        PlaceRequest::factory()->create([
            'status' => 'submitted',
            'created_at' => now()->subMinutes(1),
        ]);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'status', 'sortDirection' => 'asc'],
            20
        );

        $items = $result->items();
        // ENUM sorts by definition order: ['submitted', 'pending', 'accepted', 'refused']
        // So ASC gives: submitted, pending, accepted
        $this->assertEquals(\App\Enums\RequestStatus::Submitted, $items[0]->status);
        $this->assertEquals(\App\Enums\RequestStatus::Pending, $items[1]->status);
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $items[2]->status);
    }

    public function test_sorts_by_created_at_descending(): void
    {
        $old = PlaceRequest::factory()->create(['status' => 'submitted']);
        $old->created_at = now()->subDays(2);
        $old->save();

        $medium = PlaceRequest::factory()->create(['status' => 'submitted']);
        $medium->created_at = now()->subDay();
        $medium->save();

        $recent = PlaceRequest::factory()->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $items = $result->items();
        $this->assertEquals($recent->id, $items[0]->id);
        $this->assertEquals($medium->id, $items[1]->id);
        $this->assertEquals($old->id, $items[2]->id);
    }

    public function test_sorts_by_created_at_ascending(): void
    {
        $old = PlaceRequest::factory()->create(['status' => 'submitted']);
        $old->created_at = now()->subDays(2);
        $old->save();

        $medium = PlaceRequest::factory()->create(['status' => 'submitted']);
        $medium->created_at = now()->subDay();
        $medium->save();

        $recent = PlaceRequest::factory()->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'asc'],
            20
        );

        $items = $result->items();
        $this->assertEquals($old->id, $items[0]->id);
        $this->assertEquals($medium->id, $items[1]->id);
        $this->assertEquals($recent->id, $items[2]->id);
    }

    public function test_defaults_to_created_at_for_invalid_sort_column(): void
    {
        $old = PlaceRequest::factory()->create(['status' => 'submitted']);
        $old->created_at = now()->subDays(2);
        $old->save();

        $recent = PlaceRequest::factory()->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'invalid_column', 'sortDirection' => 'desc'],
            20
        );

        $items = $result->items();
        $this->assertEquals($recent->id, $items[0]->id);
        $this->assertEquals($old->id, $items[1]->id);
    }

    // ========================================
    // Pagination
    // ========================================

    public function test_respects_per_page_parameter(): void
    {
        PlaceRequest::factory()->count(50)->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            30
        );

        $this->assertEquals(30, $result->count());
        $this->assertEquals(50, $result->total());
        $this->assertEquals(2, $result->lastPage());
    }

    public function test_paginates_results_correctly(): void
    {
        PlaceRequest::factory()->count(25)->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            10
        );

        $this->assertEquals(10, $result->count());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
    }

    // ========================================
    // Eager Loading (N+1 Prevention)
    // ========================================

    public function test_eager_loads_viewed_by_admin(): void
    {
        $viewer = User::factory()->create(['name' => 'Admin Viewer']);

        PlaceRequest::factory()->create([
            'status' => 'pending',
            'viewed_by_admin_id' => $viewer->id,
        ]);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $placeRequest = $result->first();
        $this->assertTrue($placeRequest->relationLoaded('viewedByAdmin'));
        $this->assertEquals('Admin Viewer', $placeRequest->viewedByAdmin->name);
    }

    public function test_eager_loads_processed_by_admin(): void
    {
        $processor = User::factory()->create(['name' => 'Admin Processor']);

        PlaceRequest::factory()->create([
            'status' => 'accepted',
            'processed_by_admin_id' => $processor->id,
        ]);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $placeRequest = $result->first();
        $this->assertTrue($placeRequest->relationLoaded('processedByAdmin'));
        $this->assertEquals('Admin Processor', $placeRequest->processedByAdmin->name);
    }

    public function test_eager_loads_photos(): void
    {
        $placeRequest = PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequestPhoto::factory()->count(3)->create(['place_request_id' => $placeRequest->id]);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $loadedRequest = $result->first();
        $this->assertTrue($loadedRequest->relationLoaded('photos'));
        $this->assertCount(3, $loadedRequest->photos);
    }

    public function test_handles_null_admin_relationships(): void
    {
        PlaceRequest::factory()->create([
            'status' => 'submitted',
            'viewed_by_admin_id' => null,
            'processed_by_admin_id' => null,
        ]);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $placeRequest = $result->first();
        $this->assertTrue($placeRequest->relationLoaded('viewedByAdmin'));
        $this->assertTrue($placeRequest->relationLoaded('processedByAdmin'));
        $this->assertNull($placeRequest->viewedByAdmin);
        $this->assertNull($placeRequest->processedByAdmin);
    }

    // ========================================
    // Combined Filters and Sorting
    // ========================================

    public function test_applies_filters_and_sorting_together(): void
    {
        PlaceRequest::factory()->create(['title' => 'Zebra', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Alpha', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Beta', 'status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['pending']],
            ['sortBy' => 'title', 'sortDirection' => 'asc'],
            20
        );

        $this->assertEquals(2, $result->total());
        $items = $result->items();
        $this->assertEquals('Alpha', $items[0]->title);
        $this->assertEquals('Zebra', $items[1]->title);
    }

    public function test_applies_multiple_status_filters_and_sorting(): void
    {
        PlaceRequest::factory()->create(['title' => 'Zebra', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Alpha', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Beta', 'status' => 'accepted']);
        PlaceRequest::factory()->create(['title' => 'Gamma', 'status' => 'pending']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['pending', 'submitted']],
            ['sortBy' => 'title', 'sortDirection' => 'asc'],
            20
        );

        $this->assertEquals(3, $result->total());
        $items = $result->items();
        $this->assertEquals('Alpha', $items[0]->title);
        $this->assertEquals('Gamma', $items[1]->title);
        $this->assertEquals('Zebra', $items[2]->title);
    }

    // ========================================
    // Edge Cases
    // ========================================

    public function test_returns_empty_when_no_data(): void
    {
        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => []],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        $this->assertEquals(0, $result->total());
    }

    public function test_handles_duplicate_statuses_in_filter(): void
    {
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'submitted']);

        $result = $this->repository->getPaginatedPlaceRequests(
            ['status' => ['pending', 'pending', 'submitted']],
            ['sortBy' => 'created_at', 'sortDirection' => 'desc'],
            20
        );

        // Should still return correct results despite duplicates
        $this->assertEquals(2, $result->total());
    }
}
