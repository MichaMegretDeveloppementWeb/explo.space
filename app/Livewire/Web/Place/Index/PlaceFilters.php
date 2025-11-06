<?php

namespace App\Livewire\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Enums\ValidationStrategy;
use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use App\Livewire\Web\Place\Index\Traits\HandlesAddressAutocomplete;
use App\Livewire\Web\Place\Index\Traits\HandlesGeolocation;
use App\Livewire\Web\Place\Index\Traits\HandlesRadius;
use App\Livewire\Web\Place\Index\Traits\HandlesSearchModes;
use App\Livewire\Web\Place\Index\Traits\HandlesTagFiltering;
use App\Services\Web\Place\Index\PlaceExplorationFiltersValidationService;
use App\Support\Config\PlaceSearchConfig;
use Livewire\Component;

class PlaceFilters extends Component
{
    use HandlesAddressAutocomplete;
    use HandlesGeolocation;
    use HandlesRadius;
    // ========================================
    // TRAITS
    // ========================================

    use HandlesSearchModes;
    use HandlesTagFiltering;

    // ========================================
    // PROPRIÉTÉS DE FILTRES (reçues du parent PlaceExplorer)
    // ========================================
    // Note: Ces propriétés ne sont PAS synchronisées avec l'URL directement.
    // PlaceExplorer est le seul responsable de la gestion des paramètres URL.
    // PlaceFilters reçoit les valeurs via mount($initialFilters) et émet
    // l'événement 'filters-changed' quand elles changent.

    public string $searchMode = PlaceSearchConfig::SEARCH_MODE_DEFAULT;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public int $radius = PlaceSearchConfig::RADIUS_DEFAULT;

    public ?string $address = null;

    public string $selectedTagsSlugs = '';

    // ========================================
    // PROPRIÉTÉS UI - ADDRESS AUTOCOMPLETE
    // ========================================

    /** @var array<int, mixed> */
    public array $addressSuggestions = [];

    public bool $addressSearchLoading = false;

    // ========================================
    // PROPRIÉTÉS UI - GEOLOCATION
    // ========================================

    public bool $geolocLoading = false;

    // ========================================
    // PROPRIÉTÉS UI - TAG FILTERING
    // ========================================

    public string $tagSearchQuery = '';

    /** @var array<int, array{slug: string, name: string}> */
    public array $availableTags = [];

    /** @var array<int, array{slug: string, name: string}> */
    public array $filteredTags = [];

    public bool $tagsLoading = false;

    // Mobile tag selector
    public bool $showMobileTagSelector = false;

    /** @var array<int, array{slug: string, name: string}> */
    public array $selectedTags = [];

    // ========================================
    // PROPRIÉTÉS UI - FILTERS COLLAPSE
    // ========================================

    public bool $filtersCollapsed = false;

    // ========================================
    // LIFECYCLE METHODS
    // ========================================

    /**
     * @param  array<string, mixed>  $initialFilters
     */
    public function mount(array $initialFilters = []): void
    {
        // Initialiser les filtres depuis le parent
        if (! empty($initialFilters)) {
            $this->searchMode = $initialFilters['mode'] ?? PlaceSearchConfig::SEARCH_MODE_DEFAULT;
            $this->latitude = $initialFilters['latitude'] ?? null;
            $this->longitude = $initialFilters['longitude'] ?? null;
            $this->radius = $initialFilters['radius'] ?? PlaceSearchConfig::RADIUS_DEFAULT;
            $this->address = $initialFilters['address'] ?? null;

            // Convertir le tableau de tags en string
            $tags = $initialFilters['tags'] ?? [];
            $this->selectedTagsSlugs = is_array($tags) ? implode(',', $tags) : '';
        }

        // Initialiser le système de tags
        $this->initializeTags();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.web.place.index.place-filters');
    }

    // ========================================
    // EVENT EMISSION
    // ========================================

    /**
     * Émettre l'événement filters-changed avec tous les filtres actuels
     *
     * VALIDATION :
     * - Utilise ValidationStrategy::THROW
     * - Si erreur : affiche via addError() + NE PAS émettre
     * - Si succès : émet l'événement
     *
     * Note: Appelée depuis les traits (HandlesSearchModes, etc.) - PHPStan ne détecte pas cet usage
     */
    private function emitFiltersChanged(): void
    {
        // Réinitialiser les erreurs
        $this->resetErrorBag();

        // Créer DTO depuis état actuel
        $dto = PlaceExplorationFiltersDTO::fromComponentData([
            'mode' => $this->searchMode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'address' => $this->address,
            'tags' => collect($this->selectedTags)->pluck('slug')->toArray(),
        ]);

        try {
            // Valider avec THROW (interactions utilisateur)
            $validationService = app(PlaceExplorationFiltersValidationService::class);
            $validatedDto = $validationService->validate($dto, ValidationStrategy::THROW);

            // Conversion en format component pour émission
            $validatedData = $validatedDto->toComponentData();

            // Émettre l'événement vers PlaceMap uniquement (PlaceList sera notifié via JavaScript)
            $this->dispatch('filters-updated', filters: $validatedData);

        } catch (InvalidPlaceFiltersException $e) {
            $this->addError('filters_validation', $e->getUserMessage());
        }
    }

    // ========================================
    // METHODS FOR FILTERS COLLAPSE
    // ========================================

    /**
     * Toggle l'état de repli des filtres
     */
    public function toggleFilters(): void
    {
        $this->filtersCollapsed = ! $this->filtersCollapsed;
    }

    /**
     * Replie les filtres (appelé après sélection/validation)
     */
    public function collapseFilters(): void
    {
        $this->filtersCollapsed = true;
    }

    /**
     * Déplie les filtres
     */
    public function expandFilters(): void
    {
        $this->filtersCollapsed = false;
    }

    /**
     * Fermer le message d'erreur de validation
     */
    public function dismissValidationError(): void
    {
        $this->resetErrorBag('filters_validation');
    }
}
