<?php

namespace Tests\Livewire\Admin\Tag\TagList;

use App\Livewire\Admin\Tag\TagList\TagListFilters;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TagListFiltersTest extends TestCase
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
    private function test_component(array $initialFilters = [])
    {
        return Livewire::test(TagListFilters::class, [
            'initialFilters' => array_merge([
                'search' => '',
                'activeFilter' => 'all',
                'locale' => 'fr',
            ], $initialFilters),
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
            ->assertSet('locale', 'fr');
    }

    public function test_component_view_exists(): void
    {
        $this->testComponent()
            ->assertViewIs('livewire.admin.tag.tag-list.tag-list-filters');
    }

    // ========================================
    // Mount with Initial Filters
    // ========================================

    public function test_mount_accepts_initial_filters_with_search(): void
    {
        $this->testComponent(['search' => 'test'])
            ->assertSet('search', 'test');
    }

    public function test_mount_accepts_initial_filters_with_active_filter(): void
    {
        $this->testComponent(['activeFilter' => 'active'])
            ->assertSet('activeFilter', 'active');
    }

    public function test_mount_accepts_initial_filters_with_locale(): void
    {
        $this->testComponent(['locale' => 'en'])
            ->assertSet('locale', 'en');
    }

    public function test_mount_accepts_all_initial_filters(): void
    {
        $this->testComponent([
            'search' => 'space',
            'activeFilter' => 'inactive',
            'locale' => 'en',
        ])
            ->assertSet('search', 'space')
            ->assertSet('activeFilter', 'inactive')
            ->assertSet('locale', 'en');
    }

    // ========================================
    // Search Input Updates
    // ========================================

    public function test_updated_search_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('search', 'test')
            ->assertDispatched('filters:updated', search: 'test', activeFilter: 'all', locale: 'fr');
    }

    public function test_updated_search_with_active_filter_and_locale(): void
    {
        $this->testComponent()
            ->set('activeFilter', 'active')
            ->set('locale', 'en')
            ->set('search', 'space')
            ->assertDispatched('filters:updated', search: 'space', activeFilter: 'active', locale: 'en');
    }

    public function test_clearing_search_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('search', 'test')
            ->set('search', '')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'fr');
    }

    // ========================================
    // Active Filter Select Updates
    // ========================================

    public function test_updated_active_filter_all_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('activeFilter', 'all')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'fr');
    }

    public function test_updated_active_filter_active_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('activeFilter', 'active')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'active', locale: 'fr');
    }

    public function test_updated_active_filter_inactive_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('activeFilter', 'inactive')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'inactive', locale: 'fr');
    }

    public function test_changing_active_filter_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('activeFilter', 'active')
            ->set('activeFilter', 'inactive')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'inactive', locale: 'fr');
    }

    // ========================================
    // Locale Updates
    // ========================================

    public function test_updated_locale_fr_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('locale', 'fr')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'fr');
    }

    public function test_updated_locale_en_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->set('locale', 'en')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'en');
    }

    public function test_set_locale_method_updates_locale_and_triggers_apply_filters(): void
    {
        $this->testComponent()
            ->call('setLocale', 'en')
            ->assertSet('locale', 'en')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'en');
    }

    public function test_set_locale_method_can_switch_back_to_fr(): void
    {
        $this->testComponent()
            ->set('locale', 'en')
            ->call('setLocale', 'fr')
            ->assertSet('locale', 'fr')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'fr');
    }

    // ========================================
    // Apply Filters Method
    // ========================================

    public function test_apply_filters_dispatches_event_with_current_values(): void
    {
        $this->testComponent()
            ->set('search', 'space')
            ->set('activeFilter', 'active')
            ->set('locale', 'en')
            ->call('applyFilters')
            ->assertDispatched('filters:updated', search: 'space', activeFilter: 'active', locale: 'en');
    }

    public function test_apply_filters_dispatches_event_with_empty_values(): void
    {
        $this->testComponent()
            ->call('applyFilters')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'fr');
    }

    // ========================================
    // Reset Filters Method
    // ========================================

    public function test_reset_filters_clears_all_values(): void
    {
        $this->testComponent()
            ->set('search', 'test')
            ->set('activeFilter', 'active')
            ->set('locale', 'en')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('activeFilter', 'all')
            ->assertSet('locale', 'fr');
    }

    public function test_reset_filters_dispatches_event(): void
    {
        $this->testComponent()
            ->set('search', 'test')
            ->set('activeFilter', 'active')
            ->set('locale', 'en')
            ->call('resetFilters')
            ->assertDispatched('filters:updated', search: '', activeFilter: 'all', locale: 'fr');
    }

    public function test_reset_filters_when_already_empty(): void
    {
        $this->testComponent()
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('activeFilter', 'all')
            ->assertSet('locale', 'fr');
    }

    // ========================================
    // Multiple Filter Updates
    // ========================================

    public function test_updating_multiple_filters_in_sequence(): void
    {
        $component = $this->testComponent()
            ->set('search', 'space')
            ->assertDispatched('filters:updated', search: 'space', activeFilter: 'all', locale: 'fr')
            ->set('activeFilter', 'active')
            ->assertDispatched('filters:updated', search: 'space', activeFilter: 'active', locale: 'fr')
            ->set('locale', 'en')
            ->assertDispatched('filters:updated', search: 'space', activeFilter: 'active', locale: 'en');

        $this->assertEquals('space', $component->get('search'));
        $this->assertEquals('active', $component->get('activeFilter'));
        $this->assertEquals('en', $component->get('locale'));
    }

    // ========================================
    // View Rendering
    // ========================================

    public function test_view_renders_search_input(): void
    {
        $this->testComponent()
            ->assertSee('wire:model.live.debounce.500ms="search"', false);
    }

    public function test_view_renders_active_filter_select(): void
    {
        $this->testComponent()
            ->assertSee('wire:model.live="activeFilter"', false);
    }

    public function test_view_renders_locale_buttons(): void
    {
        $this->testComponent()
            ->assertSee('wire:click="setLocale(\'fr\')"', false)
            ->assertSee('wire:click="setLocale(\'en\')"', false);
    }

    public function test_view_renders_reset_button_when_search_active(): void
    {
        $this->testComponent()
            ->set('search', 'test')
            ->assertSee('Réinitialiser les filtres');
    }

    public function test_view_renders_reset_button_when_active_filter_not_all(): void
    {
        $this->testComponent()
            ->set('activeFilter', 'active')
            ->assertSee('Réinitialiser les filtres');
    }

    public function test_view_renders_reset_button_when_locale_not_fr(): void
    {
        $this->testComponent()
            ->set('locale', 'en')
            ->assertSee('Réinitialiser les filtres');
    }

    public function test_view_hides_reset_button_when_filters_empty(): void
    {
        $this->testComponent()
            ->assertDontSee('Réinitialiser les filtres');
    }
}
