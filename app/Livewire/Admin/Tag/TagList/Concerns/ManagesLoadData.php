<?php

namespace App\Livewire\Admin\Tag\TagList\Concerns;

use App\Services\Admin\Tag\TagList\TagListFilterValidationService;
use App\Services\Admin\Tag\TagList\TagListPaginationValidationService;
use App\Services\Admin\Tag\TagList\TagListService;
use App\Services\Admin\Tag\TagList\TagListSortingValidationService;
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
            $paginationService = app(TagListPaginationValidationService::class);
            $this->perPage = $paginationService->validate(['perPage' => $this->perPage]);
        } catch (ValidationException $e) {
            $this->perPage = 20; // Valeur par défaut
            $this->dispatch('pagination:updated', perPage: $this->perPage);
            $hasErrors = true;
        }

        // 2. Valider tri
        try {
            $sortingService = app(TagListSortingValidationService::class);
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
            $filterService = app(TagListFilterValidationService::class);
            $filterService->validate([
                'search' => $this->search,
                'activeFilter' => $this->activeFilter,
                'locale' => $this->locale,
            ]);
        } catch (ValidationException $e) {
            // Réinitialiser uniquement les filtres
            $this->search = '';
            $this->activeFilter = 'all';
            $this->locale = 'fr';
            $this->dispatch('filters:updated',
                search: $this->search,
                activeFilter: $this->activeFilter,
                locale: $this->locale
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
     * @return LengthAwarePaginator<int, \App\Models\Tag>
     */
    private function loadTags(): LengthAwarePaginator
    {
        // Validation préventive
        $this->validateAndCleanCurrentFilters();

        // Chargement avec garde-fou
        try {
            $service = app(TagListService::class);

            return $service->getPaginatedTags(
                [
                    'search' => $this->search,
                    'activeFilter' => $this->activeFilter,
                    'locale' => $this->locale,
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
        $this->search = '';
        $this->activeFilter = 'all';
        $this->locale = 'fr';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 20;
    }
}
