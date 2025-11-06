<?php

namespace App\Contracts\Repositories\Web\Place\PreviewModal;

use App\DTO\Web\Place\PlacePreviewDTO;
use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;

interface PlacePreviewRepositoryInterface
{
    /**
     * Récupère les données de prévisualisation d'un lieu par son ID
     *
     * OPTIMISATIONS :
     * - Eager loading uniquement des relations nécessaires
     * - SELECT limité aux colonnes utiles pour la modale
     * - Utilisation du scope forLocale() pour la traduction active
     *
     * @param  int  $placeId  ID du lieu
     * @return PlacePreviewDTO DTO avec données optimisées
     *
     * @throws PlaceNotFoundException Si le lieu n'existe pas en base
     * @throws PlaceTranslationNotFoundException Si la traduction publiée n'existe pas pour la locale active
     */
    public function getPlacePreviewById(int $placeId): PlacePreviewDTO;
}
