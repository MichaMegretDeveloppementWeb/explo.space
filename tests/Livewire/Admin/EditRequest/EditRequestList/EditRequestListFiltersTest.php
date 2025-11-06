<?php

namespace Tests\Livewire\Admin\EditRequest\EditRequestList;

use App\Livewire\Admin\EditRequest\EditRequestList\EditRequestListFilters;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditRequestListFiltersTest extends TestCase
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
        Livewire::test(EditRequestListFilters::class)
            ->assertStatus(200);
    }

    public function test_mount_initializes_with_default_values(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->assertSet('search', '')
            ->assertSet('type', '')
            ->assertSet('status', '');
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->assertViewIs('livewire.admin.edit-request.edit-request-list.edit-request-list-filters');
    }

    // ========================================
    // Mount with Initial Filters
    // ========================================

    public function test_mount_accepts_initial_filters_with_search(): void
    {
        Livewire::test(EditRequestListFilters::class, [
            'initialFilters' => ['search' => 'test', 'type' => '', 'status' => ''],
        ])
            ->assertSet('search', 'test');
    }

    public function test_mount_accepts_initial_filters_with_type(): void
    {
        Livewire::test(EditRequestListFilters::class, [
            'initialFilters' => ['search' => '', 'type' => 'modification', 'status' => ''],
        ])
            ->assertSet('type', 'modification');
    }

    public function test_mount_accepts_initial_filters_with_status(): void
    {
        Livewire::test(EditRequestListFilters::class, [
            'initialFilters' => ['search' => '', 'type' => '', 'status' => 'pending'],
        ])
            ->assertSet('status', 'pending');
    }

    public function test_mount_accepts_all_initial_filters(): void
    {
        Livewire::test(EditRequestListFilters::class, [
            'initialFilters' => [
                'search' => 'kennedy',
                'type' => 'modification',
                'status' => 'submitted',
            ],
        ])
            ->assertSet('search', 'kennedy')
            ->assertSet('type', 'modification')
            ->assertSet('status', 'submitted');
    }

    // ========================================
    // Search Input Updates
    // ========================================

    public function test_updated_search_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('search', 'test')
            ->assertDispatched('filters:updated', search: 'test', type: '', status: '');
    }

    public function test_updated_search_with_type_and_status(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('type', 'modification')
            ->set('status', 'pending')
            ->set('search', 'kennedy')
            ->assertDispatched('filters:updated', search: 'kennedy', type: 'modification', status: 'pending');
    }

    public function test_clearing_search_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('search', 'test')
            ->set('search', '')
            ->assertDispatched('filters:updated', search: '', type: '', status: '');
    }

    // ========================================
    // Type Select Updates
    // ========================================

    public function test_updated_type_modification_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('type', 'modification')
            ->assertDispatched('filters:updated', search: '', type: 'modification', status: '');
    }

    public function test_updated_type_signalement_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('type', 'signalement')
            ->assertDispatched('filters:updated', search: '', type: 'signalement', status: '');
    }

    public function test_updated_type_photo_suggestion_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('type', 'photo_suggestion')
            ->assertDispatched('filters:updated', search: '', type: 'photo_suggestion', status: '');
    }

    public function test_clearing_type_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('type', 'modification')
            ->set('type', '')
            ->assertDispatched('filters:updated', search: '', type: '', status: '');
    }

    // ========================================
    // Status Select Updates
    // ========================================

    public function test_updated_status_submitted_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('status', 'submitted')
            ->assertDispatched('filters:updated', search: '', type: '', status: 'submitted');
    }

    public function test_updated_status_pending_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('status', 'pending')
            ->assertDispatched('filters:updated', search: '', type: '', status: 'pending');
    }

    public function test_updated_status_accepted_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('status', 'accepted')
            ->assertDispatched('filters:updated', search: '', type: '', status: 'accepted');
    }

    public function test_updated_status_refused_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('status', 'refused')
            ->assertDispatched('filters:updated', search: '', type: '', status: 'refused');
    }

    public function test_clearing_status_triggers_apply_filters(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('status', 'pending')
            ->set('status', '')
            ->assertDispatched('filters:updated', search: '', type: '', status: '');
    }

    // ========================================
    // Apply Filters Method
    // ========================================

    public function test_apply_filters_dispatches_event_with_current_values(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('search', 'kennedy')
            ->set('type', 'modification')
            ->set('status', 'submitted')
            ->call('applyFilters')
            ->assertDispatched('filters:updated', search: 'kennedy', type: 'modification', status: 'submitted');
    }

    public function test_apply_filters_dispatches_event_with_empty_values(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->call('applyFilters')
            ->assertDispatched('filters:updated', search: '', type: '', status: '');
    }

    // ========================================
    // Reset Filters Method
    // ========================================

    public function test_reset_filters_clears_all_values(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('search', 'test')
            ->set('type', 'modification')
            ->set('status', 'pending')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('type', '')
            ->assertSet('status', '');
    }

    public function test_reset_filters_dispatches_event(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('search', 'test')
            ->set('type', 'modification')
            ->set('status', 'pending')
            ->call('resetFilters')
            ->assertDispatched('filters:updated', search: '', type: '', status: '');
    }

    public function test_reset_filters_when_already_empty(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('type', '')
            ->assertSet('status', '');
    }

    // ========================================
    // Multiple Filter Updates
    // ========================================

    public function test_updating_multiple_filters_in_sequence(): void
    {
        $component = Livewire::test(EditRequestListFilters::class)
            ->set('search', 'kennedy')
            ->assertDispatched('filters:updated', search: 'kennedy', type: '', status: '')
            ->set('type', 'modification')
            ->assertDispatched('filters:updated', search: 'kennedy', type: 'modification', status: '')
            ->set('status', 'submitted')
            ->assertDispatched('filters:updated', search: 'kennedy', type: 'modification', status: 'submitted');

        $this->assertEquals('kennedy', $component->get('search'));
        $this->assertEquals('modification', $component->get('type'));
        $this->assertEquals('submitted', $component->get('status'));
    }

    // ========================================
    // View Rendering
    // ========================================

    public function test_view_renders_search_input(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->assertSee('wire:model.live.debounce.500ms="search"', false);
    }

    public function test_view_renders_type_select(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->assertSee('wire:model.live="type"', false);
    }

    public function test_view_renders_status_select(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->assertSee('wire:model.live="status"', false);
    }

    public function test_view_renders_reset_button_when_filters_active(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->set('search', 'test')
            ->assertSee('Réinitialiser les filtres');
    }

    public function test_view_hides_reset_button_when_filters_empty(): void
    {
        Livewire::test(EditRequestListFilters::class)
            ->assertDontSee('Réinitialiser les filtres');
    }
}
