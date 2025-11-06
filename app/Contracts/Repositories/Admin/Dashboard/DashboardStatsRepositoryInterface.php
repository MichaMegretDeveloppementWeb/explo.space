<?php

namespace App\Contracts\Repositories\Admin\Dashboard;

use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceRequest;
use Illuminate\Support\Collection;

interface DashboardStatsRepositoryInterface
{
    /**
     * Récupérer le nombre total de lieux
     */
    public function getTotalPlaces(): int;

    /**
     * Récupérer le nombre de demandes de lieux en attente
     */
    public function getPendingPlaceRequests(): int;

    /**
     * Récupérer le nombre de demandes de modifications en attente
     */
    public function getPendingEditRequests(): int;

    /**
     * Récupérer le nombre total de tags actifs
     */
    public function getTotalTags(): int;

    /**
     * Récupérer le nombre total de catégories actives
     */
    public function getTotalCategories(): int;

    /**
     * Récupérer les dernières demandes de lieux
     *
     * @param  int  $limit  Nombre de demandes à récupérer
     * @return Collection<int, PlaceRequest>
     */
    public function getRecentPlaceRequests(int $limit = 5): Collection;

    /**
     * Récupérer les derniers lieux ajoutés
     *
     * @param  int  $limit  Nombre de lieux à récupérer
     * @return Collection<int, Place>
     */
    public function getRecentPlaces(int $limit = 5): Collection;

    /**
     * Récupérer les dernières demandes de modification/signalement
     *
     * @param  int  $limit  Nombre de demandes à récupérer
     * @return Collection<int, EditRequest>
     */
    public function getRecentEditRequests(int $limit = 5): Collection;
}
