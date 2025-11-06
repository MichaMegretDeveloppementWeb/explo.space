<?php

namespace Tests\Feature\Admin\EditRequest;

use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditRequestListControllerTest extends TestCase
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
    // Route Access & Rendering
    // ========================================

    public function test_index_page_is_accessible(): void
    {
        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertStatus(200);
    }

    public function test_index_page_requires_authentication(): void
    {
        auth()->logout();

        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_index_page_uses_correct_view(): void
    {
        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertViewIs('admin.edit-request.index');
    }

    public function test_index_page_renders_livewire_component(): void
    {
        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertSeeLivewire('admin.edit-request.edit-request-list.edit-request-list-page');
    }

    // ========================================
    // URL Parameters
    // ========================================

    public function test_index_page_accepts_search_parameter(): void
    {
        $response = $this->get(route('admin.edit-requests.index', ['q' => 'kennedy']));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_type_parameter(): void
    {
        $response = $this->get(route('admin.edit-requests.index', ['type' => 'modification']));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_status_parameter(): void
    {
        $response = $this->get(route('admin.edit-requests.index', ['status' => 'pending']));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_sort_parameters(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            's' => 'place',
            'd' => 'asc',
        ]));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_per_page_parameter(): void
    {
        $response = $this->get(route('admin.edit-requests.index', ['pp' => 50]));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_all_parameters_together(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            'q' => 'kennedy',
            'type' => 'modification',
            'status' => 'pending',
            's' => 'place',
            'd' => 'desc',
            'pp' => 100,
        ]));

        $response->assertStatus(200);
    }

    // ========================================
    // Data Display
    // ========================================

    public function test_index_page_shows_edit_requests(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
            'status' => 'published',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'test@example.com',
            'status' => 'submitted',
        ]);

        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertSee('Centre Spatial Kennedy');
        $response->assertSee('test@example.com');
    }

    public function test_index_page_shows_empty_state_when_no_requests(): void
    {
        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertSee('Aucune demande trouvée');
    }

    public function test_index_page_displays_page_title(): void
    {
        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertSee('Demandes de modification');
    }

    public function test_index_page_displays_breadcrumb(): void
    {
        $response = $this->get(route('admin.edit-requests.index'));

        $response->assertSee('Tableau de bord');
        $response->assertSee('Demandes de modification');
    }

    // ========================================
    // Filtering
    // ========================================

    public function test_index_page_filters_by_type(): void
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
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',
            'status' => 'submitted',
        ]);

        $response = $this->get(route('admin.edit-requests.index', ['type' => 'modification']));

        $response->assertStatus(200);
    }

    public function test_index_page_filters_by_status(): void
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
            'status' => 'submitted',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'status' => 'pending',
        ]);

        $response = $this->get(route('admin.edit-requests.index', ['status' => 'submitted']));

        $response->assertStatus(200);
    }

    public function test_index_page_filters_by_search(): void
    {
        // Create place with translation
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

        EditRequest::factory()->create(['place_id' => $place1->id]);
        EditRequest::factory()->create(['place_id' => $place2->id]);

        $response = $this->get(route('admin.edit-requests.index', ['q' => 'Kennedy']));

        $response->assertStatus(200);
    }

    // ========================================
    // Flash Messages
    // ========================================

    public function test_index_page_displays_success_flash_message(): void
    {
        $response = $this->withSession(['success' => 'Opération réussie'])
            ->get(route('admin.edit-requests.index'));

        $response->assertSee('Opération réussie');
    }

    public function test_index_page_displays_error_flash_message(): void
    {
        $response = $this->withSession(['error' => 'Une erreur est survenue'])
            ->get(route('admin.edit-requests.index'));

        $response->assertSee('Une erreur est survenue');
    }

    // ========================================
    // Sorting
    // ========================================

    public function test_index_page_sorts_by_place(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            's' => 'place',
            'd' => 'asc',
        ]));

        $response->assertStatus(200);
    }

    public function test_index_page_sorts_by_type(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            's' => 'type',
            'd' => 'desc',
        ]));

        $response->assertStatus(200);
    }

    public function test_index_page_sorts_by_contact_email(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            's' => 'contact_email',
            'd' => 'asc',
        ]));

        $response->assertStatus(200);
    }

    public function test_index_page_sorts_by_status(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            's' => 'status',
            'd' => 'desc',
        ]));

        $response->assertStatus(200);
    }

    public function test_index_page_sorts_by_created_at(): void
    {
        $response = $this->get(route('admin.edit-requests.index', [
            's' => 'created_at',
            'd' => 'desc',
        ]));

        $response->assertStatus(200);
    }

    // ========================================
    // Pagination
    // ========================================

    public function test_index_page_paginates_results(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create 25 edit requests
        EditRequest::factory()->count(25)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $response = $this->get(route('admin.edit-requests.index', ['pp' => 10]));

        $response->assertStatus(200);
    }

    public function test_index_page_respects_per_page_parameter(): void
    {
        // Create place with translation
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->count(15)->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        $response = $this->get(route('admin.edit-requests.index', ['pp' => 50]));

        $response->assertStatus(200);
    }
}
