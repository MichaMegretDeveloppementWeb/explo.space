<?php

namespace App\Livewire\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Enums\ValidationStrategy;
use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use App\Services\Web\Place\Index\PlaceExplorationFiltersValidationService;
use App\Support\Config\PlaceSearchConfig;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class PlaceExplorer extends Component
{
    // ========================================
    // PROPRIÉTÉS URL (synchronisées avec navigateur)
    // ========================================

    #[Url(as: 'mode')]
    public string $searchMode = PlaceSearchConfig::SEARCH_MODE_DEFAULT;

    #[Url(as: 'lat')]
    public ?float $latitude = null;

    #[Url(as: 'lng')]
    public ?float $longitude = null;

    #[Url(as: 'radius')]
    public int $radius = PlaceSearchConfig::RADIUS_DEFAULT;

    #[Url(as: 'address')]
    public ?string $address = null;

    #[Url(as: 'tags')]
    public string $selectedTagsSlugs = '';

    #[Url(as: 'featured')]
    public bool $showFeaturedOnly = false;

    // ========================================
    // PROPRIÉTÉS POUR ENFANTS
    // ========================================

    /** @var array<string, mixed> */
    public array $initialFilters = [];

    // ========================================
    // LIFECYCLE METHODS
    // ========================================

    /**
     * @param  array<string, mixed>  $filters
     *
     * @throws InvalidPlaceFiltersException
     */
    public function mount(array $filters = []): void
    {
        // Créer DTO depuis URL params (format court : lat, lng)
        $rawDto = PlaceExplorationFiltersDTO::fromUrlParams($filters);

        // Valider avec stratégie CORRECT_SILENTLY (correction automatique depuis URL)
        $validationService = app(PlaceExplorationFiltersValidationService::class);
        $validatedDto = $validationService->validate($rawDto, ValidationStrategy::CORRECT_SILENTLY);

        // Affecter les valeurs validées aux propriétés #[Url]
        $this->searchMode = $validatedDto->mode;
        $this->latitude = $validatedDto->latitude;
        $this->longitude = $validatedDto->longitude;
        $this->radius = $validatedDto->radius;
        $this->address = $validatedDto->address;
        $this->selectedTagsSlugs = ! empty($validatedDto->tags) ? implode(',', $validatedDto->tags) : '';
        $this->showFeaturedOnly = $validatedDto->featured;

        // Préparer filtres initiaux pour composants enfants
        $this->initialFilters = $validatedDto->toComponentData();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.web.place.index.place-explorer');
    }

    // ========================================
    // EVENT LISTENERS
    // ========================================

    /**
     * Écouter les changements de filtres depuis PlaceFilters
     * et synchroniser les URL params
     *
     * SÉCURITÉ : Re-valide avec CORRECT_SILENTLY (défense en profondeur)
     * pour garantir que l'URL ne contient jamais de valeurs invalides.
     *
     * @param  array<string, mixed>  $filters
     *
     * @throws \App\Exceptions\Admin\Place\InvalidPlaceFiltersException
     */
    #[On('filters-updated')]
    public function syncUrlParams(array $filters): void
    {

        // Créer DTO depuis données composant (format long : latitude, longitude)
        $rawDto = PlaceExplorationFiltersDTO::fromComponentData($filters);

        // Re-valider avec CORRECT_SILENTLY (défense en profondeur)
        $validationService = app(PlaceExplorationFiltersValidationService::class);
        $validatedDto = $validationService->validate($rawDto, ValidationStrategy::CORRECT_SILENTLY);

        // Mettre à jour les propriétés #[Url] avec données validées
        $this->searchMode = $validatedDto->mode;
        $this->latitude = $validatedDto->latitude;
        $this->longitude = $validatedDto->longitude;
        $this->radius = $validatedDto->radius;
        $this->address = $validatedDto->address;
        $this->selectedTagsSlugs = ! empty($validatedDto->tags) ? implode(',', $validatedDto->tags) : '';
        $this->showFeaturedOnly = $validatedDto->featured;

        $this->skipRender();
    }
}
