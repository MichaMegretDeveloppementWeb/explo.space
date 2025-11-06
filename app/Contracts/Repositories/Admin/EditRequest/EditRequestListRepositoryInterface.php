<?php

namespace App\Contracts\Repositories\Admin\EditRequest;

use Illuminate\Pagination\LengthAwarePaginator;

interface EditRequestListRepositoryInterface
{
    /**
     * Récupérer les demandes de modification/signalement paginées avec filtres, tri et eager loading
     *
     * @param  array{search: string, type: string, status: array<int, string>}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\EditRequest>
     */
    public function getPaginatedEditRequests(array $filters, array $sorting, int $perPage): LengthAwarePaginator;
}
