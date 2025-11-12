<?php

namespace App\Livewire\Admin\Category\CategoryList;

use App\Livewire\Admin\Category\CategoryList\Concerns\ManagesLoadData;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryListTable extends Component
{
    use ManagesLoadData;
    use WithPagination;

    protected string $paginationTheme = 'tailwind-modern';

    // State from parent component
    public string $search = '';

    public string $activeFilter = 'all';

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public int $perPage = 20;

    /**
     * Initialize state from parent component
     *
     * @param  array{search: string, activeFilter: string}  $initialFilters
     * @param  array{sortBy: string, sortDirection: string}  $initialSorting
     */
    public function mount(array $initialFilters, array $initialSorting, int $initialPerPage): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->activeFilter = $initialFilters['activeFilter'] ?? 'all';
        $this->sortBy = $initialSorting['sortBy'] ?? 'name';
        $this->sortDirection = $initialSorting['sortDirection'] ?? 'asc';
        $this->perPage = $initialPerPage;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.category.category-list.category-list-table', [
            'categories' => $this->loadCategories(),
        ]);
    }

    /**
     * Listen to filter updates from parent
     */
    #[On('filters:updated')]
    public function updateFiltersFromEvent(string $search, string $activeFilter): void
    {
        $this->search = $search;
        $this->activeFilter = $activeFilter;

        // Reset to page 1 when filters change
        $this->resetPage();
    }

    /**
     * Handle column sorting
     */
    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            // Toggle direction if same column
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // New column, default to desc
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();

        // Notify parent for URL sync
        $this->dispatch('sorting:updated',
            sortBy: $this->sortBy,
            sortDirection: $this->sortDirection
        );
    }

    /**
     * Update items per page
     */
    public function updatePerPage(int $value): void
    {
        $this->perPage = $value;
        $this->resetPage();

        // Notify parent for URL sync
        $this->dispatch('pagination:updated', perPage: $this->perPage);
    }
}
