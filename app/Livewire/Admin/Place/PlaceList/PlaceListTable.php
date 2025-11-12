<?php

namespace App\Livewire\Admin\Place\PlaceList;

use App\Livewire\Admin\Place\PlaceList\Concerns\ManagesLoadData;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PlaceListTable extends Component
{
    use ManagesLoadData;
    use WithPagination;

    /**
     * Vue de pagination personnalisée
     */
    protected string $paginationTheme = 'tailwind-modern';

    /**
     * Filtres appliqués
     */
    public string $search = '';

    /** @var array<int, string> */
    public array $tags = [];

    /** @var array<int, int> */
    public array $categories = [];

    public string $locale = 'fr';

    /**
     * Tri appliqué
     */
    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    /**
     * Pagination
     */
    public int $perPage = 20;

    /**
     * Initialiser depuis les props du parent
     *
     * @param  array{search: string, tags: array<int, string>, categories: array<int, int>, locale: string}  $initialFilters
     * @param  array{sortBy: string, sortDirection: string}  $initialSorting
     */
    public function mount(array $initialFilters, array $initialSorting, int $initialPerPage): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->tags = $initialFilters['tags'] ?? [];
        $this->categories = $initialFilters['categories'] ?? [];
        $this->locale = $initialFilters['locale'] ?? 'fr';

        $this->sortBy = $initialSorting['sortBy'] ?? 'created_at';
        $this->sortDirection = $initialSorting['sortDirection'] ?? 'desc';

        $this->perPage = $initialPerPage;
    }

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.place.place-list.place-list-table', [
            'places' => $this->loadPlaces(),
            'locale' => $this->locale,
        ]);
    }

    /**
     * Écouter les changements de filtres
     *
     * @param  array<int, string>  $tags
     * @param  array<int, int>  $categories
     */
    #[On('filters:updated')]
    public function updateFiltersFromEvent(string $search, array $tags, array $categories, string $locale): void
    {
        $this->search = $search;
        $this->tags = $tags;
        $this->categories = $categories;
        $this->locale = $locale;

        // Réinitialiser la pagination lors d'un changement de filtre
        $this->resetPage();
    }

    /**
     * Trier par colonne (toggle ASC/DESC)
     */
    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            // Toggle direction
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Nouvelle colonne, direction par défaut DESC
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();

        // Dispatch vers parent pour sync URL
        $this->dispatch('sorting:updated',
            sortBy: $this->sortBy,
            sortDirection: $this->sortDirection
        );
    }

    /**
     * Changer le nombre d'éléments par page
     */
    public function updatePerPage(int $value): void
    {
        $this->perPage = $value;
        $this->resetPage();

        // Dispatch vers parent pour sync URL
        $this->dispatch('pagination:updated', perPage: $this->perPage);
    }
}
