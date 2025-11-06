<?php

namespace App\Exceptions;

use Exception;

class GeocodingException extends Exception
{
    private string $userMessage;

    private string $errorType;

    public function __construct(string $userMessage, string $errorType = 'general', string $technicalMessage = '')
    {
        $this->userMessage = $userMessage;
        if (config('app.debug')) {
            $this->userMessage .= ' Error = '.$technicalMessage;
        }
        $this->errorType = $errorType;

        $fullMessage = $technicalMessage ?: $userMessage;
        if ($technicalMessage && $technicalMessage !== $userMessage) {
            $fullMessage = "{$userMessage} [Technical: {$technicalMessage}]";
        }

        parent::__construct($fullMessage);
    }

    /**
     * Get user-friendly error message
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Get error type for logging/categorization
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * Create exception for no results found (forward geocoding)
     */
    public static function noResults(string $query): self
    {
        return new self(
            __('errors/geocoding.no_results'),
            'no_results',
            "No results found for query: {$query}"
        );
    }

    /**
     * Create exception for no results found in reverse geocoding
     */
    public static function noReverseGeocodeResults(float $latitude, float $longitude): self
    {
        return new self(
            __('errors/geocoding.reverse.no_results'),
            'reverse_no_results',
            "No address found for coordinates: {$latitude}, {$longitude}"
        );
    }

    /**
     * Create exception for invalid coordinates
     */
    public static function invalidCoordinates(float $latitude, float $longitude): self
    {
        return new self(
            __('errors/geocoding.reverse.invalid_coordinates'),
            'invalid_coordinates',
            "Invalid coordinates: {$latitude}, {$longitude}"
        );
    }

    /**
     * Create exception for location not geocodable (ocean, unmapped area, etc.)
     */
    public static function locationNotGeocodable(float $latitude, float $longitude): self
    {
        return new self(
            __('errors/geocoding.reverse.location_not_geocodable'),
            'location_not_geocodable',
            "Location not geocodable: {$latitude}, {$longitude} (likely ocean or unmapped area)"
        );
    }

    /**
     * Create exception for connection issues
     */
    public static function connectionFailed(string $technicalMessage): self
    {
        return new self(
            __('errors/geocoding.connection_failed'),
            'connection',
            $technicalMessage
        );
    }

    /**
     * Create exception for rate limiting
     */
    public static function rateLimited(int $retryAfterSeconds = 60): self
    {
        return new self(
            __('errors/geocoding.rate_limited', ['seconds' => $retryAfterSeconds]),
            'rate_limit',
            "Rate limit exceeded, retry after {$retryAfterSeconds} seconds"
        );
    }

    /**
     * Create exception for service errors
     */
    public static function serviceError(int $httpStatus, string $responseBody = ''): self
    {
        $userMessage = match (true) {
            $httpStatus >= 500 => __('errors/geocoding.service.unavailable'),
            $httpStatus === 429 => __('errors/geocoding.service.rate_limit'),
            default => __('errors/geocoding.service.generic')
        };

        $technicalMessage = "HTTP {$httpStatus}";
        if ($responseBody) {
            $technicalMessage .= ": {$responseBody}";
        }

        return new self($userMessage, 'service', $technicalMessage);
    }

    /**
     * Create exception for request failures
     */
    public static function requestFailed(string $technicalMessage): self
    {
        return new self(
            __('errors/geocoding.request_failed'),
            'request',
            $technicalMessage
        );
    }

    /**
     * Create exception for unexpected errors
     */
    public static function unexpectedError(string $technicalMessage): self
    {
        return new self(
            __('errors/geocoding.unexpected'),
            'unexpected',
            $technicalMessage
        );
    }
}
