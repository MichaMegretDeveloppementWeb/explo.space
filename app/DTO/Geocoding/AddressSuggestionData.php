<?php

namespace App\DTO\Geocoding;

/**
 * DTO for address suggestion data from geocoding service
 */
readonly class AddressSuggestionData
{
    /**
     * Create a new address suggestion instance
     */
    public function __construct(
        public string $displayName,
        public float $latitude,
        public float $longitude,
        public ?string $type = null,
        public ?string $class = null,
        public float $importance = 0.0,
    ) {}

    /**
     * Create from array (from Nominatim service response)
     *
     * @param  array{latitude: float, longitude: float, display_name: string, type: string|null, class: string|null, importance: float|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            displayName: $data['display_name'],
            latitude: $data['latitude'],
            longitude: $data['longitude'],
            type: $data['type'] ?? null,
            class: $data['class'] ?? null,
            importance: $data['importance'] ?? 0.0,
        );
    }

    /**
     * Convert to array for JSON serialization
     *
     * @return array{display_name: string, latitude: float, longitude: float, type: string, class: string, importance: float}
     */
    public function toArray(): array
    {
        return [
            'display_name' => $this->displayName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'type' => $this->type,
            'class' => $this->class,
            'importance' => $this->importance,
        ];
    }
}
