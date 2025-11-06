<?php

namespace Tests\Livewire\Admin\EditRequest\EditRequestList;

use App\Livewire\Admin\EditRequest\EditRequestList\EditRequestListTable;
use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditRequestListTableTest extends TestCase
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
        return Livewire::test(EditRequestListTable::class, [
            'initialFilters' => array_merge([
                'search' => '',
                'type' => '',
                'status' => '',
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
        $this->testComponent()
            ->assertSet('search', '')
            ->assertSet('type', '')
            ->assertSet('status', '')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 20);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        $this->testComponent([
            'search' => 'test',
            'type' => 'modification',
            'status' => 'pending',
        ])
            ->assertSet('search', 'test')
            ->assertSet('type', 'modification')
            ->assertSet('status', 'pending');
    }

    public function test_mount_accepts_initial_sorting(): void
    {
        $this->testComponent([], [
            'sortBy' => 'place',
            'sortDirection' => 'asc',
        ])
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_mount_accepts_initial_per_page(): void
    {
        $this->testComponent([], [], 50)
            ->assertSet('perPage', 50);
    }

    public function test_component_view_exists(): void
    {
        $this->testComponent()
            ->assertViewIs('livewire.admin.edit-request.edit-request-list.edit-request-list-table');
    }

    // ========================================
    // Pagination
    // ========================================

    public function test_per_page_can_be_changed(): void
    {
        $this->testComponent()
            ->call('updatePerPage', 50)
            ->assertSet('perPage', 50)
            ->assertDispatched('pagination:updated', perPage: 50);
    }

    public function test_valid_per_page_values_are_accepted(): void
    {
        $validValues = [10, 20, 50, 100];

        foreach ($validValues as $value) {
            $this->testComponent()
                ->call('updatePerPage', $value)
                ->assertSet('perPage', $value);
        }
    }

    public function test_update_per_page_resets_pagination(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create many requests
        EditRequest::factory()->count(30)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change perPage - should reset to page 1
        $component->call('updatePerPage', 20);

        $editRequests = $component->viewData('editRequests');
        $this->assertEquals(1, $editRequests->currentPage());
    }

    public function test_pagination_resets_when_filters_change(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create multiple edit requests
        EditRequest::factory()->count(15)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change filters - should reset to page 1
        $component->dispatch('filters:updated', search: '', type: '', status: 'pending');

        $component->assertSet('status', 'pending');
    }

    // ========================================
    // Sorting
    // ========================================

    public function test_sort_by_place(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'place')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'desc')
            ->assertDispatched('sorting:updated', sortBy: 'place', sortDirection: 'desc');
    }

    public function test_sort_by_type(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'type')
            ->assertSet('sortBy', 'type')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sort_by_contact_email(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'contact_email')
            ->assertSet('sortBy', 'contact_email')
            ->assertSet('sortDirection', 'desc');
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
        $this->testComponent()
            ->call('sortByColumn', 'created_at')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sort_direction_toggles_on_same_column(): void
    {
        $component = $this->testComponent()
            ->call('sortByColumn', 'place')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'desc');

        // Click again on the same column - should toggle direction
        $component->call('sortByColumn', 'place')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'asc');

        // Click again - should toggle back
        $component->call('sortByColumn', 'place')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sort_by_different_column_resets_direction_to_desc(): void
    {
        $component = $this->testComponent()
            ->call('sortByColumn', 'place')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'desc');

        // Sort by different column - should reset direction to desc
        $component->call('sortByColumn', 'type')
            ->assertSet('sortBy', 'type')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sorting_resets_pagination(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create many requests
        EditRequest::factory()->count(30)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Sort - should reset to page 1
        $component->call('sortByColumn', 'place');

        $this->assertEquals(1, $component->get('page'));
    }

    public function test_sorting_dispatches_event(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'place')
            ->assertDispatched('sorting:updated', sortBy: 'place', sortDirection: 'desc');
    }

    // ========================================
    // Filters via Events
    // ========================================

    public function test_filters_updated_event_updates_search(): void
    {
        $this->testComponent()
            ->dispatch('filters:updated', search: 'test', type: '', status: '')
            ->assertSet('search', 'test');
    }

    public function test_filters_updated_event_updates_type(): void
    {
        $this->testComponent()
            ->dispatch('filters:updated', search: '', type: 'modification', status: '')
            ->assertSet('type', 'modification');
    }

    public function test_filters_updated_event_updates_status(): void
    {
        $this->testComponent()
            ->dispatch('filters:updated', search: '', type: '', status: 'pending')
            ->assertSet('status', 'pending');
    }

    public function test_filters_updated_event_updates_all_filters(): void
    {
        $this->testComponent()
            ->dispatch('filters:updated', search: 'kennedy', type: 'modification', status: 'submitted')
            ->assertSet('search', 'kennedy')
            ->assertSet('type', 'modification')
            ->assertSet('status', 'submitted');
    }

    public function test_filters_updated_resets_pagination(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create many requests
        EditRequest::factory()->count(30)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Update filters - should reset to page 1
        $component->dispatch('filters:updated', search: 'test', type: '', status: '');

        $this->assertEquals(1, $component->get('page'));
    }

    // ========================================
    // Data Loading
    // ========================================

    public function test_view_data_contains_edit_requests(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Test Place',
            'status' => 'published',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $component = $this->testComponent();

        $editRequests = $component->viewData('editRequests');

        $this->assertNotNull($editRequests);
        $this->assertCount(1, $editRequests);
    }

    public function test_filters_edit_requests_by_search(): void
    {
        // Create places with translations
        $place1 = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'SpaceX Boca Chica',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place1->id, 'status' => 'submitted']);
        EditRequest::factory()->create(['place_id' => $place2->id, 'status' => 'submitted']);

        $component = $this->testComponent(['search' => 'Kennedy']);

        $editRequests = $component->viewData('editRequests');

        $this->assertCount(1, $editRequests);
    }

    public function test_filters_edit_requests_by_type(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'type' => 'modification', 'status' => 'submitted']);
        EditRequest::factory()->create(['place_id' => $place->id, 'type' => 'signalement', 'status' => 'submitted']);

        $component = $this->testComponent(['type' => 'modification']);

        $editRequests = $component->viewData('editRequests');

        $this->assertCount(1, $editRequests);
        $this->assertEquals('modification', $editRequests->first()->type);
    }

    public function test_filters_edit_requests_by_status(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'submitted']);
        EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'pending']);

        $component = $this->testComponent(['status' => 'submitted']);

        $editRequests = $component->viewData('editRequests');

        $this->assertCount(1, $editRequests);
        $this->assertEquals('submitted', $editRequests->first()->status->value);
    }

    public function test_respects_per_page_parameter(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create 25 requests
        EditRequest::factory()->count(25)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $component = $this->testComponent([], [], 10);

        $editRequests = $component->viewData('editRequests');

        $this->assertCount(10, $editRequests);
        $this->assertEquals(25, $editRequests->total());
    }

    // ========================================
    // View Rendering
    // ========================================

    public function test_view_renders_table_headers(): void
    {
        $this->testComponent()
            ->assertSee('Lieu')
            ->assertSee('Type')
            ->assertSee('Contact')
            ->assertSee('Statut')
            ->assertSee('Soumis le');
    }

    public function test_view_renders_per_page_selector(): void
    {
        $this->testComponent()
            ->assertSee('Lignes par page')
            ->assertSee('wire:model.live="perPage"', false);
    }

    public function test_view_renders_loading_indicator(): void
    {
        $this->testComponent()
            ->assertSee('wire:loading', false);
    }

    public function test_view_renders_empty_state_when_no_results(): void
    {
        $this->testComponent()
            ->assertSee('Aucune demande trouvée');
    }

    public function test_view_renders_edit_request_data(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
            'contact_email' => 'test@example.com',
        ]);

        $this->testComponent()
            ->assertSee('Kennedy Space Center')
            ->assertSee('test@example.com');
    }
}
