<?php

namespace App\Livewire\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Enums\ValidationStrategy;
use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use App\Services\Web\Place\Index\PlaceExplorationFiltersValidationService;
use App\Services\Web\Place\Index\PlaceExplorationService;
use App\Support\Config\PlaceSearchConfig;
use Livewire\Attributes\On;
use Livewire\Component;

class PlaceMap extends Component
{
    // ========================================
    // PROPRIÉTÉS
    // ========================================

    /**
     * Filtres actuels reçus de PlaceFilters via événement filters-changed
     *
     * SÉCURITÉ :
     * - #[Locked] empêche toute modification directe depuis le client
     * - Re-validés dans onFiltersChanged() pour défense en profondeur
     *
     * @var array<string, mixed>
     */
    #[\Livewire\Attributes\Locked]
    public array $currentFilters = [];

    /** @var array<string, float>|null Bounding box du viewport actuel de la carte */
    public ?array $boundingBox = null;

    /** @var array<int, array{id: int, latitude: float, longitude: float}> */
    public array $coordinates = [];

    public int $visibleCount = 0;

    /**
     * Indique si le système de bounding box dynamique est activé
     *
     * Si true : Chargement progressif selon zone visible (optimisé pour > 1000 lieux)
     * Si false : Chargement complet une fois (optimisé pour < 1000 lieux)
     */
    #[\Livewire\Attributes\Locked]
    public bool $useBoundingBox;

    /**
     * Snapshot des filtres précédents pour détecter les changements
     * et dispatcher les ajustements de vue de carte appropriés
     *
     * NOTE: Doit être public pour être persisté entre les requêtes Livewire
     *
     * @var array{mode: string, latitude: float|null, longitude: float|null, radius: int, tags: array<int, string>}
     */
    public array $previousFilters = [
        'mode' => PlaceSearchConfig::SEARCH_MODE_DEFAULT,
        'latitude' => null,
        'longitude' => null,
        'radius' => PlaceSearchConfig::RADIUS_DEFAULT,
        'tags' => [],
    ];

    // ========================================
    // LIFECYCLE METHODS
    // ========================================

    /**
     * @param  array<string, mixed>  $initialFilters
     */
    public function mount(array $initialFilters = []): void
    {
        $this->currentFilters = $initialFilters;

        // Initialiser previousFilters avec l'état initial
        $this->previousFilters = [
            'mode' => $initialFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
            'latitude' => $initialFilters['latitude'] ?? null,
            'longitude' => $initialFilters['longitude'] ?? null,
            'radius' => $initialFilters['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT,
            'tags' => $initialFilters['tags'] ?? [],
        ];

        // Lire configuration du système de bounding box
        $this->useBoundingBox = config('map.use_bounding_box', false);

        if ($this->useBoundingBox) {
            // Mode A : Bounding box dynamique ACTIVÉ
            // Attendre que JavaScript envoie la vraie bounding box après init carte
            $this->boundingBox = null;
            $this->coordinates = [];
            $this->visibleCount = 0;

        } else {
            // Mode B : Bounding box dynamique DÉSACTIVÉ
            // Charger TOUTES les coordonnées selon filtres immédiatement
            $this->boundingBox = null; // Non utilisé en mode B
            $this->loadCoordinates();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.web.place.index.place-map', [
            'coordinates' => $this->coordinates,
            'visibleCount' => $this->visibleCount,
        ]);
    }

    // ========================================
    // FILTER SYNCHRONIZATION
    // ========================================

