<?php

namespace App\Services\Admin\Tag\TagList;

use App\Contracts\Repositories\Admin\Tag\TagListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TagListService
{
    public function __construct(
        private readonly TagListRepositoryInterface $tagListRepository,
        private readonly TagListFilterValidationService $filterValidation,
        private readonly TagListSortingValidationService $sortingValidation,
        private readonly TagListPaginationValidationService $paginationValidation,
    ) {}

    /**
     * Get paginated tags with filters, sorting, and pagination
     *
     * @param  array{search: string, activeFilter: string, locale: string}  $filters
     * @param  array{sortBy: string, sortDirection: string}  $sorting
     * @param  array{perPage: int}  $pagination
     * @return LengthAwarePaginator<int, \App\Models\Tag>
     */
    public function getPaginatedTags(array $filters, array $sorting, array $pagination): LengthAwarePaginator
    {
        // Validate and clean inputs through dedicated services
        $cleanedFilters = $this->filterValidation->validate($filters);
        $cleanedSorting = $this->sortingValidation->validate($sorting);
        $perPage = $this->paginationValidation->validate($pagination);

        // Delegate to repository
        return $this->tagListRepository->getPaginatedTags(
            $cleanedFilters,
            $cleanedSorting,
            $perPage
        );
    }
}
