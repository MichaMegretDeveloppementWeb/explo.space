<?php

namespace Tests\Livewire\Web\Place\Index;

use App\Livewire\Web\Place\Index\PlaceMap;
use App\Support\Config\PlaceSearchConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceMapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_component_can_be_rendered(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values_mode_b(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->assertSet('currentFilters', [])
            ->assertSet('useBoundingBox', false)
            ->assertSet('boundingBox', null)
            ->assertSet('visibleCount', 0);
    }

    public function test_mount_initializes_with_default_values_mode_a(): void
    {
        config(['map.use_bounding_box' => true]);

        Livewire::test(PlaceMap::class)
            ->assertSet('currentFilters', [])
            ->assertSet('useBoundingBox', true)
            ->assertSet('boundingBox', null)
            ->assertSet('coordinates', [])
            ->assertSet('visibleCount', 0);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        config(['map.use_bounding_box' => false]);

        $initialFilters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 500000,
            'tags' => ['nasa'],
        ];

        Livewire::test(PlaceMap::class, [
            'initialFilters' => $initialFilters,
        ])
            ->assertSet('currentFilters', $initialFilters)
            ->assertSet('previousFilters', [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 500000,
                'tags' => ['nasa'],
            ]);
    }

    public function test_component_view_exists(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->assertViewIs('livewire.web.place.index.place-map');
    }

    public function test_component_has_coordinates_property(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->assertSet('coordinates', []);
    }

    public function test_component_has_bounding_box_property(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->assertSet('boundingBox', null);
    }

    public function test_on_filters_updated_dispatches_sync_filters_view(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->dispatch('filters-updated', filters: [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 200000,
                'tags' => [],
            ])
            ->assertDispatched('sync-filters-view');
    }

    public function test_on_filters_updated_updates_previous_filters(): void
    {
        config(['map.use_bounding_box' => false]);

        $component = Livewire::test(PlaceMap::class);

        // Initial previous filters
        $this->assertEmpty($component->get('previousFilters'));

        // Dispatch event
        $component->dispatch('filters-updated', filters: [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => ['nasa'],
        ]);

        // Verify previous filters updated
        $previousFilters = $component->get('previousFilters');
        $this->assertEquals('proximity', $previousFilters['mode']);
        $this->assertEquals(48.8566, $previousFilters['latitude']);
        $this->assertEquals(2.3522, $previousFilters['longitude']);
        $this->assertEquals(200000, $previousFilters['radius']);
        $this->assertEquals(['nasa'], $previousFilters['tags']);
    }

    public function test_on_initial_map_bounds_sets_bounding_box_mode_a(): void
    {
        config(['map.use_bounding_box' => true]);

        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        Livewire::test(PlaceMap::class)
            ->dispatch('initial-map-bounds', boundingBox: $boundingBox)
            ->assertSet('boundingBox', $boundingBox)
            ->assertDispatched('coordinates-updated');
    }

    public function test_on_initial_map_bounds_ignored_if_mode_b(): void
    {
        config(['map.use_bounding_box' => false]);

        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        Livewire::test(PlaceMap::class)
            ->dispatch('initial-map-bounds', boundingBox: $boundingBox)
            ->assertSet('boundingBox', null); // Should remain null
    }

    public function test_on_update_map_bounds_updates_bounding_box_mode_a(): void
    {
        config(['map.use_bounding_box' => true]);

        $initialBounds = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        $newBounds = [
            'north' => 55.0,
            'south' => 45.0,
            'east' => 15.0,
            'west' => 5.0,
        ];

        Livewire::test(PlaceMap::class)
            ->dispatch('initial-map-bounds', boundingBox: $initialBounds)
            ->assertSet('boundingBox', $initialBounds)
            ->dispatch('update-map-bounds', boundingBox: $newBounds, filters: [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 200000,
                'tags' => [],
            ])
            ->assertSet('boundingBox', $newBounds)
            ->assertDispatched('coordinates-updated');
    }

    public function test_on_update_map_bounds_ignored_if_mode_b(): void
    {
        config(['map.use_bounding_box' => false]);

        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        Livewire::test(PlaceMap::class)
            ->dispatch('update-map-bounds', boundingBox: $boundingBox, filters: [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 200000,
                'tags' => [],
            ])
            ->assertSet('boundingBox', null); // Should remain null
    }

    public function test_on_update_map_bounds_uses_current_filters_if_none_provided(): void
    {
        config(['map.use_bounding_box' => true]);

        $initialFilters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 500000,
            'tags' => ['nasa'],
        ];

        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        Livewire::test(PlaceMap::class, [
            'initialFilters' => $initialFilters,
        ])
            ->dispatch('update-map-bounds', boundingBox: $boundingBox, filters: [])
            ->assertSet('currentFilters', $initialFilters);
    }

    public function test_coordinates_stored_as_arrays(): void
    {
        config(['map.use_bounding_box' => false]);

        $component = Livewire::test(PlaceMap::class);

        $this->assertIsArray($component->get('coordinates'));
    }

    public function test_render_passes_correct_data_to_view(): void
    {
        config(['map.use_bounding_box' => false]);

        $component = Livewire::test(PlaceMap::class);

        $component->assertViewHas('coordinates', []);
        $component->assertViewHas('visibleCount', 0);
    }

    public function test_mount_initializes_previous_filters_with_defaults(): void
    {
        config(['map.use_bounding_box' => false]);

        $component = Livewire::test(PlaceMap::class);

        $previousFilters = $component->get('previousFilters');

        $this->assertArrayHasKey('mode', $previousFilters);
        $this->assertArrayHasKey('latitude', $previousFilters);
        $this->assertArrayHasKey('longitude', $previousFilters);
        $this->assertArrayHasKey('radius', $previousFilters);
        $this->assertArrayHasKey('tags', $previousFilters);

        $this->assertEquals(PlaceSearchConfig::SEARCH_MODE_DEFAULT, $previousFilters['mode']);
        $this->assertNull($previousFilters['latitude']);
        $this->assertNull($previousFilters['longitude']);
        $this->assertEquals(PlaceSearchConfig::RADIUS_DEFAULT, $previousFilters['radius']);
        $this->assertEquals([], $previousFilters['tags']);
    }

    public function test_mode_a_waits_for_javascript_bounding_box(): void
    {
        config(['map.use_bounding_box' => true]);

        // Mode A should start with empty coordinates
        Livewire::test(PlaceMap::class)
            ->assertSet('coordinates', [])
            ->assertSet('boundingBox', null)
            ->assertSet('visibleCount', 0);
    }

    public function test_visible_count_property_exists(): void
    {
        config(['map.use_bounding_box' => false]);

        Livewire::test(PlaceMap::class)
            ->assertSet('visibleCount', 0);
    }

    public function test_component_handles_mode_switching(): void
    {
        config(['map.use_bounding_box' => false]);

        $component = Livewire::test(PlaceMap::class, [
            'initialFilters' => [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 200000,
                'tags' => [],
            ],
        ]);

        // Switch to worldwide mode
        $component->dispatch('filters-updated', filters: [
            'mode' => 'worldwide',
            'latitude' => null,
            'longitude' => null,
            'radius' => PlaceSearchConfig::RADIUS_DEFAULT,
            'tags' => ['nasa'],
        ]);

        // Verify filters updated
        $currentFilters = $component->get('currentFilters');
        $this->assertEquals('worldwide', $currentFilters['mode']);
        $this->assertEquals(['nasa'], $currentFilters['tags']);
    }
}
