<?php

namespace App\Livewire\Admin\EditRequest\EditRequestList;

use Livewire\Component;

class EditRequestListFilters extends Component
{
    /**
     * Filtres locaux
     */
    public string $search = '';

    public string $type = '';

    public string $status = '';

    /**
     * Initialiser depuis les props du parent
     *
     * @param  array{search: string, type: string, status: string}  $initialFilters
     */
    public function mount(array $initialFilters): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->type = $initialFilters['type'] ?? '';
        $this->status = $initialFilters['status'] ?? '';
    }

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.edit-request.edit-request-list.edit-request-list-filters');
    }

    /**
     * Déclencher l'application des filtres sur modification du champ de recherche
     */
    public function updatedSearch(): void
    {
        $this->applyFilters();
    }

    /**
     * Déclencher l'application des filtres sur modification du type
     */
    public function updatedType(): void
    {
        $this->applyFilters();
    }

    /**
     * Déclencher l'application des filtres sur modification du statut
     */
    public function updatedStatus(): void
    {
        $this->applyFilters();
    }

    /**
     * Appliquer les filtres (déclenche événement)
     */
    public function applyFilters(): void
    {
        // Dispatch vers parent pour sync URL
        $this->dispatch('filters:updated',
            search: $this->search,
            type: $this->type,
            status: $this->status
        );
    }

    /**
     * Réinitialiser tous les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->type = '';
        $this->status = '';

        $this->applyFilters();
    }
}
