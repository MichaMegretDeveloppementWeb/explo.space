<?php

namespace App\Contracts\Services;

use App\DTO\Geocoding\AddressSuggestionData;
use App\DTO\Geocoding\GeocodingResultData;
use App\DTO\Geocoding\ReverseGeocodingResultData;
use App\Exceptions\GeocodingException;

interface GeocodingServiceInterface
{
    /**
     * Geocode an address to coordinates
     *
     * @throws GeocodingException
     */
    public function geocode(string $address, ?string $locale = null): GeocodingResultData;

    /**
     * Reverse geocode coordinates to address
     *
     * @throws GeocodingException
     */
    public function reverseGeocode(float $latitude, float $longitude, ?string $locale = null): ReverseGeocodingResultData;

    /**
     * Search for address suggestions
     *
     * @return array<AddressSuggestionData>
     *
     * @throws GeocodingException
     */
    public function searchAddresses(string $query, ?string $locale = null, int $limit = 5): array;

    /**
     * Check if the geocoding service is available
     */
    public function isAvailable(): bool;
}
