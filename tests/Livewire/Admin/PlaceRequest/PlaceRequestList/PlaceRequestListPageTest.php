<?php

namespace Tests\Livewire\Admin\PlaceRequest\PlaceRequestList;

use App\Livewire\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceRequestListPageTest extends TestCase
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

    // ========================================
    // Component Rendering & Initialization
    // ========================================

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->assertSet('status', [])
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 20);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->assertViewIs('livewire.admin.place-request.place-request-list.place-request-list-page');
    }

    public function test_component_renders_child_components(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->assertSeeLivewire('admin.place-request.place-request-list.place-request-list-filters')
            ->assertSeeLivewire('admin.place-request.place-request-list.place-request-list-table');
    }

    // ========================================
    // Status Filter (Array Support)
    // ========================================

    public function test_mount_accepts_single_status_as_string(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => 'pending',
        ])
            ->assertSet('status', ['pending']);
    }

    public function test_mount_accepts_multiple_statuses_as_comma_separated_string(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => 'pending,submitted',
        ])
            ->assertSet('status', ['pending', 'submitted']);
    }

    public function test_mount_accepts_status_as_array(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => ['pending', 'submitted'],
        ])
            ->assertSet('status', ['pending', 'submitted']);
    }

    public function test_mount_accepts_empty_status(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => '',
        ])
            ->assertSet('status', []);
    }

    public function test_mount_accepts_all_status(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => 'all',
        ])
            ->assertSet('status', []);
    }

    // ========================================
    // Filter Updates via Events
    // ========================================

    public function test_update_filters_updates_status(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateFilters', ['pending', 'submitted'])
            ->assertSet('status', ['pending', 'submitted']);
    }

    public function test_update_filters_with_empty_status(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateFilters', [])
            ->assertSet('status', []);
    }

    public function test_update_filters_with_single_status(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateFilters', ['pending'])
            ->assertSet('status', ['pending']);
    }

    public function test_update_filters_with_all_statuses(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateFilters', ['submitted', 'pending', 'accepted', 'refused'])
            ->assertSet('status', ['submitted', 'pending', 'accepted', 'refused']);
    }

    // ========================================
    // Event Listening (filters:updated)
    // ========================================

    public function test_listens_to_filters_updated_event(): void
    {
        $component = Livewire::test(PlaceRequestListPage::class);

        // Dispatch the event that PlaceRequestListFilters would send
        $component->dispatch('filters:updated', status: ['pending', 'submitted']);

        $component->assertSet('status', ['pending', 'submitted']);
    }

    public function test_filters_updated_event_with_empty_values(): void
    {
        // Start with some filters
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateFilters', ['pending'])
            ->assertSet('status', ['pending'])
            // Reset filters
            ->dispatch('filters:updated', status: [])
            ->assertSet('status', []);
    }

    public function test_filters_updated_event_overwrites_previous_filters(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateFilters', ['pending'])
            ->assertSet('status', ['pending'])
            ->dispatch('filters:updated', status: ['accepted', 'refused'])
            ->assertSet('status', ['accepted', 'refused']);
    }

    // ========================================
    // Sorting Events
    // ========================================

    public function test_update_sorting_updates_properties(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updateSorting', 'title', 'asc')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_listens_to_sorting_updated_event(): void
    {
        $component = Livewire::test(PlaceRequestListPage::class);

        $component->dispatch('sorting:updated', sortBy: 'status', sortDirection: 'asc');

        $component->assertSet('sortBy', 'status')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_sorting_updates_with_all_allowed_columns(): void
    {
        $columns = ['title', 'status', 'created_at'];

        foreach ($columns as $column) {
            Livewire::test(PlaceRequestListPage::class)
                ->call('updateSorting', $column, 'asc')
                ->assertSet('sortBy', $column)
                ->assertSet('sortDirection', 'asc');
        }
    }

    // ========================================
    // Pagination Events
    // ========================================

    public function test_update_pagination_updates_property(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->call('updatePagination', 50)
            ->assertSet('perPage', 50);
    }

    public function test_listens_to_pagination_updated_event(): void
    {
        $component = Livewire::test(PlaceRequestListPage::class);

        $component->dispatch('pagination:updated', perPage: 30);

        $component->assertSet('perPage', 30);
    }

    public function test_pagination_accepts_valid_per_page_values(): void
    {
        $validValues = [10, 20, 30, 50];

        foreach ($validValues as $value) {
            Livewire::test(PlaceRequestListPage::class)
                ->call('updatePagination', $value)
                ->assertSet('perPage', $value);
        }
    }

    // ========================================
    // View Data (passes to child components)
    // ========================================

    public function test_passes_correct_initial_data_to_view(): void
    {
        $component = Livewire::test(PlaceRequestListPage::class);

        // Verify initial filters
        $initialFilters = $component->viewData('initialFilters');
        $this->assertEquals([
            'status' => [],
        ], $initialFilters);

        // Verify initial sorting
        $initialSorting = $component->viewData('initialSorting');
        $this->assertEquals([
            'sortBy' => 'created_at',
            'sortDirection' => 'desc',
        ], $initialSorting);

        // Verify initial perPage
        $initialPerPage = $component->viewData('initialPerPage');
        $this->assertEquals(20, $initialPerPage);
    }

    public function test_passes_updated_filters_to_child_components(): void
    {
        $component = Livewire::test(PlaceRequestListPage::class)
            ->set('status', ['pending', 'submitted'])
            ->set('sortBy', 'title')
            ->set('sortDirection', 'asc')
            ->set('perPage', 30);

        $initialFilters = $component->viewData('initialFilters');
        $this->assertEquals(['status' => ['pending', 'submitted']], $initialFilters);

        $initialSorting = $component->viewData('initialSorting');
        $this->assertEquals(['sortBy' => 'title', 'sortDirection' => 'asc'], $initialSorting);

        $initialPerPage = $component->viewData('initialPerPage');
        $this->assertEquals(30, $initialPerPage);
    }

    // ========================================
    // URL Parameters (via #[Url] attribute)
    // ========================================

    public function test_url_parameters_override_defaults_with_string(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => 'pending',
        ])
            ->assertSet('status', ['pending']);
    }

    public function test_url_parameters_override_defaults_with_comma_separated_string(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => 'pending,submitted,accepted',
        ])
            ->assertSet('status', ['pending', 'submitted', 'accepted']);
    }

    public function test_url_parameters_handle_empty_values_correctly(): void
    {
        Livewire::test(PlaceRequestListPage::class, [
            'status' => '',
        ])
            ->assertSet('status', []);
    }

    // ========================================
    // Component State Management
    // ========================================

    public function test_changing_filters_preserves_other_properties(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->set('sortBy', 'title')
            ->set('sortDirection', 'asc')
            ->set('perPage', 50)
            ->call('updateFilters', ['pending'])
            ->assertSet('status', ['pending'])
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc')
            ->assertSet('perPage', 50);
    }

    public function test_changing_sorting_preserves_filters(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->set('status', ['pending', 'submitted'])
            ->call('updateSorting', 'title', 'asc')
            ->assertSet('status', ['pending', 'submitted'])
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_changing_pagination_preserves_filters_and_sorting(): void
    {
        Livewire::test(PlaceRequestListPage::class)
            ->set('status', ['pending'])
            ->set('sortBy', 'title')
            ->set('sortDirection', 'asc')
            ->call('updatePagination', 50)
            ->assertSet('status', ['pending'])
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc')
            ->assertSet('perPage', 50);
    }
}
