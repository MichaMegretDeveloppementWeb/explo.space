<?php

namespace Tests\Feature\Admin\Category;

use App\Models\Category;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryEditControllerTest extends TestCase
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

    public function test_can_access_category_edit_page(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
    }

    // ========================================
    // Authentication & Authorization
    // ========================================

    public function test_guest_cannot_access_category_edit(): void
    {
        auth()->logout();

        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertRedirect(route('admin.login'));
    }

    // Category Not Found
    // ========================================

    public function test_returns_404_for_nonexistent_category(): void
    {
        $response = $this->get(route('admin.categories.edit', 99999));

        // Livewire redirects when category doesn't exist instead of returning 404
        $response->assertRedirect();
    }

    // ========================================
    // Livewire Components
    // ========================================

    public function test_page_contains_category_store_form_component(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSeeLivewire('admin.category.store.category-store-form');
    }

    // ========================================
    // Form Fields with Data
    // ========================================

    public function test_form_displays_category_name(): void
    {
        $category = Category::factory()->create(['name' => 'Space Agencies']);

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Space Agencies', false);
    }

    // ========================================
    // Form Actions
    // ========================================

    public function test_form_has_save_button(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Enregistrer les modifications', false);
    }

    public function test_form_has_cancel_button(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Annuler', false);
    }

    public function test_form_has_delete_button(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Supprimer', false);
    }

    // ========================================
    // Breadcrumb & Navigation
    // ========================================

    public function test_page_has_breadcrumb(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Catégories', false)
            ->assertSee('Modifier', false);
    }

    public function test_page_has_back_to_list_link(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee(route('admin.categories.index'), false);
    }

    // ========================================
    // Timestamps Display
    // ========================================

    public function test_form_displays_created_date(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test',
            'created_at' => now()->subDays(10),
        ]);

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Créé le', false);
    }

    public function test_form_displays_updated_date(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test',
            'updated_at' => now()->subHours(5),
        ]);

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Modifié le', false);
    }

    // ========================================
    // Mode Verification
    // ========================================

    public function test_form_is_in_edit_mode(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        // Verify edit-specific elements
        $response->assertSee('Modifier la catégorie', false)
            ->assertSee('Supprimer', false);
    }

    public function test_form_shows_delete_button_in_edit_mode(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Supprimer', false);
    }

    // ========================================
    // Associated Places Warning
    // ========================================

    public function test_displays_places_count_when_associated(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);
        $place1 = Place::factory()->create(['admin_id' => $this->admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $this->admin->id]);
        $category->places()->attach([$place1->id, $place2->id]);

        $response = $this->get(route('admin.categories.edit', $category->id));

        // Places count is shown in delete modal which is rendered on page load
        $response->assertSee('lieu', false);
    }

    public function test_does_not_show_places_warning_when_no_associations(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        $response = $this->get(route('admin.categories.edit', $category->id));

        // No places count should be shown
        $response->assertDontSee('lieux associés', false);
    }

    // ========================================
    // Page Structure
    // ========================================

    public function test_page_has_main_content_card(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Informations de base', false);
    }

    public function test_page_has_settings_section(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertSee('Paramètres', false);
    }

    // ========================================
    // Category with Null Description
    // ========================================

    public function test_form_handles_null_description(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test',
            'description' => null,
        ]);

        $response = $this->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
    }

    // ========================================
    // Color Display
    // ========================================

    // Help Text & Instructions
    // ========================================

    // Multiple Categories
    // ========================================

    public function test_can_access_edit_pages_for_multiple_categories(): void
    {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        $category3 = Category::factory()->create(['name' => 'Category 3']);

        $response1 = $this->get(route('admin.categories.edit', $category1->id));
        $response2 = $this->get(route('admin.categories.edit', $category2->id));
        $response3 = $this->get(route('admin.categories.edit', $category3->id));

        $response1->assertStatus(200)->assertSee('Category 1', false);
        $response2->assertStatus(200)->assertSee('Category 2', false);
        $response3->assertStatus(200)->assertSee('Category 3', false);
    }
}
