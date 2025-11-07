<?php

namespace App\Livewire\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Enums\ValidationStrategy;
use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use App\Services\Web\Place\Index\PlaceExplorationFiltersValidationService;
use App\Services\Web\Place\Index\PlaceExplorationService;
use App\Support\Config\PlaceSearchConfig;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PlaceList extends Component
{
    use WithPagination;

    // ========================================
    // PROPRIÉTÉS
    // ========================================

    /**
     * Filtres actuels reçus de PlaceFilters via événement filters-changed
     *
     * SÉCURITÉ :
     * - #[Locked] empêche toute modification directe depuis le client
     * - Re-validés dans onFiltersChanged() pour défense en profondeur
     *   contre événements falsifiés ou bugs dans PlaceFilters
     *
     * @var array<string, mixed>
     */
    #[\Livewire\Attributes\Locked]
    public array $currentFilters = [];

    /**
     * Bounding box actuelle de la carte (viewport visible)
     * Utilisée pour filtrer la liste par les lieux visibles sur la carte
     *
     * @var array<string, float>|null ['north' => float, 'south' => float, 'east' => float, 'west' => float]
     */
    public ?array $currentBoundingBox = null;

    /**
     * Places sous forme de tableaux (évite problèmes sérialisation Livewire)
     * Grandit avec le scroll infini en ajoutant 20 nouveaux lieux à chaque fois.
     * Tableaux nativement sérialisables = zéro perte de données entre requêtes.
     *
     * Structure : ['id', 'latitude', 'longitude', 'address', 'distance', 'translation', 'main_photo', 'tags']
     *
     * @var array<int, array<string, mixed>>
     */
    public array $places = [];

    /**
     * Cursor pour la pagination suivante
     * Encodé en base64 par Laravel CursorPaginator
     */
    public ?string $nextCursor = null;

    /**
     * Indicateur s'il reste des pages à charger
     */
    public bool $hasMorePages = true;

    // ========================================
    // LIFECYCLE METHODS
    // ========================================

    /**
     * @param  array<string, mixed>  $initialFilters
     */
    public function mount(array $initialFilters = []): void
    {
        $this->currentFilters = $initialFilters;

        // Initialiser les tableaux vides pour le scroll infini
        $this->places = [];
        $this->nextCursor = null;
        $this->hasMorePages = true;

        // Liste vide au départ, attend la boundingBox initiale de la carte
        // (évite de charger tous les lieux avant de connaître le viewport)
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.web.place.index.place-list', [
            'places' => $this->places,
            'hasBoundingBox' => $this->currentBoundingBox !== null,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    // ========================================
    // EVENT LISTENERS
    // ========================================

    /**
     * Écouter la boundingBox initiale de la carte (une seule fois au chargement)
     *
     * Événement émis par index.js après que la carte soit complètement prête.
     * Déclenche le premier chargement de la liste filtrée par viewport.
     *
     * @param  array<string, float>  $boundingBox  ['north', 'south', 'east', 'west']
     */
    #[On('initial-list-bounds')]
    public function onInitialListBounds(array $boundingBox): void
    {
        $this->currentBoundingBox = $boundingBox;

        // Charger la première page de résultats
        $this->loadPlaces();
    }

    /**
     * Écouter les changements de boundingBox depuis JavaScript
     *
     * DÉFENSE EN PROFONDEUR :
     * - Re-valide les filtres reçus (même si déjà validés dans PlaceMap)
     * - Protège contre événements falsifiés ou race conditions
     * - Reset la pagination et charge les lieux avec filtres + boundingBox synchronisés
     *
     * @param  array<string, float>  $boundingBox  ['north', 'south', 'east', 'west']
     * @param  array<string, mixed>  $filters
     */
    #[On('update-list-bounds')]
    public function onUpdateListBounds(array $boundingBox, array $filters = []): void
    {
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
            $this->currentBoundingBox = $boundingBox;

            $this->loadPlaces();

        } catch (InvalidPlaceFiltersException $e) {
            $this->addError('filters_validation', $e->getUserMessage());
            $this->places = [];
            $this->hasMorePages = false;
        }

        // 🔑 Signaler à JavaScript que la mise à jour est terminée
        // Permet de débloquer les éventuelles mises à jour en attente
        $this->dispatch('list-update-complete');
    }

    // ========================================
    // DATA LOADING
    // ========================================

    /**
     * Charger les lieux dans la boundingBox actuelle (viewport visible de la carte)
     *
     * Chargement initial avec cursor pagination pour scroll infini.
     * Reset la collection et charge la première page (20 résultats).
     * Filtre TOUJOURS par boundingBox pour cohérence carte ↔ liste.
     */
    private function loadPlaces(): void
    {
        try {
            // Vérifier que boundingBox existe
            if ($this->currentBoundingBox === null) {
                return;
            }

            // Reset pour nouveau chargement
            $this->places = [];
            $this->nextCursor = null;
            $this->hasMorePages = true;

            // Préparer les filtres pour le service
            $filters = [
                'mode' => $this->currentFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
                'latitude' => $this->currentFilters['latitude'] ?? null,
                'longitude' => $this->currentFilters['longitude'] ?? null,
                'radius' => $this->currentFilters['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT,
                'tags' => $this->currentFilters['tags'] ?? [],
                'featured' => $this->currentFilters['featured'] ?? false,
            ];

            // Charger les lieux sous forme de tableaux (20 résultats)
            $result = app(PlaceExplorationService::class)
                ->getPlacesForList($filters, $this->currentBoundingBox, 20);

            // Stocker directement les tableaux (évite problèmes sérialisation Livewire)
            $this->places = $result['places'];
            $this->nextCursor = $result['nextCursor'];
            $this->hasMorePages = $result['hasMorePages'];

            // Dispatch événement pour reset du scroll (UX : remettre en haut après rechargement)
            $this->dispatch('list-reset');

        } catch (\Exception $e) {
            // ERREUR TECHNIQUE (Eloquent, DB, etc.) : logger avec détails complets
            \Log::error('Technical error loading places in bounding box (PlaceList)', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'filters' => $this->currentFilters,
                'bounding_box' => $this->currentBoundingBox,
            ]);

            // Message GÉNÉRIQUE à l'utilisateur
            $userMessage = __('errors/exploration.list.system_error');

            if (config('app.debug')) {
                $userMessage .= ' - '.$e->getMessage();
            }

            $this->addError('places_loading', $userMessage);
        }
    }

    /**
     * Charger la page suivante (scroll infini)
     *
     * Appelé automatiquement par l'Intersection Observer frontend.
     * Ajoute les nouveaux lieux à la collection existante.
     */
    public function loadMore(): void
    {
        // Vérifications de sécurité
        if (! $this->hasMorePages || $this->nextCursor === null) {
            return;
        }

        try {
            // Préparer les filtres
            $filters = [
                'mode' => $this->currentFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT,
                'latitude' => $this->currentFilters['latitude'] ?? null,
                'longitude' => $this->currentFilters['longitude'] ?? null,
                'radius' => $this->currentFilters['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT,
                'tags' => $this->currentFilters['tags'] ?? [],
                'featured' => $this->currentFilters['featured'] ?? false,
            ];

            // Charger la page suivante sous forme de tableaux (20 nouveaux lieux)
            // IMPORTANT : Passer le nextCursor pour obtenir la page suivante (pas la première à nouveau)
            $result = app(PlaceExplorationService::class)
                ->getPlacesForList($filters, $this->currentBoundingBox, 20, $this->nextCursor);

            // AJOUTER les 20 nouveaux lieux (ne pas remplacer les anciens)
            $this->places = array_merge($this->places, $result['places']);

            // Mettre à jour le cursor et l'état
            $this->nextCursor = $result['nextCursor'];
            $this->hasMorePages = $result['hasMorePages'];

        } catch (\Exception $e) {
            \Log::error('Technical error loading more places (PlaceList)', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'cursor' => $this->nextCursor,
                'filters' => $this->currentFilters,
            ]);

            $this->addError('places_loading', __('errors/exploration.list.system_error'));
        }
    }

    /**
     * Fermer le message d'erreur de validation
     */
    public function dismissValidationError(): void
    {
        $this->resetErrorBag('filters_validation');
    }

    // ========================================
    // EMPTY STATES HELPERS
    // ========================================

    /**
     * Vérifier si les conditions minimales pour lancer une recherche sont réunies
     *
     * Mode proximity : nécessite latitude + longitude (adresse saisie ou géolocalisation)
     * Mode worldwide : nécessite au moins 1 tag sélectionné
     */
    #[Computed]
    public function isMinimalConditionsMet(): bool
    {
        $mode = $this->currentFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT;

        // Mode proximity : nécessite latitude + longitude
        if ($mode === 'proximity') {
            return ! empty($this->currentFilters['latitude']) && ! empty($this->currentFilters['longitude']);
        }

        // Mode worldwide : nécessite au moins 1 tag OU featured=true
        if ($mode === 'worldwide') {
            $hasTags = ! empty($this->currentFilters['tags']) && count($this->currentFilters['tags']) > 0;
            $hasFeatured = ! empty($this->currentFilters['featured']);

            return $hasTags || $hasFeatured;
        }

        return false;
    }

    /**
     * Obtenir le message d'incitation à l'action (État 1 : conditions minimales NON réunies)
     */
    public function getStartSearchMessage(): string
    {
        $mode = $this->currentFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT;

        if ($mode === 'proximity') {
            return __('web/pages/explore.livewire.empty_state_proximity_no_address');
        }

        return __('web/pages/explore.livewire.empty_state_worldwide_no_tags');
    }

    /**
     * Obtenir le message "aucun résultat" (État 2 : recherche effectuée SANS résultats)
     */
    public function getNoResultsMessage(): string
    {
        $mode = $this->currentFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT;

        if ($mode === 'proximity') {
            return __('web/pages/explore.livewire.no_results_proximity_zone');
        }

        return __('web/pages/explore.livewire.no_results_worldwide_tags');
    }
}
