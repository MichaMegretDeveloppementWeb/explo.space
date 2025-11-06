<?php

namespace App\DTO\Geocoding;

/**
 * DTO for normalized address components from geocoding services
 */
readonly class AddressComponentsData
{
    /**
     * Create a new address components instance
     */
    public function __construct(
        public ?string $houseNumber = null,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postcode = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $countryCode = null,
    ) {}

    /**
     * Create from array (normalize different provider formats)
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            houseNumber: $data['house_number'] ?? $data['houseNumber'] ?? null,
            street: $data['street'] ?? $data['road'] ?? null,
            city: $data['city'] ?? $data['town'] ?? $data['village'] ?? null,
            postcode: $data['postcode'] ?? $data['postal_code'] ?? null,
            state: $data['state'] ?? $data['region'] ?? null,
            country: $data['country'] ?? null,
            countryCode: $data['country_code'] ?? $data['countryCode'] ?? null,
        );
    }

    /**
     * Convert to array for JSON serialization
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'house_number' => $this->houseNumber,
            'street' => $this->street,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'state' => $this->state,
            'country' => $this->country,
            'country_code' => $this->countryCode,
        ];
    }
}
