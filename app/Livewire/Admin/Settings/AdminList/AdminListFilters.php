<?php

namespace App\Livewire\Admin\Settings\AdminList;

use Livewire\Component;

class AdminListFilters extends Component
{
    /**
     * Filtre local
     */
    public string $search = '';

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.settings.admin-list.admin-list-filters');
    }

    /**
     * Appliquer les filtres lors de la modification du champ
     */
    public function updatedSearch(): void
    {
        $this->applyFilters();
    }

    /**
     * Appliquer les filtres (déclenche événement vers AdminListTable)
     */
    public function applyFilters(): void
    {
        $this->dispatch('filters:updated', search: $this->search);
    }

    /**
     * Réinitialiser les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->applyFilters();
    }
}
