<?php

namespace Tests\Livewire\Admin\Category\CategoryList;

use App\Livewire\Admin\Category\CategoryList\CategoryListTable;
use App\Models\Category;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryListTableTest extends TestCase
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
        return Livewire::test(CategoryListTable::class, [
            'initialFilters' => array_merge([
                'search' => '',
                'activeFilter' => 'all',
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
            ->assertSet('activeFilter', 'all')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 20);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        $this->testComponent([
            'search' => 'test',
            'activeFilter' => 'active',
        ])
            ->assertSet('search', 'test')
            ->assertSet('activeFilter', 'active');
    }

    public function test_mount_accepts_initial_sorting(): void
    {
        $this->testComponent([], [
            'sortBy' => 'name',
            'sortDirection' => 'asc',
        ])
            ->assertSet('sortBy', 'name')
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
            ->assertViewIs('livewire.admin.category.category-list.category-list-table');
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
        // Create many categories
        Category::factory()->count(30)->create();

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change perPage - should reset to page 1
        $component->call('updatePerPage', 20);

        $categories = $component->viewData('categories');
        $this->assertEquals(1, $categories->currentPage());
    }

    public function test_pagination_resets_when_filters_change(): void
    {
        // Create multiple categories
        Category::factory()->count(15)->create(['is_active' => true]);

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change filters - should reset to page 1
        $component->dispatch('filters:updated', search: '', activeFilter: 'inactive');

        $component->assertSet('activeFilter', 'inactive');
    }

    // ========================================
    // Sorting
    // ========================================

    public function test_sort_by_name(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'desc')
            ->assertDispatched('sorting:updated', sortBy: 'name', sortDirection: 'desc');
    }

    public function test_sort_by_created_at(): void
    {
        // Note: created_at is the default sort column with desc direction
        // So clicking on it will toggle to asc
        $this->testComponent()
            ->call('sortByColumn', 'created_at')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'asc')
            ->assertDispatched('sorting:updated', sortBy: 'created_at', sortDirection: 'asc');
    }

    public function test_sort_by_updated_at(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'updated_at')
            ->assertSet('sortBy', 'updated_at')
            ->assertSet('sortDirection', 'desc')
            ->assertDispatched('sorting:updated', sortBy: 'updated_at', sortDirection: 'desc');
    }

    public function test_sort_by_is_active(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'is_active')
            ->assertSet('sortBy', 'is_active')
            ->assertSet('sortDirection', 'desc')
            ->assertDispatched('sorting:updated', sortBy: 'is_active', sortDirection: 'desc');
    }

    public function test_sort_by_places_count(): void
    {
        $this->testComponent()
            ->call('sortByColumn', 'places_count')
            ->assertSet('sortBy', 'places_count')
            ->assertSet('sortDirection', 'desc')
            ->assertDispatched('sorting:updated', sortBy: 'places_count', sortDirection: 'desc');
    }

    public function test_sort_direction_toggles_on_same_column(): void
    {
        $component = $this->testComponent();

        // First click: set to desc
        $component->call('sortByColumn', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'desc');

        // Second click: toggle to asc
        $component->call('sortByColumn', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'asc');

        // Third click: toggle back to desc
        $component->call('sortByColumn', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sorting_resets_pagination(): void
    {
        // Create many categories
        Category::factory()->count(30)->create();

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change sorting - should reset to page 1
        $component->call('sortByColumn', 'name');

        $categories = $component->viewData('categories');
        $this->assertEquals(1, $categories->currentPage());
    }

    // ========================================
    // Data Loading & Filtering
    // ========================================

    public function test_loads_categories(): void
    {
        // Create a category
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'is_active' => true,
        ]);

        $component = $this->testComponent();

        $categories = $component->viewData('categories');

        $this->assertEquals(1, $categories->count());
        $this->assertEquals('Test Category', $categories->first()->name);
    }

    public function test_filters_by_search_term(): void
    {
        // Create categories
        Category::factory()->create(['name' => 'Space Agencies']);
        Category::factory()->create(['name' => 'Launch Sites']);

        $component = $this->testComponent(['search' => 'Space']);

        $categories = $component->viewData('categories');

        $this->assertEquals(1, $categories->count());
        $this->assertEquals('Space Agencies', $categories->first()->name);
    }

    public function test_filters_by_active_status(): void
    {
        // Create active and inactive categories
        Category::factory()->create([
            'name' => 'Active Category',
            'is_active' => true,
        ]);

        Category::factory()->create([
            'name' => 'Inactive Category',
            'is_active' => false,
        ]);

        $component = $this->testComponent(['activeFilter' => 'active']);

        $categories = $component->viewData('categories');

        $this->assertEquals(1, $categories->count());
        $this->assertTrue($categories->first()->is_active);
    }

    public function test_filters_by_inactive_status(): void
    {
        // Create active and inactive categories
        Category::factory()->create([
            'name' => 'Active Category',
            'is_active' => true,
        ]);

        Category::factory()->create([
            'name' => 'Inactive Category',
            'is_active' => false,
        ]);

        $component = $this->testComponent(['activeFilter' => 'inactive']);

        $categories = $component->viewData('categories');

        $this->assertEquals(1, $categories->count());
        $this->assertFalse($categories->first()->is_active);
    }

    public function test_shows_all_categories_when_filter_is_all(): void
    {
        // Create active and inactive categories
        Category::factory()->create(['is_active' => true]);
        Category::factory()->create(['is_active' => false]);

        $component = $this->testComponent(['activeFilter' => 'all']);

        $categories = $component->viewData('categories');

        $this->assertEquals(2, $categories->count());
    }

    public function test_loads_places_count(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        // Create places and associate with category
        $place1 = Place::factory()->create(['admin_id' => $this->admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $this->admin->id]);

        $category->places()->attach([$place1->id, $place2->id]);

        $component = $this->testComponent();

        $categories = $component->viewData('categories');

        $this->assertEquals(1, $categories->count());
        $this->assertEquals(2, $categories->first()->places_count);
    }

    // ========================================
    // Event Listeners
    // ========================================

    public function test_updates_filters_from_event(): void
    {
        $component = $this->testComponent();

        $component->dispatch('filters:updated', search: 'test', activeFilter: 'active');

        $component->assertSet('search', 'test')
            ->assertSet('activeFilter', 'active');
    }

    // ========================================
    // Empty States
    // ========================================

    public function test_shows_empty_state_when_no_categories(): void
    {
        $this->testComponent()
            ->assertSee('Aucune catégorie trouvée');
    }

    public function test_shows_empty_state_when_search_returns_no_results(): void
    {
        // Create a category
        Category::factory()->create(['name' => 'Space']);

        $this->testComponent(['search' => 'NonexistentCategory'])
            ->assertSee('Aucune catégorie trouvée');
    }

    // ========================================
    // View Rendering
    // ========================================

    public function test_view_displays_category_name(): void
    {
        Category::factory()->create(['name' => 'Test Category']);

        $this->testComponent()
            ->assertSee('Test Category');
    }

    public function test_view_displays_category_slug(): void
    {
        Category::factory()->create([
            'name' => 'Test',
            'slug' => 'test-category',
        ]);

        $this->testComponent()
            ->assertSee('test-category');
    }

    public function test_view_displays_category_color(): void
    {
        Category::factory()->create([
            'name' => 'Test',
            'color' => '#FF5733',
        ]);

        $this->testComponent()
            ->assertSee('#FF5733');
    }

    public function test_view_displays_active_status(): void
    {
        Category::factory()->create([
            'name' => 'Test',
            'is_active' => true,
        ]);

        $this->testComponent()
            ->assertSee('Active');
    }

    public function test_view_displays_inactive_status(): void
    {
        Category::factory()->create([
            'name' => 'Test',
            'is_active' => false,
        ]);

        $this->testComponent()
            ->assertSee('Inactive');
    }

    public function test_view_displays_places_count(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        // Create and associate place
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        $category->places()->attach($place->id);

        $this->testComponent()
            ->assertSee('1 lieu');
    }

    public function test_view_displays_pagination_info(): void
    {
        // Create 25 categories
        Category::factory()->count(25)->create();

        $this->testComponent([], [], 20)
            ->assertSee('1-20 sur 25');
    }
}
