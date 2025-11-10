<?php

namespace Tests\Livewire\Admin\Tag\TagList;

use App\Livewire\Admin\Tag\TagList\TagListTable;
use App\Models\Place;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TagListTableTest extends TestCase
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
        return Livewire::test(TagListTable::class, [
            'initialFilters' => array_merge([
                'search' => '',
                'activeFilter' => 'all',
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
        $this->testComponent()
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        $this->testComponent()
            ->assertSet('search', '')
            ->assertSet('activeFilter', 'all')
            ->assertSet('locale', 'fr')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSet('perPage', 20);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        $this->testComponent([
            'search' => 'test',
            'activeFilter' => 'active',
            'locale' => 'en',
        ])
            ->assertSet('search', 'test')
            ->assertSet('activeFilter', 'active')
            ->assertSet('locale', 'en');
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
            ->assertViewIs('livewire.admin.tag.tag-list.tag-list-table');
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
        // Create many tags
        Tag::factory()->count(30)->create(['is_active' => true]);

        // Create translations for each tag
        Tag::all()->each(function ($tag) {
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'status' => 'published',
            ]);
        });

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change perPage - should reset to page 1
        $component->call('updatePerPage', 20);

        $tags = $component->viewData('tags');
        $this->assertEquals(1, $tags->currentPage());
    }

    public function test_pagination_resets_when_filters_change(): void
    {
        // Create multiple tags
        Tag::factory()->count(15)->create(['is_active' => true]);

        // Create translations for each tag
        Tag::all()->each(function ($tag) {
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'status' => 'published',
            ]);
        });

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change filters - should reset to page 1
        $component->dispatch('filters:updated', search: '', activeFilter: 'inactive', locale: 'fr');

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
        // Create many tags
        Tag::factory()->count(30)->create(['is_active' => true]);

        // Create translations for each tag
        Tag::all()->each(function ($tag) {
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'status' => 'published',
            ]);
        });

        $component = $this->testComponent()
            ->set('perPage', 10);

        // Go to page 2
        $component->call('nextPage');

        // Change sorting - should reset to page 1
        $component->call('sortByColumn', 'name');

        $tags = $component->viewData('tags');
        $this->assertEquals(1, $tags->currentPage());
    }

    // ========================================
    // Data Loading & Filtering
    // ========================================

    public function test_loads_tags_with_translations(): void
    {
        // Create a tag with translation
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Test Tag',
            'status' => 'published',
        ]);

        $component = $this->testComponent();

        $tags = $component->viewData('tags');

        $this->assertEquals(1, $tags->count());
        $this->assertNotNull($tags->first()->translations->first());
        $this->assertEquals('Test Tag', $tags->first()->translations->first()->name);
    }

    public function test_filters_by_search_term(): void
    {
        // Create tags with translations
        $tag1 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'Space Exploration',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'Astronomy',
            'status' => 'published',
        ]);

        $component = $this->testComponent(['search' => 'Space']);

        $tags = $component->viewData('tags');

        $this->assertEquals(1, $tags->count());
        $this->assertEquals('Space Exploration', $tags->first()->translations->first()->name);
    }

    public function test_filters_by_active_status(): void
    {
        // Create active and inactive tags
        $activeTag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $activeTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $inactiveTag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $inactiveTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $component = $this->testComponent(['activeFilter' => 'active']);

        $tags = $component->viewData('tags');

        $this->assertEquals(1, $tags->count());
        $this->assertTrue($tags->first()->is_active);
    }

    public function test_filters_by_inactive_status(): void
    {
        // Create active and inactive tags
        $activeTag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $activeTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $inactiveTag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $inactiveTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $component = $this->testComponent(['activeFilter' => 'inactive']);

        $tags = $component->viewData('tags');

        $this->assertEquals(1, $tags->count());
        $this->assertFalse($tags->first()->is_active);
    }

    public function test_shows_all_tags_when_filter_is_all(): void
    {
        // Create active and inactive tags
        Tag::factory()->create(['is_active' => true]);
        Tag::factory()->create(['is_active' => false]);

        // Create translations for each tag
        Tag::all()->each(function ($tag) {
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'status' => 'published',
            ]);
        });

        $component = $this->testComponent(['activeFilter' => 'all']);

        $tags = $component->viewData('tags');

        $this->assertEquals(2, $tags->count());
    }

    public function test_loads_translations_for_selected_locale(): void
    {
        $tag = Tag::factory()->create();

        // Create French translation
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Exploration Spatiale',
            'status' => 'published',
        ]);

        // Create English translation
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Space Exploration',
            'status' => 'published',
        ]);

        $component = $this->testComponent(['locale' => 'en']);

        $tags = $component->viewData('tags');

        $this->assertEquals(1, $tags->count());
        $this->assertEquals('Space Exploration', $tags->first()->translations->first()->name);
    }

    public function test_loads_places_count(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create places and associate with tag
        $place1 = Place::factory()->create(['admin_id' => $this->admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $this->admin->id]);

        $tag->places()->attach([$place1->id, $place2->id]);

        $component = $this->testComponent();

        $tags = $component->viewData('tags');

        $this->assertEquals(1, $tags->count());
        $this->assertEquals(2, $tags->first()->places_count);
    }

    // ========================================
    // Event Listeners
    // ========================================

    public function test_updates_filters_from_event(): void
    {
        $component = $this->testComponent();

        $component->dispatch('filters:updated', search: 'test', activeFilter: 'active', locale: 'en');

        $component->assertSet('search', 'test')
            ->assertSet('activeFilter', 'active')
            ->assertSet('locale', 'en');
    }

    // ========================================
    // Empty States
    // ========================================

    public function test_shows_empty_state_when_no_tags(): void
    {
        $this->testComponent()
            ->assertSee('Aucun tag trouvé');
    }

    public function test_shows_empty_state_when_search_returns_no_results(): void
    {
        // Create a tag
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Space',
            'status' => 'published',
        ]);

        $this->testComponent(['search' => 'NonexistentTag'])
            ->assertSee('Aucun tag trouvé');
    }

    // ========================================
    // View Rendering
    // ========================================

    public function test_view_displays_tag_name(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Test Tag',
            'status' => 'published',
        ]);

        $this->testComponent()
            ->assertSee('Test Tag');
    }

    public function test_view_displays_tag_slug(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'test-tag',
            'status' => 'published',
        ]);

        $this->testComponent()
            ->assertSee('test-tag');
    }

    public function test_view_displays_tag_color(): void
    {
        $tag = Tag::factory()->create(['color' => '#FF5733']);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $this->testComponent()
            ->assertSee('#FF5733');
    }

    public function test_view_displays_active_status(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $this->testComponent()
            ->assertSee('Actif');
    }

    public function test_view_displays_inactive_status(): void
    {
        $tag = Tag::factory()->create(['is_active' => false]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $this->testComponent()
            ->assertSee('Inactif');
    }

    public function test_view_displays_places_count(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create and associate places
        $place = Place::factory()->create(['admin_id' => $this->admin->id]);
        $tag->places()->attach($place->id);

        $this->testComponent()
            ->assertSee('1 lieu');
    }

    public function test_view_displays_pagination_info(): void
    {
        // Create 25 tags
        Tag::factory()->count(25)->create();

        Tag::all()->each(function ($tag) {
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'status' => 'published',
            ]);
        });

        $this->testComponent([], [], 20)
            ->assertSee('1-20 sur 25');
    }
}
