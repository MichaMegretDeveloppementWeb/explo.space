<?php

namespace App\Strategies\Geocoding;

use App\Contracts\Services\GeocodingServiceInterface;

class GeocodingResolver
{
    /**
     * Cache of resolved strategy instances
     *
     * @var array<string, GeocodingServiceInterface>
     */
    private array $instances = [];

    /**
     * Get a geocoding strategy instance via the specified driver.
     */
    public function via(string $driver): GeocodingServiceInterface
    {
        // Return cached instance if exists
        if (isset($this->instances[$driver])) {
            return $this->instances[$driver];
        }

        // Resolve and cache the strategy instance
        $this->instances[$driver] = $this->resolve($driver);

        return $this->instances[$driver];
    }

    /**
     * Resolve the geocoding strategy instance for the given driver.
     */
    private function resolve(string $driver): GeocodingServiceInterface
    {
        return match ($driver) {
            'nominatim' => new NominatimGeocodingStrategy,
            'google' => new GoogleGeocodingStrategy,
            'mapbox' => new MapboxGeocodingStrategy,
            default => throw new \InvalidArgumentException("Geocoding driver [{$driver}] is not supported"),
        };
    }
}
