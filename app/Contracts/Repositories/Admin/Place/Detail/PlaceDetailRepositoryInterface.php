<?php

namespace App\Contracts\Repositories\Admin\Place\Detail;

use App\Models\Place;

interface PlaceDetailRepositoryInterface
{
    /**
     * Récupérer un lieu avec toutes ses relations pour affichage détail
     */
    public function getPlaceWithRelations(int $placeId): ?Place;

    /**
     * Vérifier si un lieu existe
     */
    public function exists(int $placeId): bool;
}
