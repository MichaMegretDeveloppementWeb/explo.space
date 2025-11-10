<?php

namespace App\Contracts\Repositories\Admin\Tag;

use Illuminate\Pagination\LengthAwarePaginator;

interface TagListRepositoryInterface
{
    /**
     * Get paginated tags with filters and sorting
     *
     * @param  array{search: string, activeFilter: string, locale: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\Tag>
     */
    public function getPaginatedTags(array $filters, array $sorting, int $perPage): LengthAwarePaginator;
}
