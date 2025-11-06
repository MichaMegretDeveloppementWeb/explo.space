<?php

namespace App\Services\Admin\PlaceRequest\PlaceRequestList;

use App\Contracts\Repositories\Admin\PlaceRequest\PlaceRequestListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PlaceRequestListService
{
    public function __construct(
        private PlaceRequestListRepositoryInterface $placeRequestListRepository,
        private PlaceRequestListFilterValidationService $filterValidation,
        private PlaceRequestListSortingValidationService $sortingValidation,
        private PlaceRequestListPaginationValidationService $paginationValidation,
    ) {}

    /**
     * Récupérer les propositions de lieux paginées avec validation des paramètres
     *
     * @param  array{status?: array<string>|string}  $filters
     * @param  array{sortBy?: string, sortDirection?: string}  $sorting
     * @param  array{perPage?: int}  $pagination
     * @return LengthAwarePaginator<int, \App\Models\PlaceRequest>
     *
     * @throws ValidationException
     */
    public function getPaginatedPlaceRequests(array $filters, array $sorting, array $pagination): LengthAwarePaginator
    {
        // Valider et nettoyer les filtres
        $cleanedFilters = $this->filterValidation->validate($filters);

        // Valider et nettoyer le tri
        $cleanedSorting = $this->sortingValidation->validate($sorting);

        // Valider et nettoyer la pagination
        $perPage = $this->paginationValidation->validate($pagination);

        // Récupérer les propositions depuis le repository
        return $this->placeRequestListRepository->getPaginatedPlaceRequests(
            $cleanedFilters,
            $cleanedSorting,
            $perPage
        );
    }

    /**
     * Récupérer les valeurs par défaut pour les filtres
     *
     * @return array{status: string}
     */
    public function getDefaultFilters(): array
    {
        return ['status' => 'all'];
    }

    /**
     * Récupérer les valeurs par défaut pour le tri
     *
     * @return array{sortBy: string, sortDirection: string}
     */
    public function getDefaultSorting(): array
    {
        return $this->sortingValidation->getDefaultSort();
    }

    /**
     * Récupérer la valeur par défaut pour la pagination
     */
    public function getDefaultPerPage(): int
    {
        return $this->paginationValidation->getDefaultValue();
    }
}
