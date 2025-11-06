<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

use App\Contracts\Services\GeocodingServiceInterface;
use App\Exceptions\GeocodingException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

trait ManagesLocation
{
    /**
     * Normaliser une coordonnée à 6 décimales (précision au mètre)
     */
    private function normalizeCoordinate(float $value): float
    {
        return round($value, 6);
    }

    /**
     * Hook appelé quand la recherche d'adresse change
     * Recherche les suggestions via Nominatim avec debounce côté frontend
     */
    public function updatedQueryAddress(): void
    {

        if (strlen($this->queryAddress) < 3) {
            $this->suggestions = [];
            $this->showSuggestions = false;

            return;
        }

        // Réinitialiser les erreurs précédentes
        $this->resetErrorBag('queryAddress');

        try {
            $service = app(GeocodingServiceInterface::class);
            $suggestions = $service->searchAddresses($this->queryAddress, locale: null, limit: 10);

            // Convertir les DTOs en tableaux pour Livewire
            $this->suggestions = array_map(fn ($dto) => $dto->toArray(), $suggestions);
            $this->showSuggestions = true;
        } catch (GeocodingException $e) {
            // Cas spécial : aucune adresse trouvée (pas une erreur bloquante)
            if ($e->getErrorType() === 'no_results') {
                $this->suggestions = [];
                $this->showSuggestions = true; // Garder ouvert pour afficher "Aucun résultat"
            } else {
                // Erreur technique (rate limit, service error, connection, etc.)
                $this->suggestions = [];
                $this->showSuggestions = false;
                $this->addError('queryAddress', $e->getUserMessage());
            }
        } catch (\Exception $e) {
            // Erreur inattendue (non liée au géocodage)
            $this->suggestions = [];
            $this->showSuggestions = false;

            $errorMessage = __('errors/general.unexpected');
            if (app()->environment('local')) {
                $errorMessage .= ' ('.$e->getMessage().')';
            }

            $this->addError('queryAddress', $errorMessage);
            Log::error('Unexpected error during address search', [
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
            ]);
        }
    }

    /**
     * Sélectionner une adresse depuis les suggestions
     */
    public function selectAddress(int $index): void
    {
        if (! isset($this->suggestions[$index])) {
            return;
        }

        // Réinitialiser les erreurs précédentes
        $this->resetErrorBag(['queryAddress', 'address']);

        $selected = $this->suggestions[$index];

        // Mettre à jour les propriétés avec normalisation à 6 décimales
        $this->placeAddress = $selected['display_name'];
        $this->address = $selected['display_name'];
        $this->queryAddress = $selected['display_name'];
        $this->latitude = $this->normalizeCoordinate((float) $selected['latitude']);
        $this->longitude = $this->normalizeCoordinate((float) $selected['longitude']);

        // Fermer les suggestions
        $this->suggestions = [];
        $this->showSuggestions = false;

        // Notifier JavaScript pour mettre à jour le marker
        $this->dispatch('address-selected', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
    }

    /**
     * Listener pour clic sur la carte
     * Fait du reverse geocoding pour trouver l'adresse
     */
    #[On('map-clicked')]
    public function handleMapClick(float $latitude, float $longitude): void
    {
        // Réinitialiser les erreurs précédentes
        $this->resetErrorBag('address');

        // Mettre à jour les coordonnées avec normalisation à 6 décimales
        $this->latitude = $this->normalizeCoordinate($latitude);
        $this->longitude = $this->normalizeCoordinate($longitude);

        // Reverse geocoding
        try {
            $service = app(GeocodingServiceInterface::class);
            $result = $service->reverseGeocode($latitude, $longitude);

            // Le DTO est retourné, on accède directement à ses propriétés
            $this->placeAddress = $result->displayName;
            $this->address = $result->displayName;
            $this->queryAddress = $result->displayName;
        } catch (GeocodingException $e) {
            $this->placeAddress = null;
            $this->address = null;
            $this->queryAddress = '';

            $errorMessage = $e->getMessage();

            $this->addError('address', $errorMessage);
            $this->addError('placeAddress', $errorMessage);
            Log::error('Reverse geocoding failed', ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->placeAddress = null;
            $this->address = null;
            $this->queryAddress = '';

            $errorMessage = 'Erreur inattendue lors de la recherche d\'adresse. Veuillez contacter l\'administrateur si cela persiste.';
            if (app()->environment('local')) {
                $errorMessage .= ' ('.$e->getMessage().')';
            }

            $this->addError('address', $errorMessage);
            $this->addError('placeAddress', $errorMessage);
            Log::error('Unexpected error during reverse geocoding', [
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
            ]);
        }
    }

    /**
     * Hook appelé quand latitude est modifiée manuellement
     * Notifie JavaScript pour mettre à jour le marker
     */
    public function updatedLatitude(): void
    {
        // Normaliser à 6 décimales
        $this->latitude = $this->normalizeCoordinate($this->latitude);

        if ($this->latitude !== 0.0 && $this->longitude !== 0.0) {
            $this->dispatch('coordinates-changed', [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);
        }
    }

    /**
     * Hook appelé quand longitude est modifiée manuellement
     * Notifie JavaScript pour mettre à jour le marker
     */
    public function updatedLongitude(): void
    {
        // Normaliser à 6 décimales
        $this->longitude = $this->normalizeCoordinate($this->longitude);

        if ($this->latitude !== 0.0 && $this->longitude !== 0.0) {
            $this->dispatch('coordinates-changed', [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);
        }
    }
}
