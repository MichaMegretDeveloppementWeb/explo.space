<?php

namespace App\Livewire\Admin\Category\CategoryList;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class CategoryListPage extends Component
{
    // URL-synchronized properties
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'active')]
    public string $activeFilter = 'all'; // all|active|inactive

    #[Url(as: 's')]
    public string $sortBy = 'name';

    #[Url(as: 'd')]
    public string $sortDirection = 'asc';

    #[Url(as: 'pp')]
    public int $perPage = 20;

    /**
     * Listen to filter changes from CategoryListFilters component
     */
    #[On('filters:updated')]
    public function updateFilters(string $search, string $activeFilter): void
    {
        $this->search = $search;
        $this->activeFilter = $activeFilter;

        // Skip render to avoid double rendering
        $this->skipRender();
    }

    /**
     * Listen to sorting changes from CategoryListTable component
     */
    #[On('sorting:updated')]
    public function updateSorting(string $sortBy, string $sortDirection): void
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;

        $this->skipRender();
    }

    /**
     * Listen to pagination changes from CategoryListTable component
     */
    #[On('pagination:updated')]
    public function updatePagination(int $perPage): void
    {
        $this->perPage = $perPage;

        $this->skipRender();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.category.category-list.category-list-page', [
            'initialFilters' => [
                'search' => $this->search,
                'activeFilter' => $this->activeFilter,
            ],
            'initialSorting' => [
                'sortBy' => $this->sortBy,
                'sortDirection' => $this->sortDirection,
            ],
            'initialPerPage' => $this->perPage,
        ]);
    }
}
