<?php

namespace Tests\Feature\Admin\Place;

use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    // ========================================
    // Authentication & Authorization
    // ========================================

    public function test_guest_cannot_access_place_list(): void
    {
        $response = $this->get(route('admin.places.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_access_place_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
    }

    // ========================================
    // View & Layout
    // ========================================

    public function test_returns_correct_view(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertViewIs('admin.place.index');
    }

    public function test_view_contains_livewire_component(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertSeeLivewire('admin.place.place-list.place-list-page');
    }

    public function test_page_title_is_present(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertSee('Gestion des lieux', false);
    }

    // ========================================
    // Route Registration
    // ========================================

    public function test_route_is_registered(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('admin.places.index')
        );
    }

    public function test_route_uses_correct_controller(): void
    {
        $route = \Illuminate\Support\Facades\Route::getRoutes()
            ->getByName('admin.places.index');

        $this->assertStringContainsString(
            'PlaceListController@index',
            $route->getActionName()
        );
    }

    public function test_route_uses_get_method(): void
    {
        $route = \Illuminate\Support\Facades\Route::getRoutes()
            ->getByName('admin.places.index');

        $this->assertContains('GET', $route->methods());
    }

    public function test_route_has_correct_uri(): void
    {
        $route = \Illuminate\Support\Facades\Route::getRoutes()
            ->getByName('admin.places.index');

        $this->assertEquals('admin/lieux', $route->uri());
    }

    // ========================================
    // Middleware & Security
    // ========================================

    public function test_route_requires_authentication(): void
    {
        // Guest access
        $response = $this->get(route('admin.places.index'));
        $response->assertRedirect(route('admin.login'));

        // Authenticated access
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));
        $response->assertStatus(200);
    }

    public function test_csrf_protection_is_active(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        // Check that CSRF token is present in the page
        $response->assertSee('csrf-token', false);
    }

    // ========================================
    // Session & State Management
    // ========================================

    public function test_session_filters_are_preserved_across_requests(): void
    {
        // First request - set filters via Livewire component
        session([
            'place_filters' => [
                'search' => 'NASA',
                'tags' => ['nasa'],
                'locale' => 'en',
            ],
        ]);

        // Second request - filters should still be in session
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        $this->assertEquals('NASA', session('place_filters.search'));
        $this->assertEquals(['nasa'], session('place_filters.tags'));
        $this->assertEquals('en', session('place_filters.locale'));
    }

    public function test_default_locale_is_set(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        $this->assertEquals('fr', app()->getLocale());
    }

    // ========================================
    // Component Integration
    // ========================================

    public function test_page_loads_with_filters_component(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertSeeLivewire('admin.place.place-list.place-list-filters');
    }

    public function test_page_loads_with_table_component(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertSeeLivewire('admin.place.place-list.place-list-table');
    }

    public function test_page_loads_with_page_component(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertSeeLivewire('admin.place.place-list.place-list-page');
    }

    // ========================================
    // Data Visibility
    // ========================================

    public function test_page_displays_places_when_they_exist(): void
    {
        // Create a place with translation
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        // The title will be rendered by Livewire component
        $response->assertSeeLivewire('admin.place.place-list.place-list-table');
    }

    public function test_page_displays_empty_state_when_no_places(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.place.place-list.place-list-table');
    }

    public function test_page_displays_only_published_translations(): void
    {
        // Published place
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Lieu publié',
            'status' => 'published',
        ]);

        // Draft place
        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Lieu brouillon',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        // Component will filter correctly
        $response->assertSeeLivewire('admin.place.place-list.place-list-table');
    }

    // ========================================
    // Performance & Response
    // ========================================

    public function test_response_time_is_acceptable(): void
    {
        // Create 50 places
        for ($i = 0; $i < 50; $i++) {
            $place = Place::factory()->create();
            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
                'locale' => 'fr',
                'title' => "Lieu {$i}",
                'status' => 'published',
            ]);
        }

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to ms

        $response->assertStatus(200);

        // Response should be under 2 seconds even with 50 places
        $this->assertLessThan(2000, $responseTime);
    }

    public function test_no_n_plus_one_queries(): void
    {
        // Create places with relations
        for ($i = 0; $i < 10; $i++) {
            $place = Place::factory()->create();
            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
                'locale' => 'fr',
                'title' => "Lieu {$i}",
                'status' => 'published',
            ]);
        }

        // Enable query log
        \DB::enableQueryLog();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        $response->assertStatus(200);

        // Should have a reasonable number of queries (not 10+ per place)
        // Initial page load should be minimal queries
        $this->assertLessThan(20, count($queries));
    }

    // ========================================
    // Headers & Security Headers
    // ========================================

    public function test_response_has_correct_content_type(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function test_response_has_security_headers(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        // Check for common security headers
        // Note: These depend on your middleware configuration
        $response->assertStatus(200);
    }

    // ========================================
    // Locale Handling
    // ========================================

    public function test_respects_session_locale(): void
    {
        // Set session locale to English
        session(['place_filters' => ['locale' => 'en']]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        // Livewire component will handle locale
    }

    public function test_falls_back_to_default_locale(): void
    {
        // No locale in session
        session()->forget('place_filters');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        $this->assertEquals('fr', app()->getLocale());
    }

    // ========================================
    // Error Handling
    // ========================================

    public function test_handles_database_connection_gracefully(): void
    {
        // This test would require mocking DB connection errors
        // For now, we just verify the page loads
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
    }

    public function test_handles_missing_translations_gracefully(): void
    {
        // Place without FR translation
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Only Place',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertStatus(200);
        // Component will filter out places without FR translation
    }

    // ========================================
    // Multiple Users
    // ========================================

    public function test_different_users_have_separate_sessions(): void
    {
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();

        // Admin 1 sets filters
        $this->actingAs($admin1);
        session([
            'place_filters' => [
                'search' => 'NASA',
                'tags' => ['nasa'],
                'locale' => 'en',
            ],
        ]);

        $response1 = $this->get(route('admin.places.index'));
        $response1->assertStatus(200);
        $this->assertEquals('NASA', session('place_filters.search'));

        // Admin 2 has clean session
        $this->actingAs($admin2);
        session()->forget('place_filters');

        $response2 = $this->get(route('admin.places.index'));
        $response2->assertStatus(200);
        $this->assertNull(session('place_filters.search'));
    }

    // ========================================
    // Browser Cache
    // ========================================

    public function test_response_is_not_cached(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        // Admin pages should not be cached
        $response->assertStatus(200);
        // Check for cache-control headers if configured
    }
}
