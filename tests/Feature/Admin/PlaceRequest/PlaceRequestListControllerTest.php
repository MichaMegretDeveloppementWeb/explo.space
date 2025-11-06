<?php

namespace Tests\Feature\Admin\PlaceRequest;

use App\Models\PlaceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceRequestListControllerTest extends TestCase
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
        $response = $this->get(route('admin.place-requests.index'));

        $response->assertStatus(200);
    }

    public function test_index_page_requires_authentication(): void
    {
        auth()->logout();

        $response = $this->get(route('admin.place-requests.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_index_page_uses_correct_view(): void
    {
        $response = $this->get(route('admin.place-requests.index'));

        $response->assertViewIs('admin.place-request.index');
    }

    public function test_index_page_renders_livewire_component(): void
    {
        $response = $this->get(route('admin.place-requests.index'));

        $response->assertSeeLivewire('admin.place-request.place-request-list.place-request-list-page');
    }

    // ========================================
    // URL Parameters
    // ========================================

    public function test_index_page_accepts_status_parameter(): void
    {
        $response = $this->get(route('admin.place-requests.index', ['status' => 'pending']));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_multiple_status_parameters(): void
    {
        $response = $this->get(route('admin.place-requests.index', ['status' => 'pending,submitted']));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_sort_parameters(): void
    {
        $response = $this->get(route('admin.place-requests.index', [
            'sortBy' => 'title',
            'sortDirection' => 'asc',
        ]));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_per_page_parameter(): void
    {
        $response = $this->get(route('admin.place-requests.index', ['perPage' => 50]));

        $response->assertStatus(200);
    }

    public function test_index_page_accepts_all_parameters_together(): void
    {
        $response = $this->get(route('admin.place-requests.index', [
            'status' => 'pending,submitted',
            'sortBy' => 'created_at',
            'sortDirection' => 'desc',
            'perPage' => 30,
        ]));

        $response->assertStatus(200);
    }

    // ========================================
    // Data Display
    // ========================================

    public function test_index_page_shows_place_requests(): void
    {
        PlaceRequest::factory()->create([
            'title' => 'Centre Spatial Kennedy',
            'status' => 'submitted',
        ]);

        $response = $this->get(route('admin.place-requests.index'));

        $response->assertSee('Centre Spatial Kennedy');
    }

    public function test_index_page_shows_empty_state_when_no_requests(): void
    {
        $response = $this->get(route('admin.place-requests.index'));

        $response->assertSee('Aucune proposition trouvée');
    }

    public function test_index_page_displays_page_title(): void
    {
        $response = $this->get(route('admin.place-requests.index'));

        $response->assertSee('Propositions de lieux');
    }

    public function test_index_page_displays_breadcrumb(): void
    {
        $response = $this->get(route('admin.place-requests.index'));

        $response->assertSee('Tableau de bord');
        $response->assertSee('Propositions de lieux');
    }

    // ========================================
    // Flash Messages
    // ========================================

    public function test_index_page_displays_success_flash_message(): void
    {
        $response = $this->withSession(['success' => 'Opération réussie'])
            ->get(route('admin.place-requests.index'));

        $response->assertSee('Opération réussie');
    }

    public function test_index_page_displays_error_flash_message(): void
    {
        $response = $this->withSession(['error' => 'Une erreur est survenue'])
            ->get(route('admin.place-requests.index'));

        $response->assertSee('Une erreur est survenue');
    }
}
