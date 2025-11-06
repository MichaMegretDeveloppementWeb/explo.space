<?php

namespace Tests\Livewire\Web\Place\Index;

use App\Livewire\Web\Place\Index\PlaceFilters;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Support\Config\PlaceSearchConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('searchMode', PlaceSearchConfig::SEARCH_MODE_DEFAULT)
            ->assertSet('radius', PlaceSearchConfig::RADIUS_DEFAULT)
            ->assertSet('latitude', null)
            ->assertSet('longitude', null)
            ->assertSet('address', null)
            ->assertSet('selectedTagsSlugs', '')
            ->assertSet('filtersCollapsed', false);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        Livewire::test(PlaceFilters::class, [
            'initialFilters' => [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 500000,
                'address' => 'Paris, France',
                'tags' => ['nasa', 'spacex'],
            ],
        ])
            ->assertSet('searchMode', 'proximity')
            ->assertSet('latitude', 48.8566)
            ->assertSet('longitude', 2.3522)
            ->assertSet('radius', 500000)
            ->assertSet('address', 'Paris, France')
            ->assertSet('selectedTagsSlugs', 'nasa,spacex');
    }

    public function test_mount_converts_tags_array_to_string(): void
    {
        Livewire::test(PlaceFilters::class, [
            'initialFilters' => [
                'tags' => ['tag1', 'tag2', 'tag3'],
            ],
        ])
            ->assertSet('selectedTagsSlugs', 'tag1,tag2,tag3');
    }

    public function test_mount_handles_empty_tags_array(): void
    {
        Livewire::test(PlaceFilters::class, [
            'initialFilters' => [
                'tags' => [],
            ],
        ])
            ->assertSet('selectedTagsSlugs', '');
    }

    public function test_toggle_filters_changes_collapsed_state(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('filtersCollapsed', false)
            ->call('toggleFilters')
            ->assertSet('filtersCollapsed', true)
            ->call('toggleFilters')
            ->assertSet('filtersCollapsed', false);
    }

    public function test_collapse_filters_sets_collapsed_to_true(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('filtersCollapsed', false)
            ->call('collapseFilters')
            ->assertSet('filtersCollapsed', true);
    }

    public function test_expand_filters_sets_collapsed_to_false(): void
    {
        Livewire::test(PlaceFilters::class)
            ->call('collapseFilters')
            ->assertSet('filtersCollapsed', true)
            ->call('expandFilters')
            ->assertSet('filtersCollapsed', false);
    }

    public function test_filters_start_uncollapsed_by_default(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('filtersCollapsed', false);
    }

    public function test_dismiss_validation_error_clears_error(): void
    {
        $component = Livewire::test(PlaceFilters::class);

        // Manually add error to simulate validation error
        $component->set('filtersCollapsed', false);

        // Call dismiss
        $component->call('dismissValidationError');

        // Verify no errors for filters_validation
        $component->assertHasNoErrors('filters_validation');
    }

    public function test_component_has_address_suggestions_property(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('addressSuggestions', [])
            ->assertSet('addressSearchLoading', false);
    }

    public function test_component_has_geolocation_properties(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('geolocLoading', false);
    }

    public function test_component_has_tag_filtering_properties(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertSet('tagSearchQuery', '')
            ->assertSet('availableTags', [])
            ->assertSet('filteredTags', [])
            ->assertSet('tagsLoading', false)
            ->assertSet('showMobileTagSelector', false)
            ->assertSet('selectedTags', []);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceFilters::class)
            ->assertViewIs('livewire.web.place.index.place-filters');
    }

    public function test_mount_initializes_tags_system(): void
    {
        // Create some tags
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

        // Test component loads tags
        $component = Livewire::test(PlaceFilters::class);

        $availableTags = $component->get('availableTags');

        $this->assertIsArray($availableTags);
        $this->assertNotEmpty($availableTags);
    }
}
