<?php

namespace App\Livewire\Admin\EditRequest\EditRequestList;

use App\Models\EditRequest;
use Livewire\Component;

class EditRequestListFilters extends Component
{
    /**
     * Filtres locaux
     */
    public string $search = '';

    public string $type = '';

    /**
     * @var array<int, string>
     */
    public array $status = [];

    /**
     * Statistiques des statuts (pour affichage des counts)
     *
     * @var array{all: int, submitted: int, pending: int, accepted: int, refused: int}
     */
    public array $statusCounts = [
        'all' => 0,
        'submitted' => 0,
        'pending' => 0,
        'accepted' => 0,
        'refused' => 0,
    ];

    /**
     * Initialiser depuis les props du parent
     *
     * @param  array{search: string, type: string, status: array<int, string>}  $initialFilters
     */
    public function mount(array $initialFilters): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->type = $initialFilters['type'] ?? '';
        $this->status = $initialFilters['status'] ?? [];

        // Charger les statistiques des statuts
        $this->loadStatusCounts();
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
        $this->status = [];

        $this->applyFilters();
    }

    /**
     * Charger les statistiques des statuts (1 seule requête optimisée)
     */
    private function loadStatusCounts(): void
    {
        // Récupérer tous les counts groupés par statut en une seule requête
        $counts = EditRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Construire le tableau avec fallback à 0 pour les statuts sans résultat
        $this->statusCounts = [
            'all' => array_sum($counts),
            'submitted' => $counts['submitted'] ?? 0,
            'pending' => $counts['pending'] ?? 0,
            'accepted' => $counts['accepted'] ?? 0,
            'refused' => $counts['refused'] ?? 0,
        ];
    }
}
