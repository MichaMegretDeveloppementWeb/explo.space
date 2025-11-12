<?php

namespace App\Contracts\Repositories\Admin\Place;

use Illuminate\Pagination\LengthAwarePaginator;

interface PlaceListRepositoryInterface
{
    /**
     * Récupérer les lieux paginés avec filtres, tri et eager loading
     *
     * @param  array{search: string, tags: array<int, string>, categories: array<int, int>, locale: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\Place>
     */
    public function getPaginatedPlaces(array $filters, array $sorting, int $perPage): LengthAwarePaginator;
}
