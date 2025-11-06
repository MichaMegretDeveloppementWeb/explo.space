<?php

namespace App\Services\Web\Place\PreviewModal;

use App\Contracts\Repositories\Web\Place\PreviewModal\PlacePreviewRepositoryInterface;
use App\DTO\Web\Place\PlacePreviewDTO;
use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;
use InvalidArgumentException;

class PlacePreviewService
{
    public function __construct(
        private readonly PlacePreviewRepositoryInterface $placePreviewRepository
    ) {}

    /**
     * Récupère les données de prévisualisation d'un lieu par son ID
     *
     * Orchestration :
     * - Validation de l'ID du lieu
     * - Appel au repository pour récupérer les données
     * - Laisse passer les exceptions du repository
     *
     * @param  int  $placeId  ID du lieu
     * @return PlacePreviewDTO DTO avec données optimisées
     *
     * @throws InvalidArgumentException Si l'ID du lieu est invalide (≤ 0)
     * @throws PlaceNotFoundException Si le lieu n'existe pas en base
     * @throws PlaceTranslationNotFoundException Si la traduction publiée n'existe pas
     */
    public function getPlacePreviewById(int $placeId): PlacePreviewDTO
    {
        // Validation de l'ID
        if ($placeId <= 0) {
            throw new InvalidArgumentException("Invalid place ID: {$placeId}. ID must be a positive integer.");
        }

        // Appel au repository - les exceptions sont propagées naturellement
        return $this->placePreviewRepository->getPlacePreviewById($placeId);
    }
}
