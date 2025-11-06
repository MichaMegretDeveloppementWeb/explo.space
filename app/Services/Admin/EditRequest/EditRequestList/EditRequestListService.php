<?php

namespace App\Services\Admin\EditRequest\EditRequestList;

use App\Contracts\Repositories\Admin\EditRequest\EditRequestListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class EditRequestListService
{
    public function __construct(
        private EditRequestListRepositoryInterface $editRequestListRepository,
        private EditRequestListFilterValidationService $filterValidation,
        private EditRequestListSortingValidationService $sortingValidation,
        private EditRequestListPaginationValidationService $paginationValidation,
    ) {}

    /**
     * Récupérer les demandes de modification/signalement paginées avec validation des paramètres
     *
     * @param  array{search?: string, type?: string, status?: array<int, string>}  $filters
     * @param  array{sortBy?: string, sortDirection?: string}  $sorting
     * @param  array{perPage?: int}  $pagination
     * @return LengthAwarePaginator<int, \App\Models\EditRequest>
     *
     * @throws ValidationException
     */
    public function getPaginatedEditRequests(array $filters, array $sorting, array $pagination): LengthAwarePaginator
    {
        // Valider et nettoyer les filtres
        $cleanedFilters = $this->filterValidation->validate($filters);

        // Valider et nettoyer le tri
        $cleanedSorting = $this->sortingValidation->validate($sorting);

        // Valider et nettoyer la pagination
        $perPage = $this->paginationValidation->validate($pagination);

        // Récupérer les demandes depuis le repository
        return $this->editRequestListRepository->getPaginatedEditRequests(
            $cleanedFilters,
            $cleanedSorting,
            $perPage
        );
    }

    /**
     * Récupérer les valeurs par défaut pour les filtres
     *
     * @return array{search: string, type: string, status: string}
     */
    public function getDefaultFilters(): array
    {
        return [
            'search' => '',
            'type' => '',
            'status' => '',
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
