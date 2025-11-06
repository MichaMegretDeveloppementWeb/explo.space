<?php

namespace App\Services\Admin\Place\PlaceList;

use App\Contracts\Repositories\Admin\Place\PlaceListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PlaceListService
{
    public function __construct(
        private PlaceListRepositoryInterface $placeListRepository,
        private PlaceListFilterValidationService $filterValidation,
        private PlaceListSortingValidationService $sortingValidation,
        private PlaceListPaginationValidationService $paginationValidation,
    ) {}

    /**
     * Récupérer les lieux paginés avec validation des paramètres
     *
     * @param  array{search?: string, tags?: array<int, string>, locale?: string}  $filters
     * @param  array{sortBy?: string, sortDirection?: string}  $sorting
     * @param  array{perPage?: int}  $pagination
     * @return LengthAwarePaginator<int, \App\Models\Place>
     *
     * @throws ValidationException
     */
    public function getPaginatedPlaces(array $filters, array $sorting, array $pagination): LengthAwarePaginator
    {
        // Valider et nettoyer les filtres
        $cleanedFilters = $this->filterValidation->validate($filters);

        // Valider et nettoyer le tri
        $cleanedSorting = $this->sortingValidation->validate($sorting);

        // Valider et nettoyer la pagination
        $perPage = $this->paginationValidation->validate($pagination);

        // Récupérer les lieux depuis le repository
        return $this->placeListRepository->getPaginatedPlaces(
            $cleanedFilters,
            $cleanedSorting,
            $perPage
        );
    }

    /**
     * Récupérer les valeurs par défaut pour les filtres
     *
     * @return array{search: string, tags: array<int, string>, locale: string}
     */
    public function getDefaultFilters(): array
    {
        return [
            'search' => '',
            'tags' => [],
            'locale' => config('locales.default', 'fr'),
        ];
    }

    /**
     * Récupérer les valeurs par défaut pour le tri
     *
     * @return array{column: string, direction: string}
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
