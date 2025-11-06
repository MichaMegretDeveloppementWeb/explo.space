<?php

namespace App\Repositories\Admin\Dashboard;

use App\Contracts\Repositories\Admin\Dashboard\DashboardStatsRepositoryInterface;
use App\Enums\RequestStatus;
use App\Models\Category;
use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceRequest;
use App\Models\Tag;
use Illuminate\Support\Collection;

class DashboardStatsRepository implements DashboardStatsRepositoryInterface
{
    /**
     * Récupérer le nombre total de lieux
     */
    public function getTotalPlaces(): int
    {
        return Place::count();
    }

    /**
     * Récupérer le nombre de demandes de lieux en attente
     */
    public function getPendingPlaceRequests(): int
    {
        return PlaceRequest::whereIn('status', [RequestStatus::Submitted, RequestStatus::Pending])->count();
    }

    /**
     * Récupérer le nombre de demandes de modifications en attente
     */
    public function getPendingEditRequests(): int
    {
        return EditRequest::whereIn('status', [RequestStatus::Submitted, RequestStatus::Pending])->count();
    }

    /**
     * Récupérer le nombre total de tags actifs
     */
    public function getTotalTags(): int
    {
        return Tag::where('is_active', true)->count();
    }

    /**
     * Récupérer le nombre total de catégories actives
     */
    public function getTotalCategories(): int
    {
        return Category::where('is_active', true)->count();
    }

    /**
     * Récupérer les dernières demandes de lieux EN ATTENTE
     *
     * @param  int  $limit  Nombre de demandes à récupérer
     * @return Collection<int, PlaceRequest>
     */
    public function getRecentPlaceRequests(int $limit = 5): Collection
    {
        return PlaceRequest::with(['processedByAdmin'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les derniers lieux ajoutés
     *
     * @param  int  $limit  Nombre de lieux à récupérer
     * @return Collection<int, Place>
     */
    public function getRecentPlaces(int $limit = 5): Collection
    {
        return Place::with(['translations', 'admin'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupérer les dernières demandes de modification/signalement EN ATTENTE
     *
     * @param  int  $limit  Nombre de demandes à récupérer
     * @return Collection<int, EditRequest>
     */
    public function getRecentEditRequests(int $limit = 5): Collection
    {
        return EditRequest::with(['place.translations', 'processedByAdmin'])
            ->whereIn('status', [RequestStatus::Submitted, RequestStatus::Pending])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
