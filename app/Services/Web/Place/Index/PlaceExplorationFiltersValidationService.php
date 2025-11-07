<?php

namespace App\Services\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Enums\ValidationStrategy;
use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use App\Services\Web\Tag\TagSelectionService;
use App\Support\Config\PlaceSearchConfig;

/**
 * Service de validation des filtres d'exploration
 *
 * STRATÉGIES :
 * - THROW : Valide + lance exception si invalide (PlaceFilters, PlaceMap, PlaceList)
 * - CORRECT_SILENTLY : Valide + corrige + log si invalide (PlaceExplorer depuis URL)
 *
 * LOGS : Uniquement pour CORRECT_SILENTLY (1 seul log par erreur, pas de redondance)
 */
class PlaceExplorationFiltersValidationService
{
    public function __construct(
        private TagSelectionService $tagService
    ) {}

    /**
     * Valide un DTO avec stratégie
     *
     * THROW : Lance exception immédiatement si erreur
     * CORRECT_SILENTLY : Corrige silencieusement + retourne DTO corrigé
     *
     * @throws InvalidPlaceFiltersException Si THROW et erreur
     */
    public function validate(PlaceExplorationFiltersDTO $dto, ValidationStrategy $strategy = ValidationStrategy::THROW): PlaceExplorationFiltersDTO
    {
        // Mode THROW : valider sans corriger
        if ($strategy === ValidationStrategy::THROW) {
            $this->validateMode($dto->mode, $strategy);
            $this->validateRadius($dto->radius, $strategy);
            $this->validateLatitude($dto->latitude, $strategy);
            $this->validateLongitude($dto->longitude, $strategy);
            $this->validateTags($dto->tags, $strategy);

            return $dto; // Pas de correction, retour direct
        }

        // Mode CORRECT_SILENTLY : corriger silencieusement
        $correctedMode = $this->validateMode($dto->mode, $strategy);
        $correctedRadius = $this->validateRadius($dto->radius, $strategy);
        $correctedLatitude = $this->validateLatitude($dto->latitude, $strategy);
        $correctedLongitude = $this->validateLongitude($dto->longitude, $strategy);
        $correctedTags = $this->validateTags($dto->tags, $strategy);

        // Cohérence : si une coordonnée invalide, nullifier les deux
        if ($correctedLatitude === null || $correctedLongitude === null) {
            $correctedLatitude = null;
            $correctedLongitude = null;
        }

        return new PlaceExplorationFiltersDTO(
            mode: $correctedMode,
            latitude: $correctedLatitude,
            longitude: $correctedLongitude,
            radius: $correctedRadius,
            address: $dto->address,
            tags: $correctedTags,
            featured: $dto->featured
        );
    }

    // ========================================
    // VALIDATIONS (une méthode par filtre)
    // ========================================

    /**
     * @return string Pour THROW: lance exception, pour CORRECT_SILENTLY: string corrigé
     */
    private function validateMode(string $mode, ValidationStrategy $strategy): string
    {
        if (! in_array($mode, PlaceSearchConfig::SEARCH_MODES, true)) {
            if ($strategy === ValidationStrategy::THROW) {
                throw InvalidPlaceFiltersException::invalidMode($mode);
            }

            // CORRECT_SILENTLY : retourner valeur par défaut
            return PlaceSearchConfig::SEARCH_MODE_DEFAULT;
        }

        return $mode;
    }

    /**
     * @return int Pour THROW: lance exception, pour CORRECT_SILENTLY: int corrigé
     */
    private function validateRadius(int $radius, ValidationStrategy $strategy): int
    {
        if ($radius < PlaceSearchConfig::RADIUS_MIN || $radius > PlaceSearchConfig::RADIUS_MAX) {
            if ($strategy === ValidationStrategy::THROW) {
                throw InvalidPlaceFiltersException::invalidRadius(
                    $radius,
                    PlaceSearchConfig::RADIUS_MIN,
                    PlaceSearchConfig::RADIUS_MAX
                );
            }

            // CORRECT_SILENTLY : retourner valeur par défaut
            return PlaceSearchConfig::RADIUS_DEFAULT;
        }

        return $radius;
    }

    /**
     * @return float|null Pour THROW: lance exception, pour CORRECT_SILENTLY: float|null corrigé
     */
    private function validateLatitude(?float $latitude, ValidationStrategy $strategy): ?float
    {
        $min = config('map.coordinates.latitude.min');
        $max = config('map.coordinates.latitude.max');

        if ($latitude !== null && ($latitude < $min || $latitude > $max)) {
            if ($strategy === ValidationStrategy::THROW) {
                throw InvalidPlaceFiltersException::invalidCoordinates('latitude', $latitude);
            }

            // CORRECT_SILENTLY : retourner null
            return null;
        }

        return $latitude;
    }

    /**
     * @return float|null Pour THROW: lance exception, pour CORRECT_SILENTLY: float|null corrigé
     */
    private function validateLongitude(?float $longitude, ValidationStrategy $strategy): ?float
    {
        $min = config('map.coordinates.longitude.min');
        $max = config('map.coordinates.longitude.max');

        if ($longitude !== null && ($longitude < $min || $longitude > $max)) {
            if ($strategy === ValidationStrategy::THROW) {
                throw InvalidPlaceFiltersException::invalidCoordinates('longitude', $longitude);
            }

            // CORRECT_SILENTLY : retourner null
            return null;
        }

        return $longitude;
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<int, string> Pour THROW: lance exception, pour CORRECT_SILENTLY: array corrigé
     */
    private function validateTags(array $tags, ValidationStrategy $strategy): array
    {
        if (empty($tags)) {
            return [];
        }

        try {
            $validSlugs = $this->tagService->validateAndCleanSlugs($tags, app()->getLocale());
            $invalidSlugs = array_diff($tags, $validSlugs);

            if (! empty($invalidSlugs) && $strategy === ValidationStrategy::THROW) {
                throw InvalidPlaceFiltersException::invalidTags($invalidSlugs);
            }

            // Limiter au maximum
            if (count($validSlugs) > PlaceSearchConfig::TAGS_MAX) {
                $validSlugs = array_slice($validSlugs, 0, PlaceSearchConfig::TAGS_MAX);
            }

            return $validSlugs;
        } catch (\Exception $e) {
            // Erreur système (pas erreur utilisateur) : logger
            \Log::error('Tags validation system error', [
                'error' => $e->getMessage(),
                'tags' => $tags,
            ]);

            if ($strategy === ValidationStrategy::THROW) {
                throw $e;
            }

            // CORRECT_SILENTLY : retourner tableau vide
            return [];
        }
    }
}
