<?php

namespace App\Repositories\Admin\Place\Management;

use App\Contracts\Repositories\Admin\Place\Management\PlaceDeleteRepositoryInterface;
use App\Models\Place;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaceDeleteRepository implements PlaceDeleteRepositoryInterface
{
    /**
     * Supprimer un lieu par son ID
     * Note: La suppression en cascade des relations est gérée par les contraintes FK de la base de données
     */
    public function deletePlace(int $placeId): bool
    {
        try {
            return DB::transaction(function () use ($placeId) {
                $place = Place::findOrFail($placeId);

                return $place->delete();
            });
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du lieu', [
                'place_id' => $placeId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Vérifier si un lieu existe
     */
    public function placeExists(int $placeId): bool
    {
        return Place::where('id', $placeId)->exists();
    }

    /**
     * Récupérer un lieu pour vérification avant suppression
     */
    public function findPlace(int $placeId): ?Place
    {
        return Place::find($placeId);
    }
}
