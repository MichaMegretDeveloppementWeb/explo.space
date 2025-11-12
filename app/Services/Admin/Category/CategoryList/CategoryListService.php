<?php

namespace App\Services\Admin\Category\CategoryList;

use App\Contracts\Repositories\Admin\Category\List\CategoryListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryListService
{
    public function __construct(
        private readonly CategoryListRepositoryInterface $categoryListRepository,
        private readonly CategoryListFilterValidationService $filterValidation,
        private readonly CategoryListSortingValidationService $sortingValidation,
        private readonly CategoryListPaginationValidationService $paginationValidation,
    ) {}

    /**
     * Get paginated categories with filters, sorting, and pagination
     *
     * @param  array{search: string, activeFilter: string}  $filters
     * @param  array{sortBy: string, sortDirection: string}  $sorting
     * @param  array{perPage: int}  $pagination
     * @return LengthAwarePaginator<int, \App\Models\Category>
     */
    public function getPaginatedCategories(array $filters, array $sorting, array $pagination): LengthAwarePaginator
    {
        // Validate and clean inputs through dedicated services
        $cleanedFilters = $this->filterValidation->validate($filters);
        $cleanedSorting = $this->sortingValidation->validate($sorting);
        $perPage = $this->paginationValidation->validate($pagination);

        // Delegate to repository
        return $this->categoryListRepository->paginate(
            $cleanedFilters,
            $cleanedSorting['column'],
            $cleanedSorting['direction'],
            $perPage
        );
    }
}
