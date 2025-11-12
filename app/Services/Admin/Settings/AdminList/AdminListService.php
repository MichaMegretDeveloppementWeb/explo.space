<?php

namespace App\Services\Admin\Settings\AdminList;

use App\Contracts\Repositories\Admin\Settings\AdminList\AdminListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class AdminListService
{
    public function __construct(
        private AdminListRepositoryInterface $adminListRepository,
        private AdminListFilterValidationService $filterValidation,
        private AdminListSortingValidationService $sortingValidation,
        private AdminListPaginationValidationService $paginationValidation,
    ) {}

    /**
     * Récupérer les administrateurs paginés avec validation des paramètres
     *
     * @param  array{search?: string}  $filters
     * @param  array{sortBy?: string, sortDirection?: string}  $sorting
     * @param  array{perPage?: int}  $pagination
     * @return LengthAwarePaginator<int, \App\Models\User>
     *
     * @throws ValidationException
     */
    public function getPaginatedAdmins(array $filters, array $sorting, array $pagination): LengthAwarePaginator
    {
        // Valider et nettoyer les filtres
        $cleanedFilters = $this->filterValidation->validate($filters);

        // Valider et nettoyer le tri
        $cleanedSorting = $this->sortingValidation->validate($sorting);

        // Valider et nettoyer la pagination
        $perPage = $this->paginationValidation->validate($pagination);

        // Récupérer les administrateurs depuis le repository
        return $this->adminListRepository->getPaginatedAdmins(
            $cleanedFilters,
            $cleanedSorting,
            $perPage
        );
    }

    /**
     * Récupérer les valeurs par défaut pour les filtres
     *
     * @return array{search: string}
     */
    public function getDefaultFilters(): array
    {
        return [
            'search' => '',
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
