<?php

namespace Tests\Livewire\Admin\PlaceRequest\PlaceRequestList;

use App\Livewire\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListFilters;
use App\Models\PlaceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceRequestListFiltersTest extends TestCase
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
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertSet('status', [])
            ->assertSet('statusCounts', [
                'all' => 0,
                'submitted' => 0,
                'pending' => 0,
                'accepted' => 0,
                'refused' => 0,
            ]);
    }

    public function test_mount_accepts_initial_filters(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending', 'submitted'],
            ],
        ])
            ->assertSet('status', ['pending', 'submitted']);
    }

    public function test_mount_loads_status_counts(): void
    {
        PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'accepted']);

        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertSet('statusCounts', [
                'all' => 4,
                'submitted' => 1,
                'pending' => 2,
                'accepted' => 1,
                'refused' => 0,
            ]);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertViewIs('livewire.admin.place-request.place-request-list.place-request-list-filters');
    }

    // ========================================
    // Status Selection (wire:model.live)
    // ========================================

    public function test_updated_status_dispatches_filters_updated_event(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->set('status', ['pending'])
            ->assertDispatched('filters:updated', status: ['pending']);
    }

    public function test_selecting_multiple_statuses_dispatches_event(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->set('status', ['pending', 'submitted'])
            ->assertDispatched('filters:updated', status: ['pending', 'submitted']);
    }

    public function test_deselecting_status_dispatches_event(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending', 'submitted'],
            ],
        ])
            ->set('status', ['pending'])
            ->assertDispatched('filters:updated', status: ['pending']);
    }

    public function test_selecting_all_statuses_dispatches_event(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->set('status', ['submitted', 'pending', 'accepted', 'refused'])
            ->assertDispatched('filters:updated', status: ['submitted', 'pending', 'accepted', 'refused']);
    }

    // ========================================
    // Reset Filters
    // ========================================

    public function test_reset_filters_clears_all_filters(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending', 'submitted'],
            ],
        ])
            ->call('resetFilters')
            ->assertSet('status', [])
            ->assertDispatched('filters:updated', status: []);
    }

    public function test_reset_filters_works_when_no_filters_selected(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->call('resetFilters')
            ->assertSet('status', []);
    }

    public function test_reset_filters_automatically_triggers_updated_status_hook(): void
    {
        $component = Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending'],
            ],
        ])
            ->call('resetFilters');

        // Verify updatedStatus() was called automatically by Livewire
        $component->assertDispatched('filters:updated', status: []);
    }

    // ========================================
    // Status Counts Display
    // ========================================

    public function test_status_counts_update_when_place_requests_exist(): void
    {
        PlaceRequest::factory()->count(5)->create(['status' => 'submitted']);
        PlaceRequest::factory()->count(3)->create(['status' => 'pending']);
        PlaceRequest::factory()->count(2)->create(['status' => 'accepted']);
        PlaceRequest::factory()->count(1)->create(['status' => 'refused']);

        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertSet('statusCounts', [
                'all' => 11,
                'submitted' => 5,
                'pending' => 3,
                'accepted' => 2,
                'refused' => 1,
            ]);
    }

    public function test_status_counts_show_zero_for_missing_statuses(): void
    {
        PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequest::factory()->create(['status' => 'pending']);

        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertSet('statusCounts.accepted', 0)
            ->assertSet('statusCounts.refused', 0);
    }

    public function test_all_count_is_sum_of_all_statuses(): void
    {
        PlaceRequest::factory()->create(['status' => 'submitted']);
        PlaceRequest::factory()->create(['status' => 'pending']);
        PlaceRequest::factory()->create(['status' => 'accepted']);
        PlaceRequest::factory()->create(['status' => 'refused']);

        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertSet('statusCounts.all', 4);
    }

    // ========================================
    // Checkbox State Synchronization
    // ========================================

    public function test_checkboxes_sync_when_status_changes_via_wire_model(): void
    {
        $component = Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ]);

        // Select pending checkbox
        $component->set('status', ['pending'])
            ->assertSet('status', ['pending']);

        // Select both pending and submitted
        $component->set('status', ['pending', 'submitted'])
            ->assertSet('status', ['pending', 'submitted']);

        // Deselect pending
        $component->set('status', ['submitted'])
            ->assertSet('status', ['submitted']);
    }

    public function test_reset_button_unchecks_all_checkboxes(): void
    {
        $component = Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending', 'submitted', 'accepted'],
            ],
        ])
            ->assertSet('status', ['pending', 'submitted', 'accepted']);

        $component->call('resetFilters')
            ->assertSet('status', []);
    }

    public function test_checkboxes_reflect_initial_filters_from_url(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending', 'submitted'],
            ],
        ])
            ->assertSet('status', ['pending', 'submitted']);
    }

    // ========================================
    // Edge Cases
    // ========================================

    public function test_handles_empty_initial_filters(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ])
            ->assertSet('status', []);
    }

    public function test_handles_single_status_filter(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending'],
            ],
        ])
            ->assertSet('status', ['pending']);
    }

    public function test_handles_all_statuses_selected(): void
    {
        Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['submitted', 'pending', 'accepted', 'refused'],
            ],
        ])
            ->assertSet('status', ['submitted', 'pending', 'accepted', 'refused']);
    }

    public function test_status_order_is_preserved(): void
    {
        $component = Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ]);

        $component->set('status', ['refused', 'accepted', 'pending', 'submitted'])
            ->assertSet('status', ['refused', 'accepted', 'pending', 'submitted']);
    }

    // ========================================
    // Livewire Hooks Behavior
    // ========================================

    public function test_updated_status_hook_is_called_automatically(): void
    {
        $component = Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => [],
            ],
        ]);

        // When we use set(), Livewire automatically calls updatedStatus()
        $component->set('status', ['pending'])
            ->assertDispatched('filters:updated', status: ['pending']);
    }

    public function test_updated_status_hook_is_called_when_reset(): void
    {
        $component = Livewire::test(PlaceRequestListFilters::class, [
            'initialFilters' => [
                'status' => ['pending'],
            ],
        ]);

        // resetFilters() sets status to [], which triggers updatedStatus()
        $component->call('resetFilters')
            ->assertDispatched('filters:updated', status: []);
    }
}
