<?php

namespace App\Contracts\Repositories\Admin\Place\Management;

use App\Models\Place;

interface PlaceDeleteRepositoryInterface
{
    /**
     * Supprimer un lieu par son ID
     */
    public function deletePlace(int $placeId): bool;

    /**
     * Vérifier si un lieu existe
     */
    public function placeExists(int $placeId): bool;

    /**
     * Récupérer un lieu pour vérification avant suppression
     */
    public function findPlace(int $placeId): ?Place;
}
