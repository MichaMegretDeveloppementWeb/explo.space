<?php

namespace App\Livewire\Admin\PlaceRequest\PlaceRequestList;

use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PlaceRequestListTable extends Component
{
    use WithPagination;

    /**
     * Vue de pagination personnalisée
     */
    protected string $paginationTheme = 'tailwind-modern';

    /**
     * Filtre appliqué (multi-sélection)
     *
     * @var array<string>
     */
    public array $status = [];

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
     * @param  array{status: array<string>}  $initialFilters
     * @param  array{sortBy: string, sortDirection: string}  $initialSorting
     */
    public function mount(array $initialFilters, array $initialSorting, int $initialPerPage): void
    {
        $this->status = $initialFilters['status'] ?? [];

        $this->sortBy = $initialSorting['sortBy'] ?? 'created_at';
        $this->sortDirection = $initialSorting['sortDirection'] ?? 'desc';

        $this->perPage = $initialPerPage;
    }

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.place-request.place-request-list.place-request-list-table', [
            'placeRequests' => $this->loadPlaceRequests(),
        ]);
    }

    /**
     * Écouter les changements de filtres
     *
     * @param  array<string>  $status
     */
    #[On('filters:updated')]
    public function updateFiltersFromEvent(array $status): void
    {
        $this->status = $status;

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

    /**
     * Charger les données depuis le service
     *
     * @return LengthAwarePaginator<int, \App\Models\PlaceRequest>
     */
    private function loadPlaceRequests(): LengthAwarePaginator
    {
        $service = app(PlaceRequestListService::class);

        return $service->getPaginatedPlaceRequests(
            [
                'status' => $this->status,
            ],
            [
                'sortBy' => $this->sortBy,
                'sortDirection' => $this->sortDirection,
            ],
            ['perPage' => $this->perPage]
        );
    }
}
