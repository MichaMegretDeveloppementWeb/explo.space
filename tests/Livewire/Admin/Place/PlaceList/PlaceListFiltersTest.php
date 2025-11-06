<?php

namespace Tests\Livewire\Admin\Place\PlaceList;

use App\Livewire\Admin\Place\PlaceList\PlaceListFilters;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceListFiltersTest extends TestCase
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
    // Component Rendering & Initialization
    // ========================================

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->assertSet('search', '')
            ->assertSet('tags', [])
            ->assertSet('locale', 'fr')
            ->assertSet('tagSearchInput', '')
            ->assertSet('availableTags', [])
            ->assertSet('tagSuggestions', []);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => 'NASA',
                'tags' => ['nasa', 'spacex'],
                'locale' => 'en',
            ],
        ])
            ->assertSet('search', 'NASA')
            ->assertSet('tags', ['nasa', 'spacex'])
            ->assertSet('locale', 'en');
    }

    public function test_mount_loads_available_tags(): void
    {
        // Create tags
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->assertSet('availableTags', [
                ['slug' => 'nasa', 'name' => 'NASA'],
            ])
            ->assertSet('tagSuggestions', [
                ['slug' => 'nasa', 'name' => 'NASA'],
            ]);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->assertViewIs('livewire.admin.place.place-list.place-list-filters');
    }

    // ========================================
    // Search Functionality
    // ========================================

    public function test_updated_search_dispatches_filters_updated_event(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->set('search', 'NASA')
            ->assertDispatched('filters:updated', search: 'NASA', tags: [], locale: 'fr');
    }

    // ========================================
    // Tag Selection & Filtering
    // ========================================

    public function test_add_tag_adds_tag_to_selection(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->call('addTag', 'nasa')
            ->assertSet('tags', ['nasa'])
            ->assertDispatched('filters:updated', search: '', tags: ['nasa'], locale: 'fr');
    }

    public function test_add_tag_does_not_add_duplicate(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => ['nasa'],
                'locale' => 'fr',
            ],
        ])
            ->call('addTag', 'nasa')
            ->assertSet('tags', ['nasa']);
    }

    public function test_add_tag_clears_search_input(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->set('tagSearchInput', 'nasa')
            ->call('addTag', 'nasa')
            ->assertSet('tagSearchInput', '');
    }

    public function test_add_tag_resets_suggestions_to_all_tags(): void
    {
        // Create tags
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->call('addTag', 'nasa')
            ->assertCount('tagSuggestions', 2);
    }

    public function test_remove_tag_removes_tag_from_selection(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => ['nasa', 'spacex'],
                'locale' => 'fr',
            ],
        ])
            ->call('removeTag', 'nasa')
            ->assertSet('tags', ['spacex'])
            ->assertDispatched('filters:updated', search: '', tags: ['spacex'], locale: 'fr');
    }

    public function test_updated_tag_search_input_filters_suggestions(): void
    {
        // Create tags
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'Observatoire',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->set('tagSearchInput', 'nasa')
            ->assertCount('tagSuggestions', 1)
            ->assertSet('tagSuggestions', [
                ['slug' => 'nasa', 'name' => 'NASA'],
            ]);
    }

    public function test_tag_search_is_case_insensitive(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->set('tagSearchInput', 'nasa')
            ->assertCount('tagSuggestions', 1);
    }

    public function test_empty_tag_search_shows_all_tags(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->set('tagSearchInput', 'nasa')
            ->assertCount('tagSuggestions', 1)
            ->set('tagSearchInput', '')
            ->assertCount('tagSuggestions', 2);
    }

    // ========================================
    // Locale Change & Tag Translation
    // ========================================

    public function test_set_locale_changes_locale(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->call('setLocale', 'en')
            ->assertSet('locale', 'en')
            ->assertDispatched('filters:updated', search: '', tags: [], locale: 'en');
    }

    public function test_set_locale_reloads_available_tags_for_new_locale(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Observatoire',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Observatory',
            'slug' => 'observatory',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->assertSet('availableTags', [
                ['slug' => 'observatoire', 'name' => 'Observatoire'],
            ])
            ->call('setLocale', 'en')
            ->assertSet('availableTags', [
                ['slug' => 'observatory', 'name' => 'Observatory'],
            ]);
    }

    public function test_set_locale_translates_selected_tags(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Observatoire',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Observatory',
            'slug' => 'observatory',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => ['observatoire'],
                'locale' => 'fr',
            ],
        ])
            ->call('setLocale', 'en')
            ->assertSet('tags', ['observatory']);
    }

    public function test_set_locale_removes_tags_without_translation(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'slug' => 'observatoire',
            'status' => 'published',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'en',
            'slug' => 'observatory',
            'status' => 'published',
        ]);

        // Tag without EN translation
        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'slug' => 'sans-traduction',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => ['observatoire', 'sans-traduction'],
                'locale' => 'fr',
            ],
        ])
            ->call('setLocale', 'en')
            ->assertSet('tags', ['observatory']);
    }

    public function test_set_locale_with_invalid_locale_defaults_to_fr(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => '',
                'tags' => [],
                'locale' => 'fr',
            ],
        ])
            ->call('setLocale', 'invalid')
            ->assertSet('locale', 'fr');
    }

    // ========================================
    // Reset Filters
    // ========================================

    public function test_reset_filters_clears_all_filters(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => 'NASA',
                'tags' => ['nasa'],
                'locale' => 'en',
            ],
        ])
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('tags', [])
            ->assertSet('locale', 'fr')
            ->assertSet('tagSearchInput', '')
            ->assertDispatched('filters:updated', search: '', tags: [], locale: 'fr');
    }

    public function test_reset_filters_resets_suggestions_to_all_available_tags(): void
    {
        $tag1 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $tag2 = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'status' => 'published',
        ]);

        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => 'test',
                'tags' => ['nasa'],
                'locale' => 'fr',
            ],
        ])
            ->set('tagSearchInput', 'nasa')
            ->call('resetFilters')
            ->assertCount('tagSuggestions', 2);
    }

    // ========================================
    // Apply Filters Event
    // ========================================

    public function test_apply_filters_dispatches_event_with_current_state(): void
    {
        Livewire::test(PlaceListFilters::class, [
            'initialFilters' => [
                'search' => 'NASA',
                'tags' => ['nasa', 'spacex'],
                'locale' => 'fr',
            ],
        ])
            ->call('applyFilters')
            ->assertDispatched('filters:updated', search: 'NASA', tags: ['nasa', 'spacex'], locale: 'fr');
    }
}
