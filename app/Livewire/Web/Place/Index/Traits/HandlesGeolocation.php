<?php

namespace App\Livewire\Web\Place\Index\Traits;

use App\Contracts\Services\GeocodingServiceInterface;
use App\Exceptions\GeocodingException;
use App\Support\Config\PlaceSearchConfig;
use Exception;
use Livewire\Attributes\On;

/**
 * Trait pour gérer la géolocalisation utilisateur
 */
trait HandlesGeolocation
{
    public function updateLocation(float $lat, float $lng, ?string $address = null): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->address = $address;
        $this->radius = PlaceSearchConfig::RADIUS_DEFAULT;

        $this->emitFiltersChanged();
    }

    public function requestGeolocation(): void
    {
        $this->geolocLoading = true;
        $this->dispatch('requestGeolocation', componentId: $this->getId());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    #[On('geolocationSuccess')]
    public function onGeolocationSuccess(array $data): void
    {
        $this->geolocLoading = false;
        $this->resetErrorBag('geolocation');

        // Try to reverse geocode the coordinates to get an address
        $address = $this->reverseGeocodeCoordinates($data['latitude'], $data['longitude']);

        $this->updateLocation(
            $data['latitude'],
            $data['longitude'],
            $address
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    #[On('geolocationError')]
    public function onGeolocationError(array $data): void
    {
        $this->geolocLoading = false;

        // Check if message contains a translation key pattern
        $message = $data['message'];
        if (str_starts_with($message, 'geolocation.')) {
            // Extract the error key and translate it
            $errorKey = str_replace('geolocation.', '', $message);
            $message = __('errors/geolocation.'.$errorKey);
        }

        $this->addError('geolocation', $message);
    }

    /**
     * Reverse geocode coordinates to get address
     */
    private function reverseGeocodeCoordinates(float $latitude, float $longitude): ?string
    {
        try {
            $geocodingService = app(GeocodingServiceInterface::class);
            $result = $geocodingService->reverseGeocode($latitude, $longitude);

            return $result->displayName;
        } catch (GeocodingException|Exception $e) {
            return 'Lat : '.$latitude.', Long : '.$longitude;
        }
    }
}
