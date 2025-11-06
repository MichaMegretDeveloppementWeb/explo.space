<?php

namespace Tests\Livewire\Admin\EditRequest\EditRequestList;

use App\Livewire\Admin\EditRequest\EditRequestList\EditRequestListPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditRequestListPageTest extends TestCase
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
        Livewire::test(EditRequestListPage::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->assertSet('search', '')
            ->assertSet('type', '')
            ->assertSet('status', '')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 20);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->assertViewIs('livewire.admin.edit-request.edit-request-list.edit-request-list-page');
    }

    public function test_component_renders_child_components(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->assertSeeLivewire('admin.edit-request.edit-request-list.edit-request-list-filters')
            ->assertSeeLivewire('admin.edit-request.edit-request-list.edit-request-list-table');
    }

    // ========================================
    // Mount with Parameters
    // ========================================

    public function test_mount_accepts_search_parameter(): void
    {
        Livewire::test(EditRequestListPage::class, [
            'q' => 'test search',
        ])
            ->assertSet('search', 'test search');
    }

    public function test_mount_accepts_type_parameter(): void
    {
        Livewire::test(EditRequestListPage::class, [
            'type' => 'modification',
        ])
            ->assertSet('type', 'modification');
    }

    public function test_mount_accepts_status_parameter(): void
    {
        Livewire::test(EditRequestListPage::class, [
            'status' => 'pending',
        ])
            ->assertSet('status', 'pending');
    }

    public function test_mount_accepts_sort_by_parameter(): void
    {
        Livewire::test(EditRequestListPage::class, [
            's' => 'place',
        ])
            ->assertSet('sortBy', 'place');
    }

    public function test_mount_accepts_sort_direction_parameter(): void
    {
        Livewire::test(EditRequestListPage::class, [
            'd' => 'asc',
        ])
            ->assertSet('sortDirection', 'asc');
    }

    public function test_mount_accepts_per_page_parameter(): void
    {
        Livewire::test(EditRequestListPage::class, [
            'pp' => 50,
        ])
            ->assertSet('perPage', 50);
    }

    public function test_mount_accepts_all_parameters_together(): void
    {
        Livewire::test(EditRequestListPage::class, [
            'q' => 'kennedy',
            'type' => 'modification',
            'status' => 'submitted',
            's' => 'place',
            'd' => 'asc',
            'pp' => 100,
        ])
            ->assertSet('search', 'kennedy')
            ->assertSet('type', 'modification')
            ->assertSet('status', 'submitted')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'asc')
            ->assertSet('perPage', 100);
    }

    // ========================================
    // Filter Updates via Events
    // ========================================

    public function test_update_filters_updates_search(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updateFilters', 'test search', '', '')
            ->assertSet('search', 'test search');
    }

    public function test_update_filters_updates_type(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updateFilters', '', 'modification', '')
            ->assertSet('type', 'modification');
    }

    public function test_update_filters_updates_status(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updateFilters', '', '', 'pending')
            ->assertSet('status', 'pending');
    }

    public function test_update_filters_updates_all_at_once(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updateFilters', 'kennedy', 'modification', 'submitted')
            ->assertSet('search', 'kennedy')
            ->assertSet('type', 'modification')
            ->assertSet('status', 'submitted');
    }

    public function test_update_filters_with_empty_values(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->set('search', 'test')
            ->set('type', 'modification')
            ->set('status', 'pending')
            ->call('updateFilters', '', '', '')
            ->assertSet('search', '')
            ->assertSet('type', '')
            ->assertSet('status', '');
    }

    public function test_update_filters_does_not_trigger_render(): void
    {
        $component = Livewire::test(EditRequestListPage::class);

        $initialRenderCount = $component->instance()->getRenderCount();

        $component->call('updateFilters', 'test', 'modification', 'pending');

        // skipRender() is called, so render count should be the same
        $this->assertEquals($initialRenderCount, $component->instance()->getRenderCount());
    }

    // ========================================
    // Sorting Updates via Events
    // ========================================

    public function test_update_sorting_updates_sort_by(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updateSorting', 'place', 'asc')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_update_sorting_updates_sort_direction(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updateSorting', 'created_at', 'asc')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_update_sorting_resets_to_first_page(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->set('page', 3)
            ->call('updateSorting', 'place', 'asc')
            ->assertSet('page', 1);
    }

    public function test_update_sorting_does_not_trigger_render(): void
    {
        $component = Livewire::test(EditRequestListPage::class);

        $initialRenderCount = $component->instance()->getRenderCount();

        $component->call('updateSorting', 'place', 'asc');

        // skipRender() is called, so render count should be the same
        $this->assertEquals($initialRenderCount, $component->instance()->getRenderCount());
    }

    // ========================================
    // Per Page Updates via Events
    // ========================================

    public function test_update_per_page_updates_value(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->call('updatePerPage', 50)
            ->assertSet('perPage', 50);
    }

    public function test_update_per_page_resets_to_first_page(): void
    {
        Livewire::test(EditRequestListPage::class)
            ->set('page', 3)
            ->call('updatePerPage', 50)
            ->assertSet('page', 1);
    }

    public function test_update_per_page_does_not_trigger_render(): void
    {
        $component = Livewire::test(EditRequestListPage::class);

        $initialRenderCount = $component->instance()->getRenderCount();

        $component->call('updatePerPage', 50);

        // skipRender() is called, so render count should be the same
        $this->assertEquals($initialRenderCount, $component->instance()->getRenderCount());
    }

    // ========================================
    // URL Query String Synchronization
    // ========================================

    public function test_component_uses_url_for_search(): void
    {
        Livewire::withQueryParams(['q' => 'test'])
            ->test(EditRequestListPage::class)
            ->assertSet('search', 'test');
    }

    public function test_component_uses_url_for_type(): void
    {
        Livewire::withQueryParams(['type' => 'modification'])
            ->test(EditRequestListPage::class)
            ->assertSet('type', 'modification');
    }

    public function test_component_uses_url_for_status(): void
    {
        Livewire::withQueryParams(['status' => 'pending'])
            ->test(EditRequestListPage::class)
            ->assertSet('status', 'pending');
    }

    public function test_component_uses_url_for_sort_by(): void
    {
        Livewire::withQueryParams(['s' => 'place'])
            ->test(EditRequestListPage::class)
            ->assertSet('sortBy', 'place');
    }

    public function test_component_uses_url_for_sort_direction(): void
    {
        Livewire::withQueryParams(['d' => 'asc'])
            ->test(EditRequestListPage::class)
            ->assertSet('sortDirection', 'asc');
    }

    public function test_component_uses_url_for_per_page(): void
    {
        Livewire::withQueryParams(['pp' => 50])
            ->test(EditRequestListPage::class)
            ->assertSet('perPage', 50);
    }

    public function test_component_syncs_all_url_parameters(): void
    {
        Livewire::withQueryParams([
            'q' => 'kennedy',
            'type' => 'modification',
            'status' => 'submitted',
            's' => 'place',
            'd' => 'asc',
            'pp' => 100,
        ])
            ->test(EditRequestListPage::class)
            ->assertSet('search', 'kennedy')
            ->assertSet('type', 'modification')
            ->assertSet('status', 'submitted')
            ->assertSet('sortBy', 'place')
            ->assertSet('sortDirection', 'asc')
            ->assertSet('perPage', 100);
    }

    // ========================================
    // Initial Filters Property
    // ========================================

    public function test_computed_initial_filters_returns_correct_structure(): void
    {
        $component = Livewire::test(EditRequestListPage::class, [
            'q' => 'test',
            'type' => 'modification',
            'status' => 'pending',
        ]);

        $initialFilters = $component->get('initialFilters');

        $this->assertIsArray($initialFilters);
        $this->assertArrayHasKey('search', $initialFilters);
        $this->assertArrayHasKey('type', $initialFilters);
        $this->assertArrayHasKey('status', $initialFilters);
        $this->assertEquals('test', $initialFilters['search']);
        $this->assertEquals('modification', $initialFilters['type']);
        $this->assertEquals('pending', $initialFilters['status']);
    }

    // ========================================
    // Initial Sorting Property
    // ========================================

    public function test_computed_initial_sorting_returns_correct_structure(): void
    {
        $component = Livewire::test(EditRequestListPage::class, [
            's' => 'place',
            'd' => 'asc',
        ]);

        $initialSorting = $component->get('initialSorting');

        $this->assertIsArray($initialSorting);
        $this->assertArrayHasKey('sortBy', $initialSorting);
        $this->assertArrayHasKey('sortDirection', $initialSorting);
        $this->assertEquals('place', $initialSorting['sortBy']);
        $this->assertEquals('asc', $initialSorting['sortDirection']);
    }

    // ========================================
    // Initial Per Page Property
    // ========================================

    public function test_computed_initial_per_page_returns_correct_value(): void
    {
        $component = Livewire::test(EditRequestListPage::class, [
            'pp' => 50,
        ]);

        $initialPerPage = $component->get('initialPerPage');

        $this->assertEquals(50, $initialPerPage);
    }
}
