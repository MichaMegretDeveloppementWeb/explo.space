<?php

namespace App\Repositories\Admin\PlaceRequest;

use App\Contracts\Repositories\Admin\PlaceRequest\PlaceRequestListRepositoryInterface;
use App\Models\PlaceRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class PlaceRequestListRepository implements PlaceRequestListRepositoryInterface
{
    /**
     * Récupérer les propositions de lieux paginées avec filtres, tri et eager loading
     *
     * @param  array{status?: array<string>}  $filters
     * @param  array{sortBy: string, sortDirection: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\PlaceRequest>
     */
    public function getPaginatedPlaceRequests(array $filters, array $sorting, int $perPage): LengthAwarePaginator
    {
        $query = PlaceRequest::query()
            ->with([
                'viewedByAdmin:id,name',
                'processedByAdmin:id,name',
                'photos',
            ]);

        // Filtrage par statut (support multi-sélection)
        if (! empty($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        // Tri
        $sortColumn = match ($sorting['sortBy']) {
            'title' => 'title',
            'status' => 'status',
            'created_at' => 'created_at',
            default => 'created_at',
        };

        $query->orderBy($sortColumn, $sorting['sortDirection']);

        return $query->paginate($perPage);
    }
}
