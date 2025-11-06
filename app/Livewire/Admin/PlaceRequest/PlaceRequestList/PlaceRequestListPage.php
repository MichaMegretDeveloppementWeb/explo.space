<?php

namespace App\Livewire\Admin\PlaceRequest\PlaceRequestList;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class PlaceRequestListPage extends Component
{
    /**
     * Paramètres de filtres synchronisés avec l'URL
     * Support multi-sélection des statuts
     *
     * @var array<string>|string
     */
    #[Url(as: 'status')]
    public array|string $status = [];

    /**
     * Paramètres de tri synchronisés avec l'URL
     */
    #[Url(as: 's')]
    public string $sortBy = 'created_at';

    #[Url(as: 'd')]
    public string $sortDirection = 'desc';

    /**
     * Paramètres de pagination synchronisés avec l'URL
     */
    #[Url(as: 'pp')]
    public int $perPage = 20;

    /**
     * Initialiser les valeurs par défaut
     */
    public function mount(): void
    {
        // Normaliser le statut en tableau si c'est une string depuis l'URL
        // Ex: status=pending,submitted -> ['pending', 'submitted']
        if (is_string($this->status)) {
            if ($this->status === '' || $this->status === 'all') {
                $this->status = [];
            } else {
                $this->status = array_filter(explode(',', $this->status));
            }
        }
    }

    /**
     * Écouter les changements de filtres depuis PlaceRequestListFilters
     *
     * @param  array<string>  $status
     */
    #[On('filters:updated')]
    public function updateFilters(array $status): void
    {
        $this->status = $status;

        $this->skipRender();
    }

    /**
     * Écouter les changements de tri depuis PlaceRequestListTable
     */
    #[On('sorting:updated')]
    public function updateSorting(string $sortBy, string $sortDirection): void
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;

        $this->skipRender();
    }

    /**
     * Écouter les changements de pagination depuis PlaceRequestListTable
     */
    #[On('pagination:updated')]
    public function updatePagination(int $perPage): void
    {
        $this->perPage = $perPage;

        $this->skipRender();
    }

    /**
     * Render du composant parent
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.place-request.place-request-list.place-request-list-page', [
            // On passe uniquement les props initiales aux composants enfants
            'initialFilters' => [
                'status' => $this->status,
            ],
            'initialSorting' => [
                'sortBy' => $this->sortBy,
                'sortDirection' => $this->sortDirection,
            ],
            'initialPerPage' => $this->perPage,
        ]);
    }
}
