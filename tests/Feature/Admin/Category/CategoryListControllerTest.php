<?php

namespace Tests\Feature\Admin\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    // ========================================
    // Access & Response
    // ========================================

    public function test_can_access_category_list_page(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    // ========================================
    // Authentication & Authorization
    // ========================================

    public function test_guest_cannot_access_category_list(): void
    {
        auth()->logout();

        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // Livewire Components
    // ========================================

    public function test_page_contains_category_list_page_component(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertSeeLivewire('admin.category.category-list.category-list-page');
    }

    // ========================================
    // Content Display
    // ========================================

    public function test_page_displays_categories_table(): void
    {
        Category::factory()->create(['name' => 'Test Category']);

        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Test Category');
    }

    public function test_page_displays_multiple_categories(): void
    {
        Category::factory()->create(['name' => 'Category One']);
        Category::factory()->create(['name' => 'Category Two']);
        Category::factory()->create(['name' => 'Category Three']);

        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Category One')
            ->assertSee('Category Two')
            ->assertSee('Category Three');
    }

    public function test_page_shows_empty_state_when_no_categories(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Aucune catégorie trouvée');
    }

    // ========================================
    // Navigation & Actions
    // ========================================

    public function test_page_has_create_category_button(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Nouvelle catégorie');
    }

    public function test_page_has_edit_links_for_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        $response = $this->get(route('admin.categories.index'));

        $response->assertSee(route('admin.categories.edit', $category->id), false);
    }

    // ========================================
    // Filters & Search
    // ========================================

    public function test_page_has_search_filter(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Rechercher');
    }

    public function test_page_has_status_filter(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Toutes les catégories')
            ->assertSee('Actives')
            ->assertSee('Inactives');
    }

    // ========================================
    // Category Information Display
    // ========================================

    public function test_displays_category_name(): void
    {
        Category::factory()->create(['name' => 'Space Agencies']);

        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Space Agencies');
    }

    // ========================================
    // URL Query Parameters
    // ========================================

    public function test_accepts_search_query_parameter(): void
    {
        Category::factory()->create(['name' => 'Space Agencies']);
        Category::factory()->create(['name' => 'Launch Sites']);

        $response = $this->get(route('admin.categories.index', ['q' => 'Space']));

        $response->assertStatus(200);
    }

    public function test_accepts_status_filter_query_parameter(): void
    {
        $response = $this->get(route('admin.categories.index', ['active' => 'active']));

        $response->assertStatus(200);
    }

    public function test_accepts_sort_query_parameters(): void
    {
        $response = $this->get(route('admin.categories.index', [
            's' => 'name',
            'd' => 'asc',
        ]));

        $response->assertStatus(200);
    }

    public function test_accepts_pagination_query_parameter(): void
    {
        $response = $this->get(route('admin.categories.index', ['pp' => 50]));

        $response->assertStatus(200);
    }

    public function test_accepts_all_query_parameters_together(): void
    {
        $response = $this->get(route('admin.categories.index', [
            'q' => 'test',
            'active' => 'active',
            's' => 'name',
            'd' => 'asc',
            'pp' => 50,
        ]));

        $response->assertStatus(200);
    }

    // ========================================
    // Performance
    // ========================================

    // Breadcrumb & Navigation
    // ========================================

    public function test_page_has_breadcrumb(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertSee('Catégories', false);
    }
}
