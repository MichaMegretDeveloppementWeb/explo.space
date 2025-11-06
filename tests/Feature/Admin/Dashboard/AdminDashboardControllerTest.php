<?php

namespace Tests\Feature\Admin\Dashboard;

use App\Models\Category;
use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceRequest;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_dashboard_requires_authentication(): void
    {
        // Act
        $response = $this->get(route('admin.dashboard'));

        // Assert
        $response->assertRedirect(route('admin.login')); // Redirects to login for unauthenticated users
    }

    public function test_dashboard_accessible_for_authenticated_admin(): void
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.index');
    }

    public function test_dashboard_displays_correct_stats(): void
    {
        // Arrange
        Place::factory()->count(10)->create();
        Tag::factory()->count(5)->create(['is_active' => true]);
        Category::factory()->count(3)->create(['is_active' => true]);
        PlaceRequest::factory()->count(4)->create(['status' => 'pending']);
        $place = Place::factory()->create();
        EditRequest::factory()->count(2)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_places'] === 11 // 10 + 1 from EditRequest
                && $stats['total_tags'] === 5
                && $stats['total_categories'] === 3
                && $stats['pending_place_requests'] === 4
                && $stats['pending_edit_requests'] === 2;
        });
    }

    public function test_dashboard_passes_stats_to_view(): void
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertViewHas('stats');
        $response->assertViewHas('recentPlaceRequests');
        $response->assertViewHas('recentEditRequests');
    }

    public function test_dashboard_displays_recent_place_requests(): void
    {
        // Arrange
        $requests = PlaceRequest::factory()->count(3)->create([
            'title' => 'Test Place Request',
        ]);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('recentPlaceRequests', function ($collection) use ($requests) {
            return $collection->count() === 3
                && $collection->pluck('id')->diff($requests->pluck('id'))->isEmpty();
        });
    }

    public function test_dashboard_displays_recent_edit_requests(): void
    {
        // Arrange
        $place = Place::factory()->create();
        $requests = EditRequest::factory()->count(2)->create([
            'place_id' => $place->id,
        ]);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('recentEditRequests', function ($collection) use ($requests) {
            return $collection->count() === 2
                && $collection->pluck('id')->diff($requests->pluck('id'))->isEmpty();
        });
    }

    public function test_dashboard_limits_recent_requests_to_five(): void
    {
        // Arrange
        PlaceRequest::factory()->count(10)->create();
        $place = Place::factory()->create();
        EditRequest::factory()->count(8)->create(['place_id' => $place->id]);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('recentPlaceRequests', function ($collection) {
            return $collection->count() === 5;
        });
        $response->assertViewHas('recentEditRequests', function ($collection) {
            return $collection->count() === 5;
        });
    }

    public function test_dashboard_shows_most_recent_requests_first(): void
    {
        // Arrange
        $oldest = PlaceRequest::factory()->create(['created_at' => now()->subDays(3)]);
        $middle = PlaceRequest::factory()->create(['created_at' => now()->subDays(2)]);
        $newest = PlaceRequest::factory()->create(['created_at' => now()->subDay()]);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('recentPlaceRequests', function ($collection) use ($newest, $oldest) {
            return $collection->first()->id === $newest->id
                && $collection->last()->id === $oldest->id;
        });
    }

    public function test_dashboard_handles_empty_stats_gracefully(): void
    {
        // Act (no data created)
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_places'] === 0
                && $stats['total_tags'] === 0
                && $stats['total_categories'] === 0
                && $stats['pending_place_requests'] === 0
                && $stats['pending_edit_requests'] === 0;
        });
        $response->assertViewHas('recentPlaceRequests', function ($collection) {
            return $collection->isEmpty();
        });
        $response->assertViewHas('recentEditRequests', function ($collection) {
            return $collection->isEmpty();
        });
    }

    public function test_dashboard_only_counts_active_tags_and_categories(): void
    {
        // Arrange
        Tag::factory()->count(3)->create(['is_active' => true]);
        Tag::factory()->count(2)->create(['is_active' => false]);
        Category::factory()->count(2)->create(['is_active' => true]);
        Category::factory()->count(1)->create(['is_active' => false]);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_tags'] === 3
                && $stats['total_categories'] === 2;
        });
    }

    public function test_dashboard_only_counts_pending_requests(): void
    {
        // Arrange
        PlaceRequest::factory()->count(2)->create(['status' => 'submitted']);
        PlaceRequest::factory()->count(3)->create(['status' => 'pending']);
        PlaceRequest::factory()->count(2)->create(['status' => 'accepted']);
        PlaceRequest::factory()->count(1)->create(['status' => 'refused']);

        $place = Place::factory()->create();
        EditRequest::factory()->count(1)->create(['place_id' => $place->id, 'status' => 'submitted']);
        EditRequest::factory()->count(2)->create(['place_id' => $place->id, 'status' => 'pending']);
        EditRequest::factory()->count(1)->create(['place_id' => $place->id, 'status' => 'accepted']);

        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['pending_place_requests'] === 5 // submitted + pending
                && $stats['pending_edit_requests'] === 3; // submitted + pending
        });
    }

    public function test_dashboard_view_contains_stat_cards(): void
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Lieux');
        $response->assertSee('Propositions de lieux en attente');
        $response->assertSee('Modifications/Signalements en attente');
        $response->assertSee('Tags');
        $response->assertSee('Catégories');
    }

    public function test_dashboard_view_contains_recent_requests_tables(): void
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Dernières propositions de lieux');
        $response->assertSee('Dernières demandes de modifications/signalements');
    }

    public function test_dashboard_shows_empty_state_when_no_requests(): void
    {
        // Act (no requests created)
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Aucune proposition de lieu récente');
        $response->assertSee('Aucune demande de modification/signalement récente');
    }
}
