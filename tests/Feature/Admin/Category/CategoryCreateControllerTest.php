<?php

namespace Tests\Feature\Admin\Category;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCreateControllerTest extends TestCase
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

    public function test_can_access_category_create_page(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertStatus(200);
    }

    // ========================================
    // Authentication & Authorization
    // ========================================

    public function test_guest_cannot_access_category_create(): void
    {
        auth()->logout();

        $response = $this->get(route('admin.categories.create'));

        $response->assertRedirect(route('admin.login'));
    }

    // Livewire Components
    // ========================================

    public function test_page_contains_category_store_form_component(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSeeLivewire('admin.category.store.category-store-form');
    }

    // ========================================
    // Form Fields
    // ========================================

    public function test_form_has_name_field(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Nom', false);
    }

    // Form Actions
    // ========================================

    public function test_form_has_save_button(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Créer la catégorie', false);
    }

    public function test_form_has_cancel_button(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Annuler', false);
    }

    public function test_cancel_button_links_to_category_list(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee(route('admin.categories.index'), false);
    }

    // ========================================
    // Breadcrumb & Navigation
    // ========================================

    public function test_page_has_breadcrumb(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Catégories', false)
            ->assertSee('Nouvelle catégorie', false);
    }

    public function test_page_has_back_to_list_link(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee(route('admin.categories.index'), false);
    }

    // ========================================
    // Default Values
    // ========================================

    public function test_form_has_default_color_value(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('#3B82F6', false);
    }

    public function test_form_is_active_by_default(): void
    {
        $response = $this->get(route('admin.categories.create'));

        // Check that the active toggle is on by default
        $response->assertSee('Catégorie active', false);
    }

    // ========================================
    // Help Text & Instructions
    // ========================================

    public function test_form_displays_name_help_text(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Ex: Fusées', false);
    }

    public function test_form_displays_slug_help_text(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Le slug est généré automatiquement', false);
    }

    public function test_form_displays_description_help_text(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Description interne de la catégorie', false);
    }

    // ========================================
    // Page Structure
    // ========================================

    public function test_page_has_main_content_card(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Informations de base', false);
    }

    public function test_page_has_settings_section(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertSee('Paramètres', false);
    }

    // ========================================
    // Mode Verification
    // ========================================

    public function test_form_is_in_create_mode(): void
    {
        $response = $this->get(route('admin.categories.create'));

        // Verify create-specific elements
        $response->assertSee('Créer une nouvelle catégorie', false)
            ->assertDontSee('Supprimer', false);
    }

    public function test_form_does_not_show_delete_button(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertDontSee('Supprimer définitivement', false);
    }

    public function test_form_does_not_show_created_date(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertDontSee('Créée le', false);
    }

    public function test_form_does_not_show_updated_date(): void
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertDontSee('Dernière modification', false);
    }
}
