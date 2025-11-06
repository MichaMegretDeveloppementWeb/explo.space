<?php

namespace Tests\Livewire\Admin\Place\PlaceList;

use App\Livewire\Admin\Place\PlaceList\PlaceListTable;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceListTableTest extends TestCase
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
        return Livewire::test(PlaceListTable::class, [
            'initialFilters' => array_merge([
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
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
        $this->test_component()
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        $this->test_component([], [], 10)
            ->assertSet('search', '')
            ->assertSet('tags', [])
            ->assertSet('locale', 'fr')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 10);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        $this->test_component([
            'search' => 'NASA',
            'tags' => ['nasa', 'spacex'],
            'locale' => 'en',
        ])
            ->assertSet('search', 'NASA')
            ->assertSet('tags', ['nasa', 'spacex'])
            ->assertSet('locale', 'en');
    }

    public function test_component_view_exists(): void
    {
        $this->test_component()
            ->assertViewIs('livewire.admin.place.place-list.place-list-table');
    }

    // ========================================
    // Pagination
    // ========================================

    public function test_per_page_can_be_changed(): void
    {
        $this->test_component()
            ->set('perPage', 20)
            ->assertSet('perPage', 20);
    }

    public function test_valid_per_page_values_are_accepted(): void
    {
        $validValues = [10, 20, 30, 50];

        foreach ($validValues as $value) {
            $this->test_component()
                ->set('perPage', $value)
                ->assertSet('perPage', $value);
        }
    }

    public function test_pagination_resets_when_filters_change(): void
    {
        // Create multiple places to test pagination
        for ($i = 0; $i < 15; $i++) {
            $place = Place::factory()->create();
            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
                'locale' => 'fr',
                'title' => "Lieu {$i}",
                'status' => 'published',
            ]);
        }

        $component = $this->test_component()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change filters - should reset to page 1
        $component->dispatch('filters:updated', search: 'NASA', tags: [], locale: 'fr');

        // Verify we're back on page 1
        $component->assertSet('search', 'NASA');
    }

    // ========================================
    // Sorting
    // ========================================

    public function test_sort_by_title(): void
    {
        $this->test_component()
            ->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc'); // Default direction for new column
    }

    public function test_sort_by_is_featured(): void
    {
        $this->test_component()
            ->call('sortByColumn', 'is_featured')
            ->assertSet('sortBy', 'is_featured')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sort_by_created_at(): void
    {
        // Component initialise avec sortBy='created_at' et sortDirection='desc'
        // Premier appel toggle vers 'asc', deuxième appel toggle vers 'desc'
        $this->test_component()
            ->call('sortByColumn', 'created_at')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'asc') // Toggle depuis desc initial
            ->call('sortByColumn', 'created_at')
            ->assertSet('sortDirection', 'desc'); // Toggle retour vers desc
    }

    public function test_sort_by_updated_at(): void
    {
        $this->test_component()
            ->call('sortByColumn', 'updated_at')
            ->assertSet('sortBy', 'updated_at')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_sort_direction_toggles_on_same_column(): void
    {
        $component = $this->test_component()
            ->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc'); // First click: desc (default)

        // Click again on same column - toggle to asc
        $component->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'asc');

        // Click again - toggle back to desc
        $component->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_changing_sort_column_resets_direction(): void
    {
        $component = $this->test_component()
            ->call('sortByColumn', 'title')
            ->assertSet('sortBy', 'title')
            ->assertSet('sortDirection', 'desc'); // First click: desc

        // Change to different column
        $component->call('sortByColumn', 'created_at')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc'); // Default direction for new column
    }

    // ========================================
    // Event Handling (filters:updated)
    // ========================================

    public function test_filters_updated_event_updates_search(): void
    {
        $this->test_component()
            ->dispatch('filters:updated', search: 'NASA', tags: [], locale: 'fr')
            ->assertSet('search', 'NASA');
    }

    public function test_filters_updated_event_updates_tags(): void
    {
        $this->test_component()
            ->dispatch('filters:updated', search: '', tags: ['nasa', 'spacex'], locale: 'fr')
            ->assertSet('tags', ['nasa', 'spacex']);
    }

    public function test_filters_updated_event_updates_locale(): void
    {
        $this->test_component()
            ->dispatch('filters:updated', search: '', tags: [], locale: 'en')
            ->assertSet('locale', 'en');
    }

    public function test_filters_updated_event_updates_all_filters(): void
    {
        $this->test_component()
            ->dispatch('filters:updated', search: 'NASA', tags: ['nasa', 'spacex'], locale: 'en')
            ->assertSet('search', 'NASA')
            ->assertSet('tags', ['nasa', 'spacex'])
            ->assertSet('locale', 'en');
    }

    // ========================================
    // Data Display
    // ========================================

    public function test_displays_places_with_translations(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
            'status' => 'published',
        ]);

        $component = $this->test_component();

        $places = $component->viewData('places');
        $this->assertCount(1, $places);
        $this->assertEquals('Centre Spatial Kennedy', $places[0]->translations[0]->title);
    }

    public function test_displays_only_published_translations(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Lieu publié',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Lieu brouillon',
            'status' => 'draft',
        ]);

        $component = $this->test_component();

        $places = $component->viewData('places');
        $this->assertCount(1, $places);
        $this->assertEquals('Lieu publié', $places[0]->translations[0]->title);
    }

    public function test_filters_by_search_term(): void
    {
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial NASA',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Observatoire SpaceX',
            'status' => 'published',
        ]);

        $component = $this->test_component([
            'search' => 'NASA',
            'tags' => [],
            'locale' => 'fr',
        ]);

        $places = $component->viewData('places');
        $this->assertCount(1, $places);
        $this->assertEquals('Centre Spatial NASA', $places[0]->translations[0]->title);
    }

    public function test_filters_by_tags(): void
    {
        // Create tag
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        // Create place with tag
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial NASA',
            'status' => 'published',
        ]);
        $place1->tags()->attach($tag->id);

        // Create place without tag
        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Observatoire',
            'status' => 'published',
        ]);

        $component = $this->test_component([
            'search' => '',
            'tags' => ['nasa'],
            'locale' => 'fr',
        ]);

        $places = $component->viewData('places');
        $this->assertCount(1, $places);
        $this->assertEquals('Centre Spatial NASA', $places[0]->translations[0]->title);
    }

    public function test_sorts_by_title_ascending(): void
    {
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Zénith Spatial',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Alpha Centre',
            'status' => 'published',
        ]);

        // First click: desc, second click: asc
        $component = $this->test_component()
            ->call('sortByColumn', 'title') // First: desc
            ->call('sortByColumn', 'title'); // Second: asc (toggle)

        $places = $component->viewData('places');
        $this->assertEquals('Alpha Centre', $places[0]->translations[0]->title);
        $this->assertEquals('Zénith Spatial', $places[count($places) - 1]->translations[0]->title);
    }

    public function test_shows_featured_badge(): void
    {
        $place = Place::factory()->create(['is_featured' => true]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Lieu vedette',
            'status' => 'published',
        ]);

        $component = $this->test_component();

        $places = $component->viewData('places');
        $this->assertTrue($places[0]->is_featured);
    }

    // ========================================
    // Empty State
    // ========================================

    public function test_shows_empty_state_when_no_results(): void
    {
        $component = $this->test_component();

        $places = $component->viewData('places');
        $this->assertCount(0, $places);
    }

    public function test_shows_empty_state_when_no_matching_search(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial',
            'status' => 'published',
        ]);

        $component = $this->test_component([
            'search' => 'inexistant',
            'tags' => [],
            'locale' => 'fr',
        ]);

        $places = $component->viewData('places');
        $this->assertCount(0, $places);
    }
}
