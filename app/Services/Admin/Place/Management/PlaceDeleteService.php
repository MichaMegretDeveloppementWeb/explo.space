<?php

namespace App\Services\Admin\Place\Management;

use App\Contracts\Repositories\Admin\Place\Management\PlaceDeleteRepositoryInterface;
use Illuminate\Support\Facades\Log;

class PlaceDeleteService
{
    public function __construct(
        private PlaceDeleteRepositoryInterface $placeDeleteRepository
    ) {}

    /**
     * Supprimer un lieu
     *
     * @throws \Exception Si le lieu n'existe pas
     */
    public function deletePlace(int $placeId): bool
    {
        // Vérifier que le lieu existe
        if (! $this->placeDeleteRepository->placeExists($placeId)) {
            Log::warning('Tentative de suppression d\'un lieu inexistant', [
                'place_id' => $placeId,
            ]);

            throw new \Exception("Le lieu avec l'ID {$placeId} n'existe pas.");
        }

        // Supprimer le lieu
        $deleted = $this->placeDeleteRepository->deletePlace($placeId);

        if ($deleted) {
            Log::info('Lieu supprimé avec succès', [
                'place_id' => $placeId,
                'admin_id' => auth()->id(),
            ]);
        } else {
            Log::error('Échec de la suppression du lieu', [
                'place_id' => $placeId,
                'admin_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    /**
     * Vérifier si un lieu peut être supprimé
     * (pour le moment, tous les lieux peuvent être supprimés)
     */
    public function canDeletePlace(int $placeId): bool
    {
        return $this->placeDeleteRepository->placeExists($placeId);
    }
}
