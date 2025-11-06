<?php

namespace App\Livewire\Web\Place\Index\Traits;

use App\Contracts\Services\GeocodingServiceInterface;
use App\Exceptions\GeocodingException;
use App\Support\Config\PlaceSearchConfig;
use Exception;

/**
 * Trait pour gérer l'autocomplétion d'adresses
 */
trait HandlesAddressAutocomplete
{
    public function updatedAddress(): void
    {
        $this->resetAddressSearch();

        if ($this->shouldShowAddressSuggestions()) {
            $this->searchAddresses();
        } elseif (! $this->address || strlen(trim($this->address)) < 3) {
            // Si l'adresse est vidée/trop courte, reset des coordonnées
            $this->latitude = null;
            $this->longitude = null;
            $this->emitFiltersChanged();
        }
    }

    /**
     * Select an address suggestion
     */
    public function selectAddressSuggestion(int $index): void
    {
        if (! isset($this->addressSuggestions[$index])) {
            return;
        }

        $suggestion = $this->addressSuggestions[$index];

        // Update all fields with selected suggestion
        $this->address = $suggestion['display_name'];
        $this->latitude = $suggestion['latitude'];
        $this->longitude = $suggestion['longitude'];
        $this->radius = PlaceSearchConfig::RADIUS_DEFAULT;

        // Émettre l'événement de changement
        $this->emitFiltersChanged();
    }

    /**
     * Check if we should show address suggestions
     */
    private function shouldShowAddressSuggestions(): bool
    {
        return $this->searchMode === 'proximity'
            && $this->address
            && strlen(trim($this->address)) >= 3;
    }

    /**
     * Reset address search state
     */
    private function resetAddressSearch(): void
    {
        $this->addressSuggestions = [];
        $this->addressSearchLoading = false;
        $this->resetErrorBag('address_search');
    }

    /**
     * Search for address suggestions
     */
    private function searchAddresses(): void
    {
        $this->addressSearchLoading = true;

        try {
            $geocodingService = app(GeocodingServiceInterface::class);
            $suggestions = $geocodingService->searchAddresses(trim($this->address), app()->getLocale(), 5);

            // Convertir les DTOs en tableaux pour Livewire
            $this->addressSuggestions = array_map(fn ($dto) => $dto->toArray(), $suggestions);

        } catch (GeocodingException $e) {
            $this->addError('address_search', $e->getUserMessage());
            $this->addressSuggestions = [];

        } catch (Exception $e) {
            $this->addError('address_search', __('errors/general.unexpected'));
            $this->addressSuggestions = [];
        }

        $this->addressSearchLoading = false;
    }
}
