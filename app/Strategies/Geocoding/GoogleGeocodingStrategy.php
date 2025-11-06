<?php

namespace App\Strategies\Geocoding;

use App\Contracts\Services\GeocodingServiceInterface;
use App\DTO\Geocoding\AddressSuggestionData;
use App\DTO\Geocoding\GeocodingResultData;
use App\DTO\Geocoding\ReverseGeocodingResultData;

class GoogleGeocodingStrategy implements GeocodingServiceInterface
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('geocoding.providers.google.api_key');

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('Google Geocoding API key is not configured. Please set GOOGLE_GEOCODING_API_KEY in your .env file.');
        }
    }

    /**
     * Geocode an address to coordinates
     *
     * @throws \Exception
     */
    public function geocode(string $address, ?string $locale = null): GeocodingResultData
    {
        throw new \Exception('Google Geocoding is not implemented yet. This is a placeholder for future implementation.');
    }

    /**
     * Reverse geocode coordinates to address
     *
     * @throws \Exception
     */
    public function reverseGeocode(float $latitude, float $longitude, ?string $locale = null): ReverseGeocodingResultData
    {
        throw new \Exception('Google Geocoding is not implemented yet. This is a placeholder for future implementation.');
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
        throw new \Exception('Google Geocoding is not implemented yet. This is a placeholder for future implementation.');
    }

    /**
     * Check if the geocoding service is available
     */
    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }
}
