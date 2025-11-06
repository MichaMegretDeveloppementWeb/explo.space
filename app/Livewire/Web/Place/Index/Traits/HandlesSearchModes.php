<?php

namespace App\Livewire\Web\Place\Index\Traits;

use App\Support\Config\PlaceSearchConfig;

/**
 * Trait pour gérer le switch entre les modes de recherche (proximity/worldwide)
 */
trait HandlesSearchModes
{
    public function switchToProximity(): void
    {
        $this->searchMode = 'proximity';
        $this->updatedSearchMode();
    }

    public function switchToWorldwide(): void
    {
        $this->searchMode = 'worldwide';
        $this->updatedSearchMode();
    }

    public function updatedSearchMode(): void
    {
        $this->resetFilters();
        $this->emitFiltersChanged();
    }

    /**
     * Reset filters but keep tags logic intact
     */
    public function resetFilters(): void
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->radius = PlaceSearchConfig::RADIUS_DEFAULT;
        $this->address = null;
        $this->selectedTagsSlugs = '';
        $this->selectedTags = [];
    }
}
