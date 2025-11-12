<?php

namespace App\Livewire\Admin\Category\CategoryList;

use Livewire\Component;

class CategoryListFilters extends Component
{
    // Local state for filters
    public string $search = '';

    public string $activeFilter = 'all'; // all|active|inactive

    /**
     * Initialize filters from parent component
     *
     * @param  array{search: string, activeFilter: string}  $initialFilters
     */
    public function mount(array $initialFilters): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->activeFilter = $initialFilters['activeFilter'] ?? 'all';
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
     * Dispatch filters to parent component for URL synchronization
     */
    public function applyFilters(): void
    {
        $this->dispatch('filters:updated',
            search: $this->search,
            activeFilter: $this->activeFilter
        );
    }

    /**
     * Reset all filters to default values
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->activeFilter = 'all';

        $this->applyFilters();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.category.category-list.category-list-filters');
    }
}
