<?php

namespace App\DTO\Geocoding;

/**
 * DTO for geocoding result (address → coordinates)
 */
readonly class GeocodingResultData
{
    /**
     * Create a new geocoding result instance
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $displayName,
        public AddressComponentsData $addressComponents,
        public float $importance,
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
            latitude: (float) ($data['latitude'] ?? $data['lat'] ?? 0),
            longitude: (float) ($data['longitude'] ?? $data['lon'] ?? $data['lng'] ?? 0),
            displayName: $data['display_name'] ?? $data['formatted_address'] ?? '',
            addressComponents: AddressComponentsData::fromArray($data['address_components'] ?? $data['address'] ?? []),
            importance: (float) ($data['importance'] ?? 0),
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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'display_name' => $this->displayName,
            'address_components' => $this->addressComponents->toArray(),
            'importance' => $this->importance,
            'type' => $this->type,
            'class' => $this->class,
        ];
    }
}
