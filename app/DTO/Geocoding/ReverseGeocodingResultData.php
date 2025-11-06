<?php

namespace App\DTO\Geocoding;

/**
 * DTO for reverse geocoding result (coordinates → address)
 */
readonly class ReverseGeocodingResultData
{
    /**
     * Create a new reverse geocoding result instance
     */
    public function __construct(
        public string $displayName,
        public AddressComponentsData $addressComponents,
        public ?string $type = null,
        public ?string $class = null,
    ) {}

    /**
     * Create from array (normalize different provider formats)
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            displayName: $data['display_name'] ?? $data['formatted_address'] ?? '',
            addressComponents: AddressComponentsData::fromArray($data['address_components'] ?? $data['address'] ?? []),
            type: $data['type'] ?? null,
            class: $data['class'] ?? null,
        );
    }

    /**
     * Convert to array for JSON serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'display_name' => $this->displayName,
            'address_components' => $this->addressComponents->toArray(),
            'type' => $this->type,
            'class' => $this->class,
        ];
    }
}
