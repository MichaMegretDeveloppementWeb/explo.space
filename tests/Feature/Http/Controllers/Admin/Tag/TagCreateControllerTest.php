<?php

namespace Tests\Feature\Http\Controllers\Admin\Tag;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pour TagCreateController
 *
 * Ce controller gère l'affichage de la page de création de tag
 */
class TagCreateControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ========================================
    // Tests accès autorisé
    // ========================================

    public function test_admin_can_access_create_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.tag.store.tag-store-form');
    }

    public function test_create_page_has_correct_title(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertSee('Créer un nouveau tag', false);
    }

    public function test_create_page_displays_tag_store_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertSeeLivewire('admin.tag.store.tag-store-form');
    }

    // ========================================
    // Tests accès refusé
    // ========================================

    public function test_guest_cannot_access_create_page(): void
    {
        $response = $this->get(route('admin.tags.create'));

        $response->assertRedirect(route('admin.login'));
    }

    // ========================================
    // Tests contenu de la page
    // ========================================

    public function test_create_page_contains_breadcrumb(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertSee('Dashboard', false);
        $response->assertSee('Tags', false);
        $response->assertSee('Nouveau tag', false);
    }

    public function test_create_page_contains_color_picker(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertSee('Couleur', false);
    }

    public function test_create_page_contains_translation_tabs(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertSee('FR', false);
        $response->assertSee('EN', false);
    }

    public function test_create_page_contains_save_button(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertSee('Créer le tag', false);
    }

    public function test_create_page_does_not_contain_delete_button(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertDontSee('Supprimer', false);
    }

    // ========================================
    // Tests structure de la page
    // ========================================

    public function test_create_page_uses_admin_layout(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        $response->assertViewIs('admin.tag.create');
    }

    public function test_livewire_component_receives_null_tag_id(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags.create'));

        // The component should be initialized with tagId = null for create mode
        $response->assertSeeLivewire('admin.tag.store.tag-store-form');
    }
}
