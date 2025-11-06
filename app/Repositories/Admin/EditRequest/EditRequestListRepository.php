<?php

namespace App\Repositories\Admin\EditRequest;

use App\Contracts\Repositories\Admin\EditRequest\EditRequestListRepositoryInterface;
use App\Models\EditRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class EditRequestListRepository implements EditRequestListRepositoryInterface
{
    /**
     * Récupérer les demandes de modification/signalement paginées avec filtres, tri et eager loading
     *
     * @param  array{search: string, type: string, status: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\EditRequest>
     */
    public function getPaginatedEditRequests(array $filters, array $sorting, int $perPage): LengthAwarePaginator
    {
        $query = EditRequest::query()
            ->with([
                'place.translations' => function ($query) {
                    $query->where('place_translations.locale', 'fr')->select('id', 'place_id', 'locale', 'title', 'slug');
                },
                'processedByAdmin:id,name,email',
                'viewedByAdmin:id,name,email',
            ]);

        // Filtrage par recherche (nom du lieu OU email de contact)
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                // Recherche dans le nom du lieu (via traductions)
                $q->whereHas('place.translations', function ($subQuery) use ($filters) {
                    $subQuery->where('title', 'like', '%'.$filters['search'].'%');
                })
                // OU recherche dans l'email de contact
                    ->orWhere('contact_email', 'like', '%'.$filters['search'].'%');
            });
        }

        // Filtrage par type
        if (! empty($filters['type'])) {
            $query->where('edit_requests.type', $filters['type']);
        }

        // Filtrage par statut
        if (! empty($filters['status'])) {
            $query->where('edit_requests.status', $filters['status']);
        }

        // Tri
        if ($sorting['column'] === 'created_at') {
            $query->orderBy('edit_requests.created_at', $sorting['direction']);
        } elseif ($sorting['column'] === 'status') {
            $query->orderBy('edit_requests.status', $sorting['direction']);
        } elseif ($sorting['column'] === 'type') {
            $query->orderBy('edit_requests.type', $sorting['direction']);
        } elseif ($sorting['column'] === 'contact_email') {
            $query->orderBy('edit_requests.contact_email', $sorting['direction']);
        } elseif ($sorting['column'] === 'place') {
            // Tri par nom du lieu (traduction française uniquement - partie admin en français)
            $query->join('places', 'edit_requests.place_id', '=', 'places.id')
                ->join('place_translations', function ($join) {
                    $join->on('places.id', '=', 'place_translations.place_id')
                        ->where('place_translations.locale', '=', 'fr')
                        ->where('place_translations.status', '=', 'published');
                })
                ->orderBy('place_translations.title', $sorting['direction'])
                ->select('edit_requests.*');
        }

        return $query->paginate($perPage);
    }
}
