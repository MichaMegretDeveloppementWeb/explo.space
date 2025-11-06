<?php

namespace App\Support\Config;

/**
 * Configuration centralisée de la recherche de lieux
 *
 * IMPORTANT : Ces valeurs sont utilisées à la fois côté backend (validation)
 * et frontend (UI ranges). Toute modification ici se propage automatiquement.
 *
 * Utilisation :
 * - PlaceSearchConfig::RADIUS_MIN
 * - PlaceSearchConfig::RADIUS_DEFAULT
 * - PlaceSearchConfig::metersToKm($radius)
 * - PlaceSearchConfig::getJsConfig() pour passer au frontend
 */
class PlaceSearchConfig
{
    // ========================================
    // RAYON DE RECHERCHE
    // ========================================

    /**
     * Rayon minimum de recherche en mètres (200km)
     *
     * Note : Utilisation de l'underscore pour lisibilité (PHP 7.4+)
     * 200_000 = 200 000 mètres = 200 km
     */
    public const RADIUS_MIN = 200_000;

    /**
     * Rayon maximum de recherche en mètres (1500km)
     *
     * Limitation pour des raisons de performance et de pertinence
     * des résultats de recherche "autour de moi"
     */
    public const RADIUS_MAX = 2_500_000;

    /**
     * Rayon par défaut en mètres (200km)
     *
     * Valeur initiale du slider de rayon dans l'interface utilisateur
     */
    public const RADIUS_DEFAULT = 200_000;

    /**
     * Pas d'incrémentation du slider en mètres (10km)
     *
     * Utilisé pour le frontend (attribut step du range input)
     * Permet des incréments de 10km
     */
    public const RADIUS_STEP = 10_000;

    // ========================================
    // MODES DE RECHERCHE
    // ========================================

    /**
     * Modes de recherche autorisés
     *
     * - proximity : Recherche autour d'un point avec rayon
     * - worldwide : Recherche mondiale (tous les lieux)
     *
     * @var array<int, string>
     */
    public const SEARCH_MODES = ['proximity', 'worldwide'];

    /**
     * Mode de recherche par défaut
     */
    public const SEARCH_MODE_DEFAULT = 'proximity';

    // ========================================
    // TAGS
    // ========================================

    /**
     * Nombre maximum de tags sélectionnables simultanément
     *
     * Limitation pour :
     * - Éviter surcharge des requêtes SQL
     * - Maintenir une UX cohérente
     * - Éviter URL trop longues
     */
    public const TAGS_MAX = 10;

    // ========================================
    // PAGINATION
    // ========================================

    /**
     * Nombre d'items par page (liste des lieux)
     */
    public const ITEMS_PER_PAGE = 30;

    /**
     * Numéro de page par défaut
     */
    public const PAGE_DEFAULT = 1;

    // ========================================
    // LIMITES SYSTÈME
    // ========================================

    /**
     * Nombre maximum de coordonnées chargées pour la carte
     *
     * Limite de sécurité pour éviter crash serveur/navigateur avec de très
     * grandes bases de données (>100K lieux). Le clustering frontend gérera
     * l'affichage de ces marqueurs.
     *
     * Note : Cette limite est volontairement élevée pour éviter les incohérences
     * entre carte et liste. Les filtres (tags, rayon) limitent déjà le nombre
     * de résultats en pratique.
     */
    public const MAX_MAP_COORDINATES = 100_000;

    /**
     * Nombre maximum de résultats affichés dans la recherche de tags
     *
     * Utilisé dans PlaceFilters pour limiter les suggestions de tags
     * lors de la saisie de recherche (performance frontend).
     */
    public const TAG_SEARCH_LIMIT = 10;

    /**
     * Durée du cache des requêtes en secondes (5 minutes)
     *
     * TTL appliqué aux résultats de PlaceExplorationService pour réduire
     * la charge sur la base de données. Les requêtes identiques (mêmes filtres
     * + même bounding box) seront servies depuis le cache.
     */
    public const CACHE_TTL = 300;

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Convertir mètres en kilomètres pour affichage
     *
     * @param  int  $meters  Valeur en mètres
     * @return int Valeur en kilomètres
     */
    public static function metersToKm(int $meters): int
    {
        return (int) ($meters / 1000);
    }

    /**
     * Convertir kilomètres en mètres pour stockage
     *
     * @param  int  $km  Valeur en kilomètres
     * @return int Valeur en mètres
     */
    public static function kmToMeters(int $km): int
    {
        return $km * 1000;
    }

    /**
     * Obtenir la configuration complète pour le frontend JavaScript
     *
     * Retourne un array avec toutes les valeurs nécessaires côté client,
     * incluant les conversions km/mètres pour faciliter l'affichage et
     * les limites de coordonnées synchronisées depuis config/map.php.
     *
     * Usage dans Blade :
     * <script>
     *     window.PlaceSearchConfig = @json(App\Support\Config\PlaceSearchConfig::getJsConfig());
     * </script>
     *
     * Usage en JavaScript :
     * const config = window.PlaceSearchConfig;
     * console.log(config.radius.minKm); // 200
     * console.log(config.map.coordinates.latitude.max); // 85
     * console.log(config.map.boundingBox.north); // 85
     *
     * @return array<string, mixed>
     */
    public static function getJsConfig(): array
    {
        return [
            'radius' => [
                // Valeurs en mètres (pour logique backend-compatible)
                'min' => self::RADIUS_MIN,
                'max' => self::RADIUS_MAX,
                'default' => self::RADIUS_DEFAULT,
                'step' => self::RADIUS_STEP,

                // Valeurs en kilomètres (pour affichage utilisateur)
                'minKm' => self::metersToKm(self::RADIUS_MIN),
                'maxKm' => self::metersToKm(self::RADIUS_MAX),
                'defaultKm' => self::metersToKm(self::RADIUS_DEFAULT),
                'stepKm' => self::metersToKm(self::RADIUS_STEP),
            ],
            'modes' => [
                'allowed' => self::SEARCH_MODES,
                'default' => self::SEARCH_MODE_DEFAULT,
            ],
            'tags' => [
                'max' => self::TAGS_MAX,
            ],
            'pagination' => [
                'itemsPerPage' => self::ITEMS_PER_PAGE,
            ],
            'map' => [
                // Limites de coordonnées (synchronisées depuis config/map.php)
                'coordinates' => config('map.coordinates'),
                // Bounding box par défaut (monde entier)
                'boundingBox' => config('map.default_bounding_box'),
                // Utilisation de la bounding box dynamique
                'useBoundingBox' => config('map.use_bounding_box'),
            ],
        ];
    }
}
