<?php

namespace App\Services\Admin\Place\Detail;

use App\Contracts\Repositories\Admin\Place\Detail\PlaceDetailRepositoryInterface;
use App\Models\Place;

class PlaceDetailService
{
    public function __construct(
        private PlaceDetailRepositoryInterface $placeDetailRepository
    ) {}

    /**
     * Récupérer les données complètes d'un lieu pour affichage détail
     *
     * @return array{}|array{
     *     place: Place,
     *     has_translations: bool,
     *     translation_count: int,
     *     has_tags: bool,
     *     tag_count: int,
     *     has_categories: bool,
     *     category_count: int,
     *     has_photos: bool,
     *     photo_count: int,
     *     main_photo: ?\App\Models\Photo
     * }
     */
    public function getPlaceDetail(int $placeId): array
    {
        $place = $this->placeDetailRepository->getPlaceWithRelations($placeId);

        if (! $place) {
            return [];
        }

        return [
            'place' => $place,

            // Stats traductions
            'has_translations' => $place->translations->isNotEmpty(),
            'translation_count' => $place->translations->count(),

            // Stats tags
            'has_tags' => $place->tags->isNotEmpty(),
            'tag_count' => $place->tags->count(),

            // Stats catégories
            'has_categories' => $place->categories->isNotEmpty(),
            'category_count' => $place->categories->count(),

            // Stats photos
            'has_photos' => $place->photos->isNotEmpty(),
            'photo_count' => $place->photos->count(),
            'main_photo' => $place->photos->firstWhere('is_main', true),
        ];
    }

    /**
     * Vérifier si un lieu existe avant suppression/modification
     */
    public function placeExists(int $placeId): bool
    {
        return $this->placeDetailRepository->exists($placeId);
    }
}
