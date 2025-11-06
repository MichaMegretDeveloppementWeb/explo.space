<?php

namespace App\Livewire\Admin\EditRequest\EditRequestList;

use App\Livewire\Admin\EditRequest\EditRequestList\Concerns\ManagesLoadData;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EditRequestListTable extends Component
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

    public string $type = '';

    /**
     * @var array<int, string>
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
     * @param  array{search: string, type: string, status: array<int, string>}  $initialFilters
     * @param  array{sortBy: string, sortDirection: string}  $initialSorting
     */
    public function mount(array $initialFilters, array $initialSorting, int $initialPerPage): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->type = $initialFilters['type'] ?? '';
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
        return view('livewire.admin.edit-request.edit-request-list.edit-request-list-table', [
            'editRequests' => $this->loadEditRequests(),
        ]);
    }

    /**
     * Écouter les changements de filtres
     *
     * @param  array<int, string>  $status
     */
    #[On('filters:updated')]
    public function updateFiltersFromEvent(string $search, string $type, array $status): void
    {
        $this->search = $search;
        $this->type = $type;
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
}
