<?php

namespace App\DTO\Web\Place\Index;

use App\Support\Config\PlaceSearchConfig;

/**
 * Data Transfer Object pour les filtres d'exploration de lieux
 *
 * Ce DTO garantit la cohérence du mapping entre :
 * - Les paramètres URL (format court : lat, lng)
 * - Les données des composants Livewire (format long : latitude, longitude)
 * - Le service de validation
 *
 * Utilisation :
 * - fromUrlParams() : Créer depuis URL (?lat=48.8&lng=2.3)
 * - fromComponentData() : Créer depuis composants Livewire
 * - toUrlParams() : Convertir vers format URL court
 * - toComponentData() : Convertir vers format composant long
 */
class PlaceExplorationFiltersDTO
{
    /**
     * @param  string  $mode  Mode de recherche : 'proximity' | 'worldwide'
     * @param  float|null  $latitude  Latitude WGS84 (limites : config('map.coordinates.latitude'))
     * @param  float|null  $longitude  Longitude WGS84 (limites : config('map.coordinates.longitude'))
     * @param  int  $radius  Rayon de recherche en mètres (200km à 1500km)
     * @param  string|null  $address  Adresse textuelle
     * @param  array<int, string>  $tags  Slugs des tags sélectionnés
     * @param  bool  $featured  Afficher uniquement les lieux emblématiques
     * @param  int  $page  Numéro de page pour pagination
     */
    public function __construct(
        public readonly string $mode,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly int $radius,
        public readonly ?string $address,
        public readonly array $tags,
        public readonly bool $featured = false,
        public readonly int $page = 1,
    ) {}

    /**
     * Créer depuis les paramètres URL (format court : lat, lng)
     *
     * Utilisé par PlaceExplorer::mount() pour lire les query params
     *
     * @param  array<string, mixed>  $urlParams
     */
    public static function fromUrlParams(array $urlParams): self
    {
        return new self(
            mode: $urlParams['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
            latitude: isset($urlParams['lat']) ? (float) $urlParams['lat'] : null,
            longitude: isset($urlParams['lng']) ? (float) $urlParams['lng'] : null,
            radius: isset($urlParams['radius']) ? (int) $urlParams['radius'] : PlaceSearchConfig::RADIUS_DEFAULT,
            address: $urlParams['address'] ?? null,
            tags: self::parseTags($urlParams['tags'] ?? []),
            featured: isset($urlParams['featured']) ? filter_var($urlParams['featured'], FILTER_VALIDATE_BOOLEAN) : false,
            page: isset($urlParams['page']) ? (int) $urlParams['page'] : PlaceSearchConfig::PAGE_DEFAULT,
        );
    }

    /**
     * Créer depuis les propriétés du composant (format long : latitude, longitude)
     *
     * Utilisé par PlaceFilters::emitFiltersChanged() et PlaceExplorer::syncUrlParams()
     *
     * @param  array<string, mixed>  $componentData
     */
    public static function fromComponentData(array $componentData): self
    {
        return new self(
            mode: $componentData['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
            latitude: $componentData['latitude'] ?? null,
            longitude: $componentData['longitude'] ?? null,
            radius: $componentData['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT,
            address: $componentData['address'] ?? null,
            tags: self::parseTags($componentData['tags'] ?? []),
            featured: $componentData['featured'] ?? false,
            page: $componentData['page'] ?? PlaceSearchConfig::PAGE_DEFAULT,
        );
    }

    /**
     * Convertir vers format URL (court : lat, lng)
     *
     * Utilisé pour mettre à jour les propriétés #[Url] de PlaceExplorer
     *
     * @return array<string, mixed>
     */
    public function toUrlParams(): array
    {
        return [
            'mode' => $this->mode,
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'radius' => $this->radius,
            'address' => $this->address,
            'tags' => $this->tags,
            'featured' => $this->featured,
            'page' => $this->page,
        ];
    }

    /**
     * Convertir vers format composant (long : latitude, longitude)
     *
     * Utilisé pour passer les données aux composants enfants (PlaceList, PlaceMap, PlaceFilters)
     *
     * @return array<string, mixed>
     */
    public function toComponentData(): array
    {
        return [
            'mode' => $this->mode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'address' => $this->address,
            'tags' => $this->tags,
            'featured' => $this->featured,
            'page' => $this->page,
        ];
    }

    /**
     * Parser les tags depuis différents formats
     *
     * Accepte :
     * - String : "tag1,tag2,tag3"
     * - Array : ["tag1", "tag2", "tag3"]
     *
     * @return array<int, string>
     */
    private static function parseTags(mixed $tags): array
    {
        if (is_string($tags)) {
            $parsed = array_filter(explode(',', $tags));

            return array_values(array_map('trim', $parsed));
        }

        if (is_array($tags)) {
            return array_values(array_filter(array_map('trim', $tags)));
        }

        return [];
    }
}
