<?php

namespace App\Livewire\Admin\Tag\TagList;

use Livewire\Component;

class TagListFilters extends Component
{
    // Local state for filters
    public string $search = '';

    public string $activeFilter = 'all'; // all|active|inactive

    public string $locale = 'fr';

    /**
     * Initialize filters from parent component
     *
     * @param  array{search: string, activeFilter: string, locale: string}  $initialFilters
     */
    public function mount(array $initialFilters): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->activeFilter = $initialFilters['activeFilter'] ?? 'all';
        $this->locale = $initialFilters['locale'] ?? 'fr';
    }

    /**
     * Called when search input changes (wire:model.live)
     */
    public function updatedSearch(): void
    {
        $this->applyFilters();
    }

    /**
     * Called when active filter changes
     */
    public function updatedActiveFilter(): void
    {
        $this->applyFilters();
    }

    /**
     * Called when locale changes
     */
    public function updatedLocale(): void
    {
        $this->applyFilters();
    }

    /**
     * Set locale manually (called from button clicks)
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->applyFilters();
    }

    /**
     * Dispatch filters to parent component for URL synchronization
     */
    public function applyFilters(): void
    {
        $this->dispatch('filters:updated',
            search: $this->search,
            activeFilter: $this->activeFilter,
            locale: $this->locale
        );
    }

    /**
     * Reset all filters to default values
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->activeFilter = 'all';
        $this->locale = 'fr';

        $this->applyFilters();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.tag.tag-list.tag-list-filters');
    }
}
