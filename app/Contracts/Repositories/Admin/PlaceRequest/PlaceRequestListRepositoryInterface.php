<?php

namespace App\Contracts\Repositories\Admin\PlaceRequest;

use Illuminate\Pagination\LengthAwarePaginator;

interface PlaceRequestListRepositoryInterface
{
    /**
     * Récupérer les propositions de lieux paginées avec filtres et tri
     *
     * @param  array{status?: array<string>}  $filters
     * @param  array{sortBy: string, sortDirection: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\PlaceRequest>
     */
    public function getPaginatedPlaceRequests(array $filters, array $sorting, int $perPage): LengthAwarePaginator;
}
