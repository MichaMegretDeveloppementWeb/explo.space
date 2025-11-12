<?php

namespace App\Contracts\Repositories\Admin\Settings\AdminList;

use Illuminate\Pagination\LengthAwarePaginator;

interface AdminListRepositoryInterface
{
    /**
     * Get paginated list of administrators with filters and sorting.
     *
     * @param  array{search: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\User>
     */
    public function getPaginatedAdmins(
        array $filters,
        array $sorting,
        int $perPage
    ): LengthAwarePaginator;
}
