<?php

namespace App\Contracts\Repositories\Admin\Category\List;

use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryListRepositoryInterface
{
    /**
     * Get paginated categories with filters and sorting
     *
     * @param  array{search?: string, is_active?: string}  $filters
     * @return LengthAwarePaginator<int, \App\Models\Category>
     */
    public function paginate(array $filters, string $sortBy, string $sortDirection, int $perPage): LengthAwarePaginator;

    /**
     * Get total count of all categories
     */
    public function getTotalCount(): int;
}
