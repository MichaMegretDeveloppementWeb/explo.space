<?php

namespace Tests\Livewire\Admin\PlaceRequest\PlaceRequestList;

use App\Livewire\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListTable;
use App\Models\PlaceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceRequestListTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
        app()->setLocale('fr');
    }

    /**
     * Helper pour créer une instance de test avec des paramètres par défaut
     */
    private function test_component(array $initialFilters = [], array $initialSorting = [], int $initialPerPage = 20)
    {
        return Livewire::test(PlaceRequestListTable::class, [
            'initialFilters' => array_merge([
                'status' => [],
            ], $initialFilters),
            'initialSorting' => array_merge([
                'sortBy' => 'created_at',
                'sortDirection' => 'desc',
            ], $initialSorting),
            'initialPerPage' => $initialPerPage,
        ]);
    }

    // ========================================
    // Component Rendering & Initialization
    // ========================================

    public function test_component_can_be_rendered(): void
    {
        $this->testComponent()
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        $this->testComponent([], [], 10)
            ->assertSet('status', [])
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 10);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        $this->testComponent([
            'status' => ['pending', 'submitted'],
        ])
            ->assertSet('status', ['pending', 'submitted']);
    }

    public function test_component_view_exists(): void
    {
        $this->testComponent()
            ->assertViewIs('livewire.admin.place-request.place-request-list.place-request-list-table');
    }

    // ========================================
    // Pagination
    // ========================================

    public function test_per_page_can_be_changed(): void
    {
        $this->testComponent()
            ->call('updatePerPage', 30)
            ->assertSet('perPage', 30)
            ->assertDispatched('pagination:updated', perPage: 30);
    }

    public function test_valid_per_page_values_are_accepted(): void
    {
        $validValues = [10, 20, 30, 50];

        foreach ($validValues as $value) {
            $this->testComponent()
                ->call('updatePerPage', $value)
                ->assertSet('perPage', $value);
        }
    }

    public function test_pagination_resets_when_filters_change(): void
    {
        // Create multiple place requests to test pagination
        for ($i = 0; $i < 15; $i++) {
            PlaceRequest::factory()->create([
                'title' => "Lieu {$i}",
                'status' => 'submitted',
            ]);
        }

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change filters - should reset to page 1
        $component->dispatch('filters:updated', status: ['pending']);

        // Verify we're back on page 1
        $component->assertSet('status', ['pending']);
    }

    public function test_update_per_page_resets_pagination(): void
    {
        // Create many requests
        PlaceRequest::factory()->count(30)->create(['status' => 'submitted']);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change perPage - should reset to page 1
        $component->call('updatePerPage', 20);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals(1, $placeRequests->currentPage());
    }

    // ========================================
    // Sorting
    // ========================================

    public function test_sort_by_title(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc') // Default direction for new column
            ->assertDispatched('sorting:updated', sortBy: 'title', sortDirection: 'desc');
    }

    public function test_sort_by_status(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'status')
            ->assertSet('sortBy', 'status')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sort_by_created_at(): void
    {
        // Component initialise avec sortBy='created_at' et sortDirection='desc'
        // Premier appel toggle vers 'asc', deuxième appel toggle vers 'desc'
        $this->testComponent()
            ->call('sortByColumn', 'created_at')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'asc') // Toggle depuis desc initial
            ->call('sortByColumn', 'created_at')
            ->assertSet('sortDirection', 'desc'); // Toggle retour vers desc
    }

    public function test_sort_direction_toggles_on_same_column(): void
    {
        $component = $this->testComponent()
            ->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc'); // First click: desc (default)

        // Click again on same column - toggle to asc
        $component->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc');

        // Click again - toggle back to desc
        $component->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_changing_sort_column_resets_direction(): void
    {
        $component = $this->testComponent()
            ->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc'); // First click: desc

        // Change to different column
        $component->call('sortByColumn', 'status')
            ->assertSet('sortBy', 'status')
            ->assertSet('sortDirection', 'desc'); // Default direction for new column
    }

    public function test_sorting_resets_pagination(): void
    {
        // Create many requests
        PlaceRequest::factory()->count(30)->create(['status' => 'submitted']);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change sorting - should reset to page 1
        $component->call('sortByColumn', 'title');

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals(1, $placeRequests->currentPage());
    }

    // ========================================
    // Event Handling (filters:updated)
    // ========================================

    public function test_filters_updated_event_updates_status(): void
    {
        $this->testComponent()
            ->dispatch('filters:updated', status: ['pending'])
            ->assertSet('status', ['pending']);
    }

    public function test_filters_updated_event_updates_multiple_statuses(): void
    {
        $this->testComponent()
            ->dispatch('filters:updated', status: ['pending', 'submitted', 'accepted'])
            ->assertSet('status', ['pending', 'submitted', 'accepted']);
    }

    public function test_filters_updated_event_resets_pagination(): void
    {
        // Create many requests
        PlaceRequest::factory()->count(30)->create(['status' => 'submitted']);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Update filters - should reset to page 1
        $component->dispatch('filters:updated', status: ['pending']);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals(1, $placeRequests->currentPage());
    }

    // ========================================
    // Data Display
    // ========================================

    public function test_displays_place_requests(): void
    {
        PlaceRequest::factory()->create([
            'title' => 'Centre Spatial Kennedy',
            'status' => 'submitted',
        ]);

        $component = $this->testComponent();

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(1, $placeRequests);
        $this->assertEquals('Centre Spatial Kennedy', $placeRequests[0]->title);
    }

    public function test_filters_by_single_status(): void
    {
        PlaceRequest::factory()->create(['title' => 'Lieu 1', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Lieu 2', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Lieu 3', 'status' => 'accepted']);

        $component = $this->testComponent([
            'status' => ['pending'],
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(1, $placeRequests);
        $this->assertEquals('Lieu 1', $placeRequests[0]->title);
    }

    public function test_filters_by_multiple_statuses(): void
    {
        PlaceRequest::factory()->create(['title' => 'Lieu 1', 'status' => 'pending']);
        PlaceRequest::factory()->create(['title' => 'Lieu 2', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Lieu 3', 'status' => 'accepted']);
        PlaceRequest::factory()->create(['title' => 'Lieu 4', 'status' => 'refused']);

        $component = $this->testComponent([
            'status' => ['pending', 'submitted'],
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(2, $placeRequests);
        $this->assertTrue($placeRequests->contains('title', 'Lieu 1'));
        $this->assertTrue($placeRequests->contains('title', 'Lieu 2'));
    }

    public function test_displays_all_when_no_status_filter(): void
    {
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequest::factory()->create(['status' => 'accepted']);

        $component = $this->testComponent([
            'status' => [],
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(3, $placeRequests);
    }

    public function test_empty_result_when_no_matching_status(): void
    {
        PlaceRequest::factory()->create(['status' => 'accepted']);
        PlaceRequest::factory()->create(['status' => 'refused']);

        $component = $this->testComponent([
            'status' => ['pending'],
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(0, $placeRequests);
    }

    // ========================================
    // Sorting Behavior
    // ========================================

    public function test_sorts_by_title_ascending(): void
    {
        PlaceRequest::factory()->create(['title' => 'Zebra', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Alpha', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Beta', 'status' => 'submitted']);

        $component = $this->testComponent([], [
            'sortBy' => 'title',
            'sortDirection' => 'asc',
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals('Alpha', $placeRequests[0]->title);
        $this->assertEquals('Beta', $placeRequests[1]->title);
        $this->assertEquals('Zebra', $placeRequests[2]->title);
    }

    public function test_sorts_by_title_descending(): void
    {
        PlaceRequest::factory()->create(['title' => 'Zebra', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Alpha', 'status' => 'submitted']);
        PlaceRequest::factory()->create(['title' => 'Beta', 'status' => 'submitted']);

        $component = $this->testComponent([], [
            'sortBy' => 'title',
            'sortDirection' => 'desc',
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals('Zebra', $placeRequests[0]->title);
        $this->assertEquals('Beta', $placeRequests[1]->title);
        $this->assertEquals('Alpha', $placeRequests[2]->title);
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

        $component = $this->testComponent([], [
            'sortBy' => 'created_at',
            'sortDirection' => 'desc',
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals($recent->id, $placeRequests[0]->id);
        $this->assertEquals($medium->id, $placeRequests[1]->id);
        $this->assertEquals($old->id, $placeRequests[2]->id);
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

        $component = $this->testComponent();
        $placeRequests = $component->viewData('placeRequests');

        // Should have eager loaded the relationship
        $this->assertTrue($placeRequests[0]->relationLoaded('viewedByAdmin'));
        $this->assertEquals('Admin Viewer', $placeRequests[0]->viewedByAdmin->name);
    }

    public function test_eager_loads_processed_by_admin(): void
    {
        $processor = User::factory()->create(['name' => 'Admin Processor']);

        PlaceRequest::factory()->create([
            'status' => 'accepted',
            'processed_by_admin_id' => $processor->id,
        ]);

        $component = $this->testComponent();
        $placeRequests = $component->viewData('placeRequests');

        // Should have eager loaded the relationship
        $this->assertTrue($placeRequests[0]->relationLoaded('processedByAdmin'));
        $this->assertEquals('Admin Processor', $placeRequests[0]->processedByAdmin->name);
    }

    public function test_eager_loads_photos(): void
    {
        $placeRequest = PlaceRequest::factory()->create(['status' => 'submitted']);
        $placeRequest->photos()->createMany([
            ['path' => 'photo1.jpg', 'is_primary' => true],
            ['path' => 'photo2.jpg', 'is_primary' => false],
        ]);

        $component = $this->testComponent();
        $placeRequests = $component->viewData('placeRequests');

        // Should have eager loaded the relationship
        $this->assertTrue($placeRequests[0]->relationLoaded('photos'));
        $this->assertCount(2, $placeRequests[0]->photos);
    }

    // ========================================
    // Pagination Display
    // ========================================

    public function test_paginates_results(): void
    {
        PlaceRequest::factory()->count(25)->create(['status' => 'submitted']);

        $component = $this->testComponent([], [], 10);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals(10, $placeRequests->count());
        $this->assertEquals(25, $placeRequests->total());
        $this->assertEquals(3, $placeRequests->lastPage());
    }

    public function test_respects_per_page_setting(): void
    {
        PlaceRequest::factory()->count(50)->create(['status' => 'submitted']);

        $component = $this->testComponent([], [], 30);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertEquals(30, $placeRequests->count());
        $this->assertEquals(50, $placeRequests->total());
    }

    // ========================================
    // Empty States
    // ========================================

    public function test_shows_empty_state_when_no_requests(): void
    {
        $component = $this->testComponent();

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(0, $placeRequests);
    }

    public function test_shows_empty_state_when_no_matching_filters(): void
    {
        PlaceRequest::factory()->create(['status' => 'accepted']);
        PlaceRequest::factory()->create(['status' => 'refused']);

        $component = $this->testComponent([
            'status' => ['pending'],
        ]);

        $placeRequests = $component->viewData('placeRequests');
        $this->assertCount(0, $placeRequests);
    }
}
