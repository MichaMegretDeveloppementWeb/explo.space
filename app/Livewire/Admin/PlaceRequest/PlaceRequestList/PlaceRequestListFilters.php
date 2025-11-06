<?php

namespace App\Livewire\Admin\PlaceRequest\PlaceRequestList;

use App\Models\PlaceRequest;
use Livewire\Component;

class PlaceRequestListFilters extends Component
{
    /**
     * Filtres locaux (multi-sélection)
     *
     * @var array<string>
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
     * @param  array{status: array<string>}  $initialFilters
     */
    public function mount(array $initialFilters): void
    {
        $this->status = $initialFilters['status'] ?? [];

        // Charger les statistiques des statuts
        $this->loadStatusCounts();
    }

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.place-request.place-request-list.place-request-list-filters');
    }

    /**
     * Charger les statistiques des statuts (1 seule requête optimisée)
     */
    private function loadStatusCounts(): void
    {
        // Récupérer tous les counts groupés par statut en une seule requête
        $counts = PlaceRequest::selectRaw('status, COUNT(*) as count')
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

    /**
     * Livewire hook appelé automatiquement quand $status change via wire:model
     */
    public function updatedStatus(): void
    {
        // Dispatch vers parent pour sync URL
        $this->dispatch('filters:updated', status: $this->status);
    }

    /**
     * Réinitialiser les filtres (tout afficher)
     */
    public function resetFilters(): void
    {
        $this->status = [];

        // Dispatch manuel car updatedStatus() n'est pas appelé automatiquement lors d'une réinitialisation manuelle
        $this->dispatch('filters:updated', status: $this->status);
    }
}
