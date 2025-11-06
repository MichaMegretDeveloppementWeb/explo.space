<?php

namespace App\Livewire\Admin\PlaceRequest\PlaceRequestList\Concerns;

use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListFilterValidationService;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListPaginationValidationService;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListService;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListSortingValidationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

trait ManagesLoadData
{
    /**
     * Valider et nettoyer les filtres actuels avant chargement
     * Réinitialise uniquement les filtres invalides
     */
    private function validateAndCleanCurrentFilters(): void
    {
        $hasErrors = false;

        // 1. Valider pagination
        try {
            $paginationService = app(PlaceRequestListPaginationValidationService::class);
            $this->perPage = $paginationService->validate(['perPage' => $this->perPage]);
        } catch (ValidationException $e) {
            $this->perPage = 20; // Valeur par défaut
            $this->dispatch('pagination:updated', perPage: $this->perPage);
            $hasErrors = true;
        }

        // 2. Valider tri
        try {
            $sortingService = app(PlaceRequestListSortingValidationService::class);
            $sortingService->validate([
                'sortBy' => $this->sortBy,
                'sortDirection' => $this->sortDirection,
            ]);
        } catch (ValidationException $e) {
            $this->sortBy = 'created_at';
            $this->sortDirection = 'desc';
            $this->dispatch('sorting:updated',
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection
            );
            $hasErrors = true;
        }

        // 3. Valider filtres
        try {
            $filterService = app(PlaceRequestListFilterValidationService::class);
            $filterService->validate([
                'status' => $this->status,
            ]);
        } catch (ValidationException $e) {
            // Réinitialiser uniquement le filtre status
            $this->status = [];
            $this->dispatch('filters:updated', status: $this->status);
            $hasErrors = true;
        }

        // Flash message si erreurs détectées
        if ($hasErrors) {
            session()->flash('error', 'Des paramètres invalides ont été détectés et réinitialisés.');
        }
    }

    /**
     * Charger les données depuis le service
     *
     * @return LengthAwarePaginator<int, \App\Models\PlaceRequest>
     */
    private function loadPlaceRequests(): LengthAwarePaginator
    {
        // Validation préventive
        $this->validateAndCleanCurrentFilters();

        // Chargement avec garde-fou
        try {
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
        } catch (\Exception $e) {
            // Construire le message d'erreur
            $message = 'Une erreur est survenue lors du chargement des données.';
            if (config('app.debug')) {
                $message .= ' ('.$e->getMessage().')';
            }

            // Ajouter l'erreur Livewire
            $this->addError('load-data', $message);

            // Retourner un paginateur vide
            return new LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    /**
     * Réinitialiser tous les filtres aux valeurs par défaut
     */
    protected function resetFiltersToDefaults(): void
    {
        $this->status = [];
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 20;
    }
}
