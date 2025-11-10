<?php

namespace App\Livewire\Admin\Tag\TagList;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class TagListPage extends Component
{
    // URL-synchronized properties
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'active')]
    public string $activeFilter = 'all'; // all|active|inactive

    #[Url(as: 'l')]
    public string $locale = 'fr';

    #[Url(as: 's')]
    public string $sortBy = 'created_at';

    #[Url(as: 'd')]
    public string $sortDirection = 'desc';

    #[Url(as: 'pp')]
    public int $perPage = 20;

    /**
     * Listen to filter changes from TagListFilters component
     */
    #[On('filters:updated')]
    public function updateFilters(string $search, string $activeFilter, string $locale): void
    {
        $this->search = $search;
        $this->activeFilter = $activeFilter;
        $this->locale = $locale;

        // Skip render to avoid double rendering
        $this->skipRender();
    }

    /**
     * Listen to sorting changes from TagListTable component
     */
    #[On('sorting:updated')]
    public function updateSorting(string $sortBy, string $sortDirection): void
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;

        $this->skipRender();
    }

    /**
     * Listen to pagination changes from TagListTable component
     */
    #[On('pagination:updated')]
    public function updatePagination(int $perPage): void
    {
        $this->perPage = $perPage;

        $this->skipRender();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.tag.tag-list.tag-list-page', [
            'initialFilters' => [
                'search' => $this->search,
                'activeFilter' => $this->activeFilter,
                'locale' => $this->locale,
            ],
            'initialSorting' => [
                'sortBy' => $this->sortBy,
                'sortDirection' => $this->sortDirection,
            ],
            'initialPerPage' => $this->perPage,
        ]);
    }
}
