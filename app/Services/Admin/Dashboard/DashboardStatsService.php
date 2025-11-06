<?php

namespace App\Services\Admin\Dashboard;

use App\Contracts\Repositories\Admin\Dashboard\DashboardStatsRepositoryInterface;

class DashboardStatsService
{
    /**
     * Repository pour les statistiques du dashboard
     */
    protected DashboardStatsRepositoryInterface $statsRepository;

    /**
     * Injection du repository
     */
    public function __construct(DashboardStatsRepositoryInterface $statsRepository)
    {
        $this->statsRepository = $statsRepository;
    }

    /**
     * Récupérer toutes les statistiques du dashboard
     *
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        return [
            'total_places' => $this->statsRepository->getTotalPlaces(),
            'pending_place_requests' => $this->statsRepository->getPendingPlaceRequests(),
            'pending_edit_requests' => $this->statsRepository->getPendingEditRequests(),
            'total_tags' => $this->statsRepository->getTotalTags(),
            'total_categories' => $this->statsRepository->getTotalCategories(),
        ];
    }

    /**
     * Récupérer les dernières demandes de lieux
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\PlaceRequest>
     */
    public function getRecentPlaceRequests(int $limit = 5): \Illuminate\Support\Collection
    {
        return $this->statsRepository->getRecentPlaceRequests($limit);
    }

    /**
     * Récupérer les derniers lieux ajoutés
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\Place>
     */
    public function getRecentPlaces(int $limit = 5): \Illuminate\Support\Collection
    {
        return $this->statsRepository->getRecentPlaces($limit);
    }

    /**
     * Récupérer les dernières demandes de modification/signalement
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\EditRequest>
     */
    public function getRecentEditRequests(int $limit = 5): \Illuminate\Support\Collection
    {
        return $this->statsRepository->getRecentEditRequests($limit);
    }
}
