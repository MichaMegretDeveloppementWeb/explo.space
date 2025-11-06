<?php

namespace App\DTO\Web\Place;

use Livewire\Wireable;

/**
 * DTO pour les données de prévisualisation d'un lieu dans la modale
 *
 * Contient uniquement les informations nécessaires pour l'affichage
 * de la carte de prévisualisation (modale)
 *
 * Implémente Wireable pour être compatible avec Livewire (sérialisation/désérialisation)
 */
readonly class PlacePreviewDTO implements Wireable
{
    /**
     * @param  int  $id  ID du lieu
     * @param  string  $slug  Slug traduit du lieu (pour la navigation)
     * @param  string  $title  Titre traduit du lieu
     * @param  string  $descriptionExcerpt  Extrait de description (max 200 caractères)
     * @param  string|null  $mainPhotoUrl  URL de la photo principale (null si aucune photo)
     * @param  array<int, array{name: string, slug: string, color: string}>  $tags  Liste des tags (max 5)
     */
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $descriptionExcerpt,
        public ?string $mainPhotoUrl,
        public array $tags = []
    ) {}

    /**
     * Convertit le DTO en tableau pour Livewire (sérialisation)
     *
     * @return array<string, mixed>
     */
    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'descriptionExcerpt' => $this->descriptionExcerpt,
            'mainPhotoUrl' => $this->mainPhotoUrl,
            'tags' => $this->tags,
        ];
    }

    /**
     * Reconstruit le DTO depuis un tableau Livewire (désérialisation)
     *
     * @param  array<string, mixed>  $value
     */
    public static function fromLivewire($value): self
    {
        return new self(
            id: $value['id'],
            slug: $value['slug'],
            title: $value['title'],
            descriptionExcerpt: $value['descriptionExcerpt'],
            mainPhotoUrl: $value['mainPhotoUrl'],
            tags: $value['tags'] ?? []
        );
    }
}
