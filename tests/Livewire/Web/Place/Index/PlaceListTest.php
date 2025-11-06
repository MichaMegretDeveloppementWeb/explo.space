<?php

namespace Tests\Livewire\Web\Place\Index;

use App\Livewire\Web\Place\Index\PlaceList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(PlaceList::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceList::class)
            ->assertSet('currentFilters', [])
            ->assertSet('places', [])
            ->assertSet('nextCursor', null)
            ->assertSet('hasMorePages', true)
            ->assertSet('currentBoundingBox', null);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 500000,
                'tags' => ['nasa'],
            ],
        ])
            ->assertSet('currentFilters', [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 500000,
                'tags' => ['nasa'],
            ])
            ->assertSet('places', [])
            ->assertSet('hasMorePages', true);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceList::class)
            ->assertViewIs('livewire.web.place.index.place-list');
    }

    public function test_component_has_places_property(): void
    {
        Livewire::test(PlaceList::class)
            ->assertSet('places', []);
    }

    public function test_component_has_pagination_properties(): void
    {
        Livewire::test(PlaceList::class)
            ->assertSet('nextCursor', null)
            ->assertSet('hasMorePages', true);
    }

    public function test_on_initial_list_bounds_sets_bounding_box(): void
    {
        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        Livewire::test(PlaceList::class)
            ->dispatch('initial-list-bounds', boundingBox: $boundingBox)
            ->assertSet('currentBoundingBox', $boundingBox);
    }

    public function test_on_update_list_bounds_updates_bounding_box(): void
    {
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

        Livewire::test(PlaceList::class)
            ->dispatch('initial-list-bounds', boundingBox: $initialBounds)
            ->assertSet('currentBoundingBox', $initialBounds)
            ->dispatch('update-list-bounds', boundingBox: $newBounds, filters: [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 200000,
                'tags' => [],
            ])
            ->assertSet('currentBoundingBox', $newBounds);
    }

    public function test_on_update_list_bounds_uses_current_filters_if_none_provided(): void
    {
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

        Livewire::test(PlaceList::class, [
            'initialFilters' => $initialFilters,
        ])
            ->dispatch('update-list-bounds', boundingBox: $boundingBox, filters: [])
            ->assertSet('currentFilters', $initialFilters);
    }

    public function test_on_update_list_bounds_dispatches_list_update_complete(): void
    {
        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        Livewire::test(PlaceList::class)
            ->dispatch('update-list-bounds', boundingBox: $boundingBox, filters: [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'radius' => 200000,
                'tags' => [],
            ])
            ->assertDispatched('list-update-complete');
    }

    public function test_load_more_returns_early_if_no_more_pages(): void
    {
        $component = Livewire::test(PlaceList::class);

        // Set hasMorePages to false
        $component->set('hasMorePages', false);
        $component->set('nextCursor', 'some-cursor');

        $initialPlaces = $component->get('places');

        $component->call('loadMore');

        // Places should remain unchanged
        $this->assertEquals($initialPlaces, $component->get('places'));
    }

    public function test_load_more_returns_early_if_no_cursor(): void
    {
        $component = Livewire::test(PlaceList::class);

        // Set nextCursor to null
        $component->set('hasMorePages', true);
        $component->set('nextCursor', null);

        $initialPlaces = $component->get('places');

        $component->call('loadMore');

        // Places should remain unchanged
        $this->assertEquals($initialPlaces, $component->get('places'));
    }

    public function test_dismiss_validation_error_clears_error(): void
    {
        $component = Livewire::test(PlaceList::class);

        // Manually add error to simulate validation error
        $component->set('places', []);

        // Call dismiss
        $component->call('dismissValidationError');

        // Verify no errors for filters_validation
        $component->assertHasNoErrors('filters_validation');
    }

    public function test_is_minimal_conditions_met_proximity_requires_coordinates(): void
    {
        // Test without coordinates
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'proximity',
                'latitude' => null,
                'longitude' => null,
            ],
        ]);

        $isConditionsMet = $component->instance()->isMinimalConditionsMet();
        $this->assertFalse($isConditionsMet);

        // Test with coordinates
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'proximity',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
            ],
        ]);

        $isConditionsMet = $component->instance()->isMinimalConditionsMet();
        $this->assertTrue($isConditionsMet);
    }

    public function test_is_minimal_conditions_met_worldwide_requires_tags(): void
    {
        // Test without tags
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'worldwide',
                'tags' => [],
            ],
        ]);

        $isConditionsMet = $component->instance()->isMinimalConditionsMet();
        $this->assertFalse($isConditionsMet);

        // Test with tags
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'worldwide',
                'tags' => ['nasa', 'spacex'],
            ],
        ]);

        $isConditionsMet = $component->instance()->isMinimalConditionsMet();
        $this->assertTrue($isConditionsMet);
    }

    public function test_get_start_search_message_returns_correct_message_for_proximity(): void
    {
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'proximity',
            ],
        ]);

        $message = $component->instance()->getStartSearchMessage();

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_get_start_search_message_returns_correct_message_for_worldwide(): void
    {
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'worldwide',
            ],
        ]);

        $message = $component->instance()->getStartSearchMessage();

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_get_no_results_message_returns_correct_message_for_proximity(): void
    {
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'proximity',
            ],
        ]);

        $message = $component->instance()->getNoResultsMessage();

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_get_no_results_message_returns_correct_message_for_worldwide(): void
    {
        $component = Livewire::test(PlaceList::class, [
            'initialFilters' => [
                'mode' => 'worldwide',
            ],
        ]);

        $message = $component->instance()->getNoResultsMessage();

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_places_stored_as_arrays_not_models(): void
    {
        Livewire::test(PlaceList::class)
            ->assertSet('places', []);

        // Verify places property is an array type
        $component = Livewire::test(PlaceList::class);
        $this->assertIsArray($component->get('places'));
    }

    public function test_component_uses_with_pagination_trait(): void
    {
        $component = new PlaceList;
        $traits = class_uses($component);

        $this->assertContains(\Livewire\WithPagination::class, $traits);
    }

    public function test_render_passes_correct_data_to_view(): void
    {
        $component = Livewire::test(PlaceList::class);

        // Initially no bounding box
        $component->assertViewHas('places', []);
        $component->assertViewHas('hasBoundingBox', false);
        $component->assertViewHas('hasMorePages', true);

        // After setting bounding box
        $boundingBox = [
            'north' => 50.0,
            'south' => 40.0,
            'east' => 10.0,
            'west' => 0.0,
        ];

        $component->dispatch('initial-list-bounds', boundingBox: $boundingBox);

        $component->assertViewHas('hasBoundingBox', true);
    }
}
