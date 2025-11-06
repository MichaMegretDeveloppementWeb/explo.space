<?php

namespace Tests\Livewire\Admin\Place\PlaceList;

use App\Livewire\Admin\Place\PlaceList\PlaceListPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceListPageTest extends TestCase
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
        Livewire::test(PlaceListPage::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceListPage::class)
            ->assertSet('search', '')
            ->assertSet('tags', [])
            ->assertSet('locale', 'fr')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 20);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceListPage::class)
            ->assertViewIs('livewire.admin.place.place-list.place-list-page');
    }

    public function test_component_renders_child_components(): void
    {
        Livewire::test(PlaceListPage::class)
            ->assertSeeLivewire('admin.place.place-list.place-list-filters')
            ->assertSeeLivewire('admin.place.place-list.place-list-table');
    }

    // ========================================
    // Filter Updates via Events
    // ========================================

    public function test_update_filters_updates_properties(): void
    {
        Livewire::test(PlaceListPage::class)
            ->call('updateFilters', 'NASA', ['nasa'], 'en')
            ->assertSet('search', 'NASA')
            ->assertSet('tags', ['nasa'])
            ->assertSet('locale', 'en');
    }

    public function test_update_filters_with_empty_search(): void
    {
        Livewire::test(PlaceListPage::class)
            ->call('updateFilters', '', [], 'fr')
            ->assertSet('search', '')
            ->assertSet('tags', [])
            ->assertSet('locale', 'fr');
    }

    public function test_update_filters_with_multiple_tags(): void
    {
        Livewire::test(PlaceListPage::class)
            ->call('updateFilters', '', ['nasa', 'spacex', 'esa'], 'en')
            ->assertSet('search', '')
            ->assertSet('tags', ['nasa', 'spacex', 'esa'])
            ->assertSet('locale', 'en');
    }

    public function test_update_filters_with_all_parameters(): void
    {
        Livewire::test(PlaceListPage::class)
            ->call('updateFilters', 'Kennedy Space Center', ['nasa', 'apollo'], 'en')
            ->assertSet('search', 'Kennedy Space Center')
            ->assertSet('tags', ['nasa', 'apollo'])
            ->assertSet('locale', 'en');
    }

    // ========================================
    // Event Listening (filters:updated)
    // ========================================

    public function test_listens_to_filters_updated_event(): void
    {
        $component = Livewire::test(PlaceListPage::class);

        // Dispatch the event that PlaceListFilters would send
        $component->dispatch('filters:updated', search: 'Apollo', tags: ['nasa'], locale: 'en');

        $component->assertSet('search', 'Apollo')
            ->assertSet('tags', ['nasa'])
            ->assertSet('locale', 'en');
    }

    public function test_filters_updated_event_with_empty_values(): void
    {
        // Start with some filters
        Livewire::test(PlaceListPage::class)
            ->call('updateFilters', 'NASA', ['nasa'], 'en')
            ->assertSet('search', 'NASA')
            // Reset filters
            ->dispatch('filters:updated', search: '', tags: [], locale: 'fr')
            ->assertSet('search', '')
            ->assertSet('tags', [])
            ->assertSet('locale', 'fr');
    }

    // ========================================
    // Sorting Events
    // ========================================

    public function test_update_sorting_updates_properties(): void
    {
        Livewire::test(PlaceListPage::class)
            ->call('updateSorting', 'title', 'asc')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_listens_to_sorting_updated_event(): void
    {
        $component = Livewire::test(PlaceListPage::class);

        $component->dispatch('sorting:updated', sortBy: 'updated_at', sortDirection: 'desc');

        $component->assertSet('sortBy', 'updated_at')
            ->assertSet('sortDirection', 'desc');
    }

    // ========================================
    // Pagination Events
    // ========================================

    public function test_update_pagination_updates_property(): void
    {
        Livewire::test(PlaceListPage::class)
            ->call('updatePagination', 50)
            ->assertSet('perPage', 50);
    }

    public function test_listens_to_pagination_updated_event(): void
    {
        $component = Livewire::test(PlaceListPage::class);

        $component->dispatch('pagination:updated', perPage: 30);

        $component->assertSet('perPage', 30);
    }

    // ========================================
    // View Data (passes to child components)
    // Note: updateFilters/updateSorting/updatePagination use skipRender()
    // so we can't test viewData after calling them.
    // The view data is correctly passed on initial render only.
    // ========================================

    public function test_passes_correct_initial_data_to_view(): void
    {
        $component = Livewire::test(PlaceListPage::class);

        // Verify initial filters
        $initialFilters = $component->viewData('initialFilters');
        $this->assertEquals([
            'search' => '',
            'tags' => [],
            'locale' => 'fr',
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

    // ========================================
    // URL Parameters (via #[Url] attribute)
    // ========================================

    public function test_url_parameters_override_defaults(): void
    {
        // Note: Testing URL parameters requires specific Livewire setup
        // This is a simplified test that verifies the component can receive parameters
        Livewire::test(PlaceListPage::class, [
            'search' => 'SpaceX',
            'tags' => ['spacex'],
            'locale' => 'en',
        ])
            ->assertSet('search', 'SpaceX')
            ->assertSet('tags', ['spacex'])
            ->assertSet('locale', 'en');
    }
}
