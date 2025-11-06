<?php

namespace Tests\Unit\Repositories\Admin\Dashboard;

use App\Models\Category;
use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceRequest;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\Admin\Dashboard\DashboardStatsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardStatsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new DashboardStatsRepository;
    }

    public function test_get_total_places_returns_correct_count(): void
    {
        // Arrange
        Place::factory()->count(5)->create();

        // Act
        $count = $this->repository->getTotalPlaces();

        // Assert
        $this->assertEquals(5, $count);
    }

    public function test_get_total_places_returns_zero_when_no_places(): void
    {
        // Act
        $count = $this->repository->getTotalPlaces();

        // Assert
        $this->assertEquals(0, $count);
    }

    public function test_get_pending_place_requests_returns_correct_count(): void
    {
        // Arrange
        PlaceRequest::factory()->count(3)->create(['status' => 'submitted']);
        PlaceRequest::factory()->count(2)->create(['status' => 'pending']);
        PlaceRequest::factory()->count(2)->create(['status' => 'accepted']);
        PlaceRequest::factory()->count(1)->create(['status' => 'refused']);

        // Act
        $count = $this->repository->getPendingPlaceRequests();

        // Assert
        $this->assertEquals(5, $count); // submitted + pending
    }

    public function test_get_pending_edit_requests_returns_correct_count(): void
    {
        // Arrange
        $place = Place::factory()->create();

        EditRequest::factory()->count(2)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);
        EditRequest::factory()->count(3)->create([
            'place_id' => $place->id,
            'status' => 'pending',
        ]);
        EditRequest::factory()->count(1)->create([
            'place_id' => $place->id,
            'status' => 'accepted',
        ]);

        // Act
        $count = $this->repository->getPendingEditRequests();

        // Assert
        $this->assertEquals(5, $count); // submitted + pending
    }

    public function test_get_total_tags_returns_only_active_tags(): void
    {
        // Arrange
        Tag::factory()->count(4)->create(['is_active' => true]);
        Tag::factory()->count(2)->create(['is_active' => false]);

        // Act
        $count = $this->repository->getTotalTags();

        // Assert
        $this->assertEquals(4, $count);
    }

    public function test_get_total_categories_returns_only_active_categories(): void
    {
        // Arrange
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->count(1)->create(['is_active' => false]);

        // Act
        $count = $this->repository->getTotalCategories();

        // Assert
        $this->assertEquals(3, $count);
    }

    public function test_get_recent_place_requests_returns_correct_limit(): void
    {
        // Arrange
        PlaceRequest::factory()->count(10)->create();

        // Act
        $requests = $this->repository->getRecentPlaceRequests(5);

        // Assert
        $this->assertCount(5, $requests);
    }

    public function test_get_recent_place_requests_returns_most_recent_first(): void
    {
        // Arrange
        $oldest = PlaceRequest::factory()->create(['created_at' => now()->subDays(3)]);
        $middle = PlaceRequest::factory()->create(['created_at' => now()->subDays(2)]);
        $newest = PlaceRequest::factory()->create(['created_at' => now()->subDay()]);

        // Act
        $requests = $this->repository->getRecentPlaceRequests(3);

        // Assert
        $this->assertEquals($newest->id, $requests->first()->id);
        $this->assertEquals($oldest->id, $requests->last()->id);
    }

    public function test_get_recent_place_requests_eager_loads_admin_relationship(): void
    {
        // Arrange
        $admin = User::factory()->create();
        PlaceRequest::factory()->create([
            'status' => 'pending', // Le repository filtre sur Submitted ou Pending
            'processed_by_admin_id' => $admin->id,
        ]);

        // Act
        $requests = $this->repository->getRecentPlaceRequests(5);

        // Assert
        $this->assertNotEmpty($requests, 'The requests collection should not be empty');
        $this->assertTrue($requests->first()->relationLoaded('processedByAdmin'));
    }

    public function test_get_recent_places_returns_correct_limit(): void
    {
        // Arrange
        Place::factory()->count(8)->create();

        // Act
        $places = $this->repository->getRecentPlaces(5);

        // Assert
        $this->assertCount(5, $places);
    }

    public function test_get_recent_places_returns_most_recent_first(): void
    {
        // Arrange
        $oldest = Place::factory()->create(['created_at' => now()->subDays(3)]);
        $middle = Place::factory()->create(['created_at' => now()->subDays(2)]);
        $newest = Place::factory()->create(['created_at' => now()->subDay()]);

        // Act
        $places = $this->repository->getRecentPlaces(3);

        // Assert
        $this->assertEquals($newest->id, $places->first()->id);
        $this->assertEquals($oldest->id, $places->last()->id);
    }

    public function test_get_recent_places_eager_loads_relationships(): void
    {
        // Arrange
        Place::factory()->create();

        // Act
        $places = $this->repository->getRecentPlaces(5);

        // Assert
        $this->assertTrue($places->first()->relationLoaded('translations'));
        $this->assertTrue($places->first()->relationLoaded('admin'));
    }

    public function test_get_recent_edit_requests_returns_correct_limit(): void
    {
        // Arrange
        $place = Place::factory()->create();
        EditRequest::factory()->count(7)->create(['place_id' => $place->id]);

        // Act
        $requests = $this->repository->getRecentEditRequests(5);

        // Assert
        $this->assertCount(5, $requests);
    }

    public function test_get_recent_edit_requests_returns_most_recent_first(): void
    {
        // Arrange
        $place = Place::factory()->create();
        $oldest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDays(3),
        ]);
        $middle = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDays(2),
        ]);
        $newest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDay(),
        ]);

        // Act
        $requests = $this->repository->getRecentEditRequests(3);

        // Assert
        $this->assertEquals($newest->id, $requests->first()->id);
        $this->assertEquals($oldest->id, $requests->last()->id);
    }

    public function test_get_recent_edit_requests_eager_loads_relationships(): void
    {
        // Arrange
        $place = Place::factory()->create();
        EditRequest::factory()->create(['place_id' => $place->id]);

        // Act
        $requests = $this->repository->getRecentEditRequests(5);

        // Assert
        $this->assertTrue($requests->first()->relationLoaded('place'));
        $this->assertTrue($requests->first()->place->relationLoaded('translations'));
        $this->assertTrue($requests->first()->relationLoaded('processedByAdmin'));
    }
}
