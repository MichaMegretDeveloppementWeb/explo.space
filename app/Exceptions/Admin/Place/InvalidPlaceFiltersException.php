<?php

namespace App\Exceptions\Admin\Place;

use Exception;

/**
 * Exception pour erreurs de validation des filtres saisies par l'utilisateur
 *
 * Cette exception indique une ERREUR UTILISATEUR (saisie invalide)
 * et NON une erreur système/programmeur.
 *
 * Le message DOIT TOUJOURS être affiché à l'utilisateur (production incluse).
 */
class InvalidPlaceFiltersException extends Exception
{
    private string $userMessage;

    private string $filterKey;

    public function __construct(string $userMessage, string $filterKey, string $technicalDetails = '')
    {
        $this->userMessage = $userMessage;
        $this->filterKey = $filterKey;

        $fullMessage = $technicalDetails ?: $userMessage;
        parent::__construct($fullMessage);
    }

    /**
     * Message à afficher à l'utilisateur (toujours safe, traduit)
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Clé du filtre invalide (pour logging/debug)
     */
    public function getFilterKey(): string
    {
        return $this->filterKey;
    }

    /**
     * Factory: Rayon invalide
     */
    public static function invalidRadius(int $radius, int $min, int $max): self
    {
        return new self(
            __('errors/exploration.filters.invalid_radius', [
                'radius' => round($radius / 1000),
                'min' => round($min / 1000),
                'max' => round($max / 1000),
            ]),
            'radius',
            "Invalid radius: {$radius}m (min: {$min}m, max: {$max}m)"
        );
    }

    /**
     * Factory: Mode invalide
     */
    public static function invalidMode(string $mode): self
    {
        return new self(
            __('errors/exploration.filters.invalid_mode', ['mode' => $mode]),
            'mode',
            "Invalid search mode: {$mode}"
        );
    }

    /**
     * Factory: Coordonnées invalides (latitude ou longitude spécifique)
     *
     * @param  string  $type  Type de coordonnée ('latitude' ou 'longitude')
     * @param  float  $value  Valeur invalide
     */
    public static function invalidCoordinates(string $type, float $value): self
    {
        $latMin = config('map.coordinates.latitude.min');
        $latMax = config('map.coordinates.latitude.max');
        $lonMin = config('map.coordinates.longitude.min');
        $lonMax = config('map.coordinates.longitude.max');

        $ranges = [
            'latitude' => "{$latMin} à {$latMax}",
            'longitude' => "{$lonMin} à {$lonMax}",
        ];

        $coordinateLabel = __('errors/exploration.filters.coordinate_'.$type);

        return new self(
            __('errors/exploration.filters.invalid_coordinates', [
                'type' => $coordinateLabel,
                'value' => $value,
                'range' => $ranges[$type] ?? 'limites valides',
            ]),
            $type,
            "Invalid {$type}: {$value} (valid range: {$ranges[$type]})"
        );
    }

    /**
     * Factory: Tags invalides
     *
     * @param  array<int, string>  $invalidSlugs  Liste des slugs invalides
     */
    public static function invalidTags(array $invalidSlugs): self
    {
        return new self(
            __('errors/exploration.filters.invalid_tags', [
                'count' => count($invalidSlugs),
                'tags' => implode(', ', array_slice($invalidSlugs, 0, 3)),
            ]),
            'tags',
            'Invalid tag slugs: '.implode(', ', $invalidSlugs)
        );
    }
}
