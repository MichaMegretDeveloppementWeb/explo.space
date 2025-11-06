<?php

namespace App\Strategies\Geocoding;

use App\Contracts\Services\GeocodingServiceInterface;
use App\DTO\Geocoding\AddressComponentsData;
use App\DTO\Geocoding\AddressSuggestionData;
use App\DTO\Geocoding\GeocodingResultData;
use App\DTO\Geocoding\ReverseGeocodingResultData;
use App\Exceptions\GeocodingException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NominatimGeocodingStrategy implements GeocodingServiceInterface
{
    private string $baseUrl;

    private int $rateLimitDelay;

    private string $userAgent;

    public function __construct()
    {
        $this->baseUrl = config('geocoding.providers.nominatim.url', 'https://nominatim.openstreetmap.org');
        $this->rateLimitDelay = config('geocoding.providers.nominatim.rate_limit_delay', 1);
        $this->userAgent = config('geocoding.providers.nominatim.user_agent', config('app.name'));
    }

    /**
     * Geocode an address to coordinates
     *
     * @throws GeocodingException
     */
    public function geocode(string $address, ?string $locale = null): GeocodingResultData
    {
        $locale = $locale ?? app()->getLocale();

        try {
            $this->enforceRateLimit();

            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
            ])
                ->timeout(10)
                ->get("{$this->baseUrl}/search", [
                    'q' => $address,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 1,
                    'accept-language' => $this->mapLocaleToAcceptLanguage($locale),
                ]);

            if (! $response->successful()) {
                $status = $response->status();
                $responseBody = $response->body();

                Log::warning('Nominatim geocoding failed', [
                    'address' => $address,
                    'status' => $status,
                    'response' => $responseBody,
                ]);

                if ($status === 429) {
                    throw GeocodingException::rateLimited(60);
                }

                throw GeocodingException::serviceError($status, $responseBody);
            }

            $data = $response->json();

            if (empty($data)) {
                throw GeocodingException::noResults($address);
            }

            $result = $data[0];

            // Return DTO instead of array
            return new GeocodingResultData(
                latitude: (float) $result['lat'],
                longitude: (float) $result['lon'],
                displayName: $result['display_name'],
                addressComponents: AddressComponentsData::fromArray($result['address'] ?? []),
                importance: (float) ($result['importance'] ?? 0),
                type: $result['type'] ?? null,
                class: $result['class'] ?? null,
            );

        } catch (ConnectionException $e) {
            Log::error('Nominatim connection error during geocoding', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);
            throw GeocodingException::connectionFailed($e->getMessage());
        } catch (RequestException $e) {
            Log::error('Nominatim request error during geocoding', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);
            throw GeocodingException::requestFailed($e->getMessage());
        } catch (GeocodingException $e) {
            Log::error('Nominatim geocoding exception', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during geocoding', [
                'address' => $address,
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
            ]);
            throw GeocodingException::unexpectedError('Unexpected error: '.$e->getMessage());
        }
    }

    /**
     * Reverse geocode coordinates to address
     *
     * @throws GeocodingException
     */
    public function reverseGeocode(float $latitude, float $longitude, ?string $locale = null): ReverseGeocodingResultData
    {
        $locale = $locale ?? app()->getLocale();

        try {
            $this->enforceRateLimit();

            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
            ])
                ->timeout(10)
                ->get("{$this->baseUrl}/reverse", [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'accept-language' => $this->mapLocaleToAcceptLanguage($locale),
                ]);

            if (! $response->successful()) {
                $status = $response->status();
                $responseBody = $response->body();

                Log::warning('Nominatim reverse geocoding failed', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status' => $status,
                    'response' => $responseBody,
                ]);

                if ($status === 429) {
                    throw GeocodingException::rateLimited(60);
                }

                throw GeocodingException::serviceError($status, $responseBody);
            }

            $result = $response->json();

            if (empty($result) || isset($result['error'])) {
                throw GeocodingException::noReverseGeocodeResults($latitude, $longitude);
            }

            // Return DTO instead of array
            return new ReverseGeocodingResultData(
                displayName: $result['display_name'],
                addressComponents: AddressComponentsData::fromArray($result['address'] ?? []),
                type: $result['type'] ?? null,
                class: $result['class'] ?? null,
            );

        } catch (ConnectionException $e) {
            Log::error('Nominatim connection error during reverse geocoding', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
            ]);
            throw GeocodingException::connectionFailed($e->getMessage());
        } catch (RequestException $e) {
            Log::error('Nominatim request error during reverse geocoding', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
            ]);
            throw GeocodingException::requestFailed($e->getMessage());
        } catch (GeocodingException $e) {
            Log::error('Nominatim reverse geocoding exception', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during reverse geocoding', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
            ]);
            throw GeocodingException::unexpectedError('Unexpected error: '.$e->getMessage());
        }
    }

    /**
     * Search for address suggestions
     *
     * @return array<AddressSuggestionData>
     *
     * @throws GeocodingException
     */
    public function searchAddresses(string $query, ?string $locale = null, int $limit = 5): array
    {
        $locale = $locale ?? app()->getLocale();

        try {
            $this->enforceRateLimit();

            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
            ])
                ->timeout(10)
                ->get("{$this->baseUrl}/search", [
                    'q' => $query,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => min($limit, 20), // Cap at 20 for performance
                    'accept-language' => $this->mapLocaleToAcceptLanguage($locale),
                ]);

            if (! $response->successful()) {
                $status = $response->status();
                $responseBody = $response->body();

                Log::warning('Nominatim address search failed', [
                    'query' => $query,
                    'status' => $status,
                    'response' => $responseBody,
                ]);

                if ($status === 429) {
                    throw GeocodingException::rateLimited(60);
                }

                throw GeocodingException::serviceError($status, $responseBody);
            }

            $data = $response->json();

            if (empty($data)) {
                throw GeocodingException::noResults($query);
            }

            // Map results to DTOs
            $suggestions = array_map(function ($result) {
                return new AddressSuggestionData(
                    displayName: $result['display_name'],
                    latitude: (float) $result['lat'],
                    longitude: (float) $result['lon'],
                    type: $result['type'] ?? null,
                    class: $result['class'] ?? null,
                    importance: (float) ($result['importance'] ?? 0.0),
                );
            }, $data);

            // Sort by importance (descending - most relevant first)
            usort($suggestions, function ($a, $b) {
                return $b->importance <=> $a->importance;
            });

            return $suggestions;

        } catch (ConnectionException $e) {
            Log::error('Nominatim connection error during address search', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            throw GeocodingException::connectionFailed($e->getMessage());
        } catch (RequestException $e) {
            Log::error('Nominatim request error during address search', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            throw GeocodingException::requestFailed($e->getMessage());
        } catch (GeocodingException $e) {
            Log::error('Nominatim address search exception', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during address search', [
                'query' => $query,
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),
            ]);
            throw GeocodingException::unexpectedError('Unexpected error: '.$e->getMessage());
        }
    }

    /**
     * Check if the geocoding service is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
            ])
                ->timeout(5)
                ->get("{$this->baseUrl}/status");

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Nominatim availability check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Map application locale to Accept-Language header
     */
    private function mapLocaleToAcceptLanguage(string $locale): string
    {
        return match ($locale) {
            'fr' => 'fr,en;q=0.9',
            'en' => 'en,fr;q=0.9',
            default => 'en,fr;q=0.9',
        };
    }

    /**
     * Enforce rate limiting to respect Nominatim usage policy
     */
    private function enforceRateLimit(): void
    {
        $cacheKey = 'nominatim_last_request';
        $lastRequest = Cache::get($cacheKey);

        if ($lastRequest && time() - $lastRequest < $this->rateLimitDelay) {
            $sleepTime = $this->rateLimitDelay - (time() - $lastRequest);
            sleep($sleepTime);
        }

        Cache::put($cacheKey, time(), 60);
    }
}
