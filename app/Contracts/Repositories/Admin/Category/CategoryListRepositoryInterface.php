<?php

namespace App\Contracts\Repositories\Admin\Category;

use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryListRepositoryInterface
{
    /**
     * Get paginated categories with filters and sorting
     *
     * @param  array{search: string, activeFilter: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\Category>
     */
    public function getPaginatedCategories(array $filters, array $sorting, int $perPage): LengthAwarePaginator;
}
