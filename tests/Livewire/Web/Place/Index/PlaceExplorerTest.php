<?php

namespace Tests\Livewire\Web\Place\Index;

use App\Livewire\Web\Place\Index\PlaceExplorer;
use App\Support\Config\PlaceSearchConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->assertSet('searchMode', PlaceSearchConfig::SEARCH_MODE_DEFAULT)
            ->assertSet('radius', PlaceSearchConfig::RADIUS_DEFAULT)
            ->assertSet('latitude', null)
            ->assertSet('longitude', null)
            ->assertSet('address', null)
            ->assertSet('selectedTagsSlugs', '');
    }

    public function test_mount_accepts_valid_filters(): void
    {
        Livewire::test(PlaceExplorer::class, [
            'filters' => [
                'mode' => 'proximity',
                'lat' => 48.8566,
                'lng' => 2.3522,
                'radius' => 500000,
                'address' => 'Paris, France',
                'tags' => 'nasa,spacex',
            ],
        ])
            ->assertSet('searchMode', 'proximity')
            ->assertSet('latitude', 48.8566)
            ->assertSet('longitude', 2.3522)
            ->assertSet('radius', 500000)
            ->assertSet('address', 'Paris, France')
            ->assertSet('selectedTagsSlugs', 'nasa,spacex');
    }

    public function test_mount_corrects_invalid_radius(): void
    {
        Livewire::test(PlaceExplorer::class, [
            'filters' => [
                'mode' => 'proximity',
                'radius' => 50000, // Below minimum
            ],
        ])
            ->assertSet('radius', PlaceSearchConfig::RADIUS_MIN); // Should be corrected
    }

    public function test_mount_corrects_oversized_radius(): void
    {
        Livewire::test(PlaceExplorer::class, [
            'filters' => [
                'mode' => 'proximity',
                'radius' => 2000000, // Above maximum
            ],
        ])
            ->assertSet('radius', PlaceSearchConfig::RADIUS_MAX); // Should be corrected
    }

    public function test_mount_sets_initial_filters_for_children(): void
    {
        $component = Livewire::test(PlaceExplorer::class, [
            'filters' => [
                'mode' => 'proximity',
                'lat' => 48.8566,
                'lng' => 2.3522,
            ],
        ]);

        $initialFilters = $component->get('initialFilters');

        $this->assertIsArray($initialFilters);
        $this->assertArrayHasKey('mode', $initialFilters);
        $this->assertEquals('proximity', $initialFilters['mode']);
    }

    public function test_sync_url_params_updates_properties(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->dispatch('filters-updated', [
                'mode' => 'worldwide',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'radius' => 800000,
                'address' => 'New York, USA',
                'tags' => ['nasa'],
            ])
            ->assertSet('searchMode', 'worldwide')
            ->assertSet('latitude', 40.7128)
            ->assertSet('longitude', -74.0060)
            ->assertSet('radius', 800000)
            ->assertSet('address', 'New York, USA')
            ->assertSet('selectedTagsSlugs', 'nasa');
    }

    public function test_sync_url_params_corrects_invalid_data(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->dispatch('filters-updated', [
                'mode' => 'proximity',
                'latitude' => null,
                'longitude' => null,
                'radius' => 50000, // Below minimum
                'address' => null,
                'tags' => [],
            ])
            ->assertSet('radius', PlaceSearchConfig::RADIUS_MIN); // Should be corrected
    }

    public function test_sync_url_params_handles_empty_tags(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->dispatch('filters-updated', [
                'mode' => 'worldwide',
                'latitude' => null,
                'longitude' => null,
                'radius' => PlaceSearchConfig::RADIUS_DEFAULT,
                'address' => null,
                'tags' => [],
            ])
            ->assertSet('selectedTagsSlugs', '');
    }

    public function test_sync_url_params_converts_tags_array_to_string(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->dispatch('filters-updated', [
                'mode' => 'worldwide',
                'latitude' => null,
                'longitude' => null,
                'radius' => PlaceSearchConfig::RADIUS_DEFAULT,
                'address' => null,
                'tags' => ['nasa', 'spacex', 'observatory'],
            ])
            ->assertSet('selectedTagsSlugs', 'nasa,spacex,observatory');
    }

    public function test_url_params_are_synchronized_with_properties(): void
    {
        $component = Livewire::test(PlaceExplorer::class, [
            'filters' => [
                'mode' => 'proximity',
                'lat' => 48.8566,
                'lng' => 2.3522,
                'radius' => 500000,
            ],
        ]);

        // Verify URL properties are set
        $this->assertEquals('proximity', $component->get('searchMode'));
        $this->assertEquals(48.8566, $component->get('latitude'));
        $this->assertEquals(2.3522, $component->get('longitude'));
        $this->assertEquals(500000, $component->get('radius'));
    }

    public function test_search_mode_defaults_to_config_value(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->assertSet('searchMode', PlaceSearchConfig::SEARCH_MODE_DEFAULT);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceExplorer::class)
            ->assertViewIs('livewire.web.place.index.place-explorer');
    }
}
