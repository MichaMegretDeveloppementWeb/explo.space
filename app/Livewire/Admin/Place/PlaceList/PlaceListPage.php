<?php

namespace App\Livewire\Admin\Place\PlaceList;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class PlaceListPage extends Component
{
    /**
     * Paramètres de filtres synchronisés avec l'URL
     */
    #[Url(as: 'q')]
    public string $search = '';

    /** @var array<int, string> */
    #[Url(as: 't')]
    public array $tags = [];

    #[Url(as: 'l')]
    public string $locale = 'fr';

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
     * Écouter les changements de filtres depuis PlaceListFilters
     *
     * @param  array<int, string>  $tags
     */
    #[On('filters:updated')]
    public function updateFilters(string $search, array $tags, string $locale): void
    {
        $this->search = $search;
        $this->tags = $tags;
        $this->locale = $locale;

        $this->skipRender();
    }

    /**
     * Écouter les changements de tri depuis PlaceListTable
     */
    #[On('sorting:updated')]
    public function updateSorting(string $sortBy, string $sortDirection): void
    {
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;

        $this->skipRender();
    }

    /**
     * Écouter les changements de pagination depuis PlaceListTable
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
        return view('livewire.admin.place.place-list.place-list-page', [
            // On passe uniquement les props initiales aux composants enfants
            'initialFilters' => [
                'search' => $this->search,
                'tags' => $this->tags,
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
