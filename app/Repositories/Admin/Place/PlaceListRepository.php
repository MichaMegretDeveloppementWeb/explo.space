<?php

namespace App\Repositories\Admin\Place;

use App\Contracts\Repositories\Admin\Place\PlaceListRepositoryInterface;
use App\Models\Place;
use Illuminate\Pagination\LengthAwarePaginator;

class PlaceListRepository implements PlaceListRepositoryInterface
{
    /**
     * Récupérer les lieux paginés avec filtres, tri et eager loading
     *
     * @param  array{search: string, tags: array<int, string>, categories: array<int, int>, locale: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\Place>
     */
    public function getPaginatedPlaces(array $filters, array $sorting, int $perPage): LengthAwarePaginator
    {
        $query = Place::query()
            ->with([
                'translations' => fn ($q) => $q->where('locale', $filters['locale'])
                    ->where('status', 'published'),
                'admin:id,name',
                'tags' => function ($query) use ($filters) {
                    $query->withWhereHas('translations', fn ($q) => $q->where('locale', $filters['locale'])
                        ->where('status', 'published'));
                },
                'categories',
                'photos' => fn ($q) => $q->where('is_main', true),
            ])
            ->whereHas('translations', fn ($q) => $q->where('locale', $filters['locale'])
                ->where('status', 'published'));

        // Filtrage par recherche (texte dans traductions)
        if (! empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('status', 'published')
                    ->where(function ($subQuery) use ($filters) {
                        $subQuery->where('title', 'like', '%'.$filters['search'].'%')
                            ->orWhere('description', 'like', '%'.$filters['search'].'%')
                            ->orWhere('practical_info', 'like', '%'.$filters['search'].'%');
                    });
            });
        }

        // Filtrage par tags
        if (! empty($filters['tags'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->whereHas('translations', function ($subQuery) use ($filters) {
                    $subQuery->where('locale', $filters['locale'])
                        ->whereIn('slug', $filters['tags']);
                });
            });
        }

        // Filtrage par catégories
        if (! empty($filters['categories'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->whereIn('categories.id', $filters['categories']);
            });
        }

        // Tri
        if ($sorting['column'] === 'title') {
            // Tri par titre (via traductions)
            $query->join('place_translations', function ($join) use ($filters) {
                $join->on('places.id', '=', 'place_translations.place_id')
                    ->where('place_translations.locale', '=', $filters['locale'])
                    ->where('place_translations.status', '=', 'published');
            })
                ->orderBy('place_translations.title', $sorting['direction'])
                ->select('places.*');
        } elseif ($sorting['column'] === 'created_at') {
            // Tri par date de création
            $query->orderBy('places.created_at', $sorting['direction']);
        } elseif ($sorting['column'] === 'updated_at') {
            // Tri par date de modification
            $query->orderBy('places.updated_at', $sorting['direction']);
        } elseif ($sorting['column'] === 'is_featured') {
            // Tri par mise à l'affiche
            $query->orderBy('places.is_featured', $sorting['direction']);
        }

        return $query->paginate($perPage);
    }
}
