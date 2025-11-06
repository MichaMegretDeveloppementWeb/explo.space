<?php

namespace App\DTO\Web\Place;

use Livewire\Wireable;

readonly class PlaceDetailDTO implements Wireable
{
    /**
     * @param  int  $id  ID du lieu
     * @param  string  $slug  Slug du lieu (locale spécifique)
     * @param  string  $title  Titre du lieu
     * @param  string  $description  Description complète
     * @param  string|null  $practicalInfo  Informations pratiques (optionnel)
     * @param  float  $latitude  Latitude (coordonnée GPS)
     * @param  float  $longitude  Longitude (coordonnée GPS)
     * @param  string|null  $address  Adresse physique (optionnel)
     * @param  array<int, array{name: string, slug: string, color: string}>  $tags  Tags avec traductions
     * @param  array<int, array{id: int, url: string, medium_url: string, is_main: bool, sort_order: int}>  $photos  Photos du lieu
     * @param  string|null  $mainPhotoUrl  URL de la photo principale
     * @param  string  $createdAt  Date de création formatée
     * @param  string  $updatedAt  Date de dernière modification formatée
     */
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $description,
        public ?string $practicalInfo,
        public float $latitude,
        public float $longitude,
        public ?string $address,
        public array $tags,
        public array $photos,
        public ?string $mainPhotoUrl,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    /**
     * Sérialiser le DTO pour Livewire (transformation vers format transmissible)
     *
     * @return array<string, mixed>
     */
    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'practicalInfo' => $this->practicalInfo,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'tags' => $this->tags,
            'photos' => $this->photos,
            'mainPhotoUrl' => $this->mainPhotoUrl,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * Désérialiser depuis Livewire (reconstruction depuis format transmis)
     *
     * @param  array<string, mixed>  $value
     */
    public static function fromLivewire($value): self
    {
        return new self(
            id: $value['id'],
            slug: $value['slug'],
            title: $value['title'],
            description: $value['description'],
            practicalInfo: $value['practicalInfo'],
            latitude: $value['latitude'],
            longitude: $value['longitude'],
            address: $value['address'],
            tags: $value['tags'],
            photos: $value['photos'],
            mainPhotoUrl: $value['mainPhotoUrl'],
            createdAt: $value['createdAt'],
            updatedAt: $value['updatedAt'],
        );
    }
}
