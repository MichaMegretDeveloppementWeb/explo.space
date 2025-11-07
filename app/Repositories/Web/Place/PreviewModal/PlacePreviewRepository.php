<?php

namespace App\Repositories\Web\Place\PreviewModal;

use App\Contracts\Repositories\Web\Place\PreviewModal\PlacePreviewRepositoryInterface;
use App\DTO\Web\Place\PlacePreviewDTO;
use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;
use App\Models\Place;

class PlacePreviewRepository implements PlacePreviewRepositoryInterface
{
    /**
     * Longueur maximale de l'extrait de description pour la modale
     */
    private const DESCRIPTION_EXCERPT_LENGTH = 200;

    /**
     * Nombre maximum de tags à afficher dans la modale
     */
    private const MAX_TAGS_DISPLAY = 5;

    /**
     * {@inheritDoc}
     */
    public function getPlacePreviewById(int $placeId): PlacePreviewDTO
    {
        $locale = app()->getLocale();

        // Requête optimisée avec eager loading ciblé
        $place = Place::with([
            // Traduction active uniquement
            'translations' => function ($query) use ($locale) {
                $query->where('locale', $locale)
                    ->where('status', 'published')
                    ->select('id', 'place_id', 'locale', 'title', 'slug', 'description');
            },
            // Photo principale uniquement
            'photos' => function ($query) {
                $query->where('is_main', true)
                    ->select('id', 'place_id', 'filename', 'is_main');
            },
            // Tags avec traductions (limités à 5)
            'tags' => function ($query) {
                $query->limit(self::MAX_TAGS_DISPLAY)
                    ->select('tags.id', 'tags.color');
            },
            'tags.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale)
                    ->where('status', 'published')
                    ->select('id', 'tag_id', 'locale', 'name', 'slug');
            },
        ])
            ->select('id', 'latitude', 'longitude', 'is_featured')
            ->find($placeId);

        if (! $place) {
            throw new PlaceNotFoundException($placeId);
        }

        // Récupérer la traduction active
        $translation = $place->translations->first();

        if (! $translation) {
            throw new PlaceTranslationNotFoundException($placeId, $locale);
        }

        // Générer l'extrait de description
        $descriptionExcerpt = $this->generateExcerpt($translation->description);

        // Récupérer l'URL de la photo principale
        $mainPhotoUrl = $this->getMainPhotoUrl($place);

        // Préparer les tags avec leurs traductions
        $tags = $this->prepareTags($place->tags);

        return new PlacePreviewDTO(
            id: $place->id,
            slug: $translation->slug,
            title: $translation->title,
            descriptionExcerpt: $descriptionExcerpt,
            mainPhotoUrl: $mainPhotoUrl,
            isFeatured: $place->is_featured,
            tags: $tags
        );
    }

    /**
     * Génère un extrait de description limité
     */
    private function generateExcerpt(?string $description): string
    {
        if (empty($description)) {
            return '';
        }

        // Limiter à DESCRIPTION_EXCERPT_LENGTH caractères
        if (mb_strlen($description) <= self::DESCRIPTION_EXCERPT_LENGTH) {
            return $description;
        }

        // Couper au dernier espace pour ne pas couper un mot
        $excerpt = mb_substr($description, 0, self::DESCRIPTION_EXCERPT_LENGTH);
        $lastSpace = mb_strrpos($excerpt, ' ');

        if ($lastSpace !== false) {
            $excerpt = mb_substr($excerpt, 0, $lastSpace);
        }

        return $excerpt.'...';
    }

    /**
     * Récupère l'URL de la photo principale
     */
    private function getMainPhotoUrl(Place $place): ?string
    {
        $mainPhoto = $place->photos->first();

        if (! $mainPhoto) {
            return null;
        }

        // Utiliser le chemin complet avec filename
        return $mainPhoto->medium_url;
    }

    /**
     * Prépare les tags avec leurs traductions
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
}
