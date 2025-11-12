<?php

namespace App\Livewire\Admin\Category\CategoryList\Concerns;

use App\Services\Admin\Category\CategoryList\CategoryListFilterValidationService;
use App\Services\Admin\Category\CategoryList\CategoryListPaginationValidationService;
use App\Services\Admin\Category\CategoryList\CategoryListService;
use App\Services\Admin\Category\CategoryList\CategoryListSortingValidationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
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
            $paginationService = app(CategoryListPaginationValidationService::class);
            $this->perPage = $paginationService->validate(['perPage' => $this->perPage]);
        } catch (ValidationException $e) {
            $this->perPage = 20; // Valeur par défaut
            $this->dispatch('pagination:updated', perPage: $this->perPage);
            $hasErrors = true;
        }

        // 2. Valider tri
        try {
            $sortingService = app(CategoryListSortingValidationService::class);
            $sortingService->validate([
                'sortBy' => $this->sortBy,
                'sortDirection' => $this->sortDirection,
            ]);
        } catch (ValidationException $e) {
            $this->sortBy = 'name';
            $this->sortDirection = 'asc';
            $this->dispatch('sorting:updated',
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection
            );
            $hasErrors = true;
        }

        // 3. Valider filtres
        try {
            $filterService = app(CategoryListFilterValidationService::class);
            $filterService->validate([
                'search' => $this->search,
                'activeFilter' => $this->activeFilter,
            ]);
        } catch (ValidationException $e) {
            // Réinitialiser uniquement les filtres
            $this->search = '';
            $this->activeFilter = 'all';
            $this->dispatch('filters:updated',
                search: $this->search,
                activeFilter: $this->activeFilter
            );
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
     * @return LengthAwarePaginator<int, \App\Models\Category>
     */
    private function loadCategories(): LengthAwarePaginator
    {
        // Validation préventive
        $this->validateAndCleanCurrentFilters();

        // Chargement avec garde-fou
        try {
            $service = app(CategoryListService::class);

            return $service->getPaginatedCategories(
                [
                    'search' => $this->search,
                    'activeFilter' => $this->activeFilter,
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

            Log::error($e->getMessage());

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
        $this->search = '';
        $this->activeFilter = 'all';
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->perPage = 20;
    }
}
