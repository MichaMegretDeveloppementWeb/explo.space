<?php

namespace App\Strategies\Geocoding;

use App\Contracts\Services\GeocodingServiceInterface;
use App\DTO\Geocoding\AddressSuggestionData;
use App\DTO\Geocoding\GeocodingResultData;
use App\DTO\Geocoding\ReverseGeocodingResultData;

class MapboxGeocodingStrategy implements GeocodingServiceInterface
{
    private string $accessToken;

    public function __construct()
    {
        $this->accessToken = config('geocoding.providers.mapbox.access_token');

        if (empty($this->accessToken)) {
            throw new \InvalidArgumentException('Mapbox access token is not configured. Please set MAPBOX_ACCESS_TOKEN in your .env file.');
        }
    }

    /**
     * Geocode an address to coordinates
     *
     * @throws \Exception
     */
    public function geocode(string $address, ?string $locale = null): GeocodingResultData
    {
        throw new \Exception('Mapbox Geocoding is not implemented yet. This is a placeholder for future implementation.');
    }

    /**
     * Reverse geocode coordinates to address
     *
     * @throws \Exception
     */
    public function reverseGeocode(float $latitude, float $longitude, ?string $locale = null): ReverseGeocodingResultData
    {
        throw new \Exception('Mapbox Geocoding is not implemented yet. This is a placeholder for future implementation.');
    }

    /**
     * Search for address suggestions
     *
     * @return array<AddressSuggestionData>
     *
     * @throws \Exception
     */
    public function searchAddresses(string $query, ?string $locale = null, int $limit = 5): array
    {
        throw new \Exception('Mapbox Geocoding is not implemented yet. This is a placeholder for future implementation.');
    }

    /**
     * Check if the geocoding service is available
     */
    public function isAvailable(): bool
    {
        return ! empty($this->accessToken);
    }
}
