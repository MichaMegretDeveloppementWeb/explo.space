<?php

namespace App\Livewire\Admin\EditRequest\EditRequestList;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class EditRequestListPage extends Component
{
    /**
     * Paramètres de filtres synchronisés avec l'URL
     */
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'status')]
    public string $status = '';

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
        // Les valeurs par défaut sont déjà définies ci-dessus
        // URL parameters override these defaults automatically via Livewire
    }

    /**
     * Écouter les changements de filtres depuis EditRequestListFilters
     */
    #[On('filters:updated')]
    public function updateFilters(string $search, string $type, string $status): void
    {
        $this->search = $search;
        $this->type = $type;
        $this->status = $status;

        $this->skipRender();
    }

    /**
     * Écouter les changements de tri depuis EditRequestListTable
     */
    #[On('sorting:updated')]
    public function updateSorting(string $sortBy, string $sortDirection): void
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;

        $this->skipRender();
    }

    /**
     * Écouter les changements de pagination depuis EditRequestListTable
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
        return view('livewire.admin.edit-request.edit-request-list.edit-request-list-page', [
            // On passe uniquement les props initiales aux composants enfants
            'initialFilters' => [
                'search' => $this->search,
                'type' => $this->type,
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
