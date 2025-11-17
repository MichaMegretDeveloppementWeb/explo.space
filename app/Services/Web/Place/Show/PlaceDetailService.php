<?php

namespace App\Services\Web\Place\Show;

use App\Contracts\Repositories\Web\Place\Show\PlaceDetailRepositoryInterface;
use App\DTO\Web\Place\PlaceDetailDTO;
use App\Exceptions\Web\Place\Show\PlaceNotFoundException;
use App\Exceptions\Web\Place\Show\PlaceTranslationNotFoundException;
use App\Models\Place;
use Illuminate\Support\Facades\Log;

class PlaceDetailService
{
    public function __construct(
        private readonly PlaceDetailRepositoryInterface $repository
    ) {}

    /**
     * Récupérer les détails d'un lieu par son slug
     *
     * @param  string  $slug  Le slug du lieu dans la locale donnée
     * @param  string  $locale  La locale de recherche (fr, en)
     * @return PlaceDetailDTO Les données structurées du lieu
     *
     * @throws PlaceNotFoundException Si le lieu n'est pas trouvé
     * @throws PlaceTranslationNotFoundException Si la traduction n'est pas trouvée
     */
    public function getPlaceDetailBySlug(string $slug, string $locale): PlaceDetailDTO
    {
        // Récupérer le lieu via le repository
        $place = $this->repository->getPlaceBySlug($slug, $locale);

        // Vérifier que le lieu existe
        if (! $place) {
            Log::warning('Place not found for slug', [
                'slug' => $slug,
                'locale' => $locale,
            ]);

            throw new PlaceNotFoundException($slug, $locale);
        }

        // Vérifier que la traduction existe
        $translation = $place->translations->first();

        if (! $translation) {
            Log::warning('Place translation not found', [
                'place_id' => $place->id,
                'locale' => $locale,
            ]);

            throw new PlaceTranslationNotFoundException($place->id, $locale);
        }

        // Construire et retourner le DTO
        return $this->buildDTO($place, $translation, $locale);
    }

    /**
     * Construire le DTO à partir du modèle Place
     *
     * @param  Place  $place  Le modèle Place avec ses relations
     * @param  \App\Models\PlaceTranslation  $translation  La traduction du lieu
     * @param  string  $locale  La locale actuelle
     * @return PlaceDetailDTO Le DTO construit
     */
    private function buildDTO(Place $place, $translation, string $locale): PlaceDetailDTO
    {
        return new PlaceDetailDTO(
            id: $place->id,
            slug: $translation->slug,
            title: $translation->title,
            description: $translation->description,
            practicalInfo: $translation->practical_info,
            latitude: (float) $place->latitude,
            longitude: (float) $place->longitude,
            address: $place->address,
            isFeatured: $place->is_featured,
            tags: $this->prepareTags($place->tags),
            photos: $this->preparePhotos($place->photos),
            mainPhotoUrl: $this->getMainPhotoUrl($place->photos),
            mainPhotoAltText: $this->getMainPhotoAltText($place->photos),
            createdAt: $place->created_at->translatedFormat('d F Y'),
            updatedAt: $place->updated_at->translatedFormat('d F Y'),
        );
    }

    /**
     * Préparer les tags avec leurs traductions
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag>  $tags
     * @return array<int, array{name: string, slug: string, color: string}>
     */
    private function prepareTags($tags): array
    {
        return $tags->map(function (\App\Models\Tag $tag) {
            $translation = $tag->translations->first();

            if (! $translation) {
                return null;
            }

            return [
                'name' => $translation->name,
                'slug' => $translation->slug,
                'color' => $tag->color,
            ];
        })
            ->filter() // Retirer les nulls
            ->values()
            ->toArray();
    }

    /**
     * Préparer les photos avec leurs traductions alt_text
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Photo>  $photos
     * @return array<int, array{id: int, url: string, medium_url: string, is_main: bool, sort_order: int, alt_text: string|null}>
     */
    private function preparePhotos($photos): array
    {
        return $photos->map(function (\App\Models\Photo $photo) {
            // Récupérer la traduction alt_text pour la locale actuelle
            $translation = $photo->translations->first();
            $altText = $translation?->alt_text;

            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'medium_url' => $photo->medium_url,
                'is_main' => $photo->is_main,
                'sort_order' => $photo->sort_order,
                'alt_text' => $altText,
            ];
        })->toArray();
    }

    /**
     * Récupérer l'URL de la photo principale
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Photo>  $photos
     * @return string|null L'URL de la photo principale ou null
     */
    private function getMainPhotoUrl($photos): ?string
    {
        $mainPhoto = $photos->firstWhere('is_main', true);

        return $mainPhoto?->url;
    }

    /**
     * Récupérer le texte alternatif traduit de la photo principale
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Photo>  $photos
     * @return string|null Le texte alternatif traduit ou null
     */
    private function getMainPhotoAltText($photos): ?string
    {
        $mainPhoto = $photos->firstWhere('is_main', true);

        if (! $mainPhoto) {
            return null;
        }

        // Récupérer la traduction alt_text pour la locale actuelle
        $translation = $mainPhoto->translations->first();

        return $translation?->alt_text;
    }
}