    /**
     * Écouter les changements de filtres depuis PlaceFilters
     *
     * WORKFLOW :
     * 1. Recevoir et valider les nouveaux filtres
     * 2. Déterminer l'action de vue nécessaire (center-on-location / show-world-view / no-change)
     * 3. Charger les coordonnées si useBoundingBox = false
     * 4. Émettre 'sync-filters-view' vers JavaScript avec action + filtres + coordonnées
     *
     * JavaScript se chargera de :
     * - Appliquer l'action de vue
     * - Recalculer la boundingBox après stabilisation
     * - Notifier PlaceList avec boundingBox + filtres synchronisés
     *
     * @param  array<string, mixed>  $filters
     */
    #[On('filters-updated')]
    public function onFiltersUpdated(array $filters): void
    {
        $this->resetErrorBag();

        $dto = PlaceExplorationFiltersDTO::fromComponentData($filters);

        try {
            $validationService = app(PlaceExplorationFiltersValidationService::class);
            $validatedDto = $validationService->validate($dto, ValidationStrategy::THROW);

            $validatedData = $validatedDto->toComponentData();
            $this->currentFilters = $validatedData;

            // Déterminer l'action de vue nécessaire (compare avec previousFilters)
            $viewAction = $this->determineViewAction($validatedData);

            // ⚠️ IMPORTANT : Mettre à jour previousFilters APRÈS avoir déterminé l'action
            $this->previousFilters = [
                'mode' => $validatedData['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
                'latitude' => $validatedData['latitude'] ?? null,
                'longitude' => $validatedData['longitude'] ?? null,
                'radius' => $validatedData['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT,
                'tags' => $validatedData['tags'] ?? [],
            ];

            // Charger les coordonnées si mode statique
            $coordinates = null;
            if (! $this->useBoundingBox) {
                $this->loadCoordinates();
                $coordinates = $this->coordinates;
            }

            // Émettre vers JavaScript pour synchronisation
            $this->dispatch('sync-filters-view', [
                'viewAction' => $viewAction,
                'filters' => $this->currentFilters,
                'coordinates' => $coordinates,
                'useBoundingBox' => $this->useBoundingBox,
            ]);

        } catch (InvalidPlaceFiltersException $e) {
            $this->addError('filters_validation', $e->getUserMessage());
        }

        $this->skipRender();
    }

    /**
     * Déterminer l'action de vue à appliquer selon les filtres
     *
     * Scénarios gérés :
     * 1. Proximity → Worldwide : show-world-view
     * 2. Worldwide → Proximity sans adresse : no-change (attendre saisie)
     * 3. Nouvelles coordonnées : center-on-location
     * 4. SEUL le rayon change : adjust-zoom
     * 5. Changement tags uniquement : no-change
     *
     * @param  array<string, mixed>  $filters
     * @return array{action: string, latitude?: float, longitude?: float, radius?: int}
     */
    private function determineViewAction(array $filters): array
    {
        // Si previousFilters est vide (premier appel), retourner no-change
        if (empty($this->previousFilters)) {
            return ['action' => 'no-change'];
        }

        $previous = $this->previousFilters;
        $new = $filters;

        // Scénario 1 : Changement mode "proximity" → "worldwide"
        if ($previous['mode'] === 'proximity' && $new['mode'] === 'worldwide') {
            return ['action' => 'show-world-view'];
        }

        // Scénario 2 : Changement mode "worldwide" → "proximity" sans adresse
        if ($previous['mode'] === 'worldwide' && $new['mode'] === 'proximity' && empty($new['latitude'])) {
            return ['action' => 'no-change'];
        }

        // Scénario 3 : Nouvelles coordonnées (adresse saisie ou géolocalisation)
        if ($this->hasCoordinatesChanged($previous, $new) && ! empty($new['latitude']) && ! empty($new['longitude'])) {
            return [
                'action' => 'center-on-location',
                'latitude' => (float) $new['latitude'],
                'longitude' => (float) $new['longitude'],
                'radius' => (int) ($new['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT),
            ];
        }

        // Scénario 4 : Changement de rayon UNIQUEMENT (coordonnées inchangées)
        if ($this->hasOnlyRadiusChanged($previous, $new) && ! empty($new['latitude'])) {
            return [
                'action' => 'adjust-zoom',
                'latitude' => (float) $new['latitude'],
                'longitude' => (float) $new['longitude'],
                'radius' => (int) $new['radius'],
            ];
        }

        // Scénario 5 : Changement de tags uniquement (ou aucun changement significatif)
        return ['action' => 'no-change'];
    }

    /**
     * Détecte si les coordonnées ont changé
     *
     * @param  array<string, mixed>  $previous
     * @param  array<string, mixed>  $new
     */
    private function hasCoordinatesChanged(array $previous, array $new): bool
    {
        // Comparer avec précision de 6 décimales (environ 10cm)
        $prevLat = isset($previous['latitude']) ? round((float) $previous['latitude'], 6) : null;
        $prevLng = isset($previous['longitude']) ? round((float) $previous['longitude'], 6) : null;
        $newLat = isset($new['latitude']) ? round((float) $new['latitude'], 6) : null;
        $newLng = isset($new['longitude']) ? round((float) $new['longitude'], 6) : null;

        return $prevLat !== $newLat || $prevLng !== $newLng;
    }

    /**
     * Détecte si SEUL le rayon a changé (mode, coordonnées, tags inchangés)
     *
     * @param  array<string, mixed>  $previous
     * @param  array<string, mixed>  $new
     */
    private function hasOnlyRadiusChanged(array $previous, array $new): bool
    {
        // Le rayon a-t-il changé ?
        if (($previous['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT) === ($new['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT)) {
            return false;
        }

        // Le mode est-il inchangé ?
        if (($previous['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT) !== ($new['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT)) {
            return false;
        }

        // Les coordonnées sont-elles inchangées ?
        if ($this->hasCoordinatesChanged($previous, $new)) {
            return false;
        }

        // Les tags sont-ils inchangés ?
        $prevTags = $previous['tags'] ?? [];
        $newTags = $new['tags'] ?? [];
        sort($prevTags);
        sort($newTags);
        if ($prevTags !== $newTags) {
            return false;
        }

        // Si on arrive ici : SEUL le rayon a changé
        return true;
    }

    // ========================================
    // PUBLIC METHODS (appelables depuis JS)
    // ========================================

    /**
     * Événement initial envoyé par JavaScript après initialisation carte
     *
     * Utilisé uniquement si useBoundingBox === true
     * JavaScript envoie la vraie bounding box calculée par Leaflet
     *
     * @param  array<string, float>  $boundingBox
     */
    #[On('initial-map-bounds')]
    public function onInitialMapBounds(array $boundingBox): void
    {

        if (! $this->useBoundingBox) {
            // Mode bounding box désactivé, ignorer cet événement
            return;
        }

        $this->boundingBox = $boundingBox;
        $this->loadCoordinates();
        $this->dispatch('coordinates-updated', coordinates: $this->coordinates);

        // Éviter le re-render : le HTML ne change jamais, seuls les événements JS sont nécessaires
        $this->skipRender();
    }

    /**
     * Mettre à jour le bounding box de la carte (appelé par JS via événement)
     *
     * DÉFENSE EN PROFONDEUR :
     * - Re-valide les filtres reçus (même si déjà validés dans PlaceMap.onFiltersUpdated)
     * - Protège contre événements falsifiés ou race conditions
     *
     * @param  array<string, float>  $boundingBox  ['north' => float, 'south' => float, 'east' => float, 'west' => float]
     * @param  array<string, mixed>  $filters
     */
    #[On('update-map-bounds')]
    public function onUpdateMapBounds(array $boundingBox, array $filters = []): void
    {
        if (! $this->useBoundingBox) {
            return;
        }

        // Si pas de filtres, utiliser les actuels
        if (empty($filters)) {
            $filters = $this->currentFilters;
        }

        $this->resetErrorBag();
        $dto = PlaceExplorationFiltersDTO::fromComponentData($filters);

        try {
            $validationService = app(PlaceExplorationFiltersValidationService::class);
            $validatedDto = $validationService->validate($dto, ValidationStrategy::THROW);

            $this->currentFilters = $validatedDto->toComponentData();
            $this->boundingBox = $boundingBox;

            $this->loadCoordinates();
            $this->dispatch('coordinates-updated', coordinates: $this->coordinates);

        } catch (InvalidPlaceFiltersException $e) {
            $this->addError('filters_validation', $e->getUserMessage());
            $this->coordinates = [];
        }

        $this->skipRender();
    }

    // ========================================
    // DATA LOADING
    // ========================================

    /**
     * Charger les coordonnées selon filtres + bounding box
     *
     * Utilisé uniquement si useBoundingBox === true
     * CRITIQUE : Utilise bounding box pour limiter la taille du résultat (100K+ lieux)
     */
    private function loadCoordinates(): void
    {
        if (! $this->useBoundingBox) {
            $this->boundingBox = null;
        }

        try {
            // Préparer les filtres pour le service
            $filters = [
                'mode' => $this->currentFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
                'latitude' => $this->currentFilters['latitude'] ?? null,
                'longitude' => $this->currentFilters['longitude'] ?? null,
                'radius' => $this->currentFilters['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT,
                'tags' => $this->currentFilters['tags'] ?? [],
            ];

            // Appeler le service avec bounding box
            $data = app(PlaceExplorationService::class)->getPlacesForMap($filters, $this->boundingBox);

            // Convertir Collection vers array
            $this->coordinates = $data['coordinates']->toArray();
            $this->visibleCount = $data['count'];

            // On laisse le choix à la méthode appelante d'ajouter le dispatch('coordinates-updated') ou non

        } catch (\Exception $e) {
            // ERREUR TECHNIQUE (Eloquent, DB, etc.) : logger avec détails complets
            \Log::error('Technical error loading coordinates with bounding box', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'filters' => $this->currentFilters,
                'bounding_box' => $this->boundingBox,
            ]);

            // Message GÉNÉRIQUE à l'utilisateur
            $userMessage = __('errors/exploration.map.system_error');

            if (config('app.debug')) {
                $userMessage .= ' - '.$e->getMessage();
            }

            $this->addError('coordinates_loading', $userMessage);

            $this->coordinates = [];
            $this->visibleCount = 0;
        }
    }
}
