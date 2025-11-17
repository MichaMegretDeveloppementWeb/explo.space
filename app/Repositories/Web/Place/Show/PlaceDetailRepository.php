<?php

namespace App\Repositories\Web\Place\Show;

use App\Contracts\Repositories\Web\Place\Show\PlaceDetailRepositoryInterface;
use App\Models\Place;

class PlaceDetailRepository implements PlaceDetailRepositoryInterface
{
    /**
     * Récupérer un lieu par son slug et sa locale avec toutes ses relations
     *
     * Optimisations :
     * - Eager loading de toutes les relations nécessaires
     * - Filtrage des traductions par locale et status published
     * - Tri des photos par sort_order
     * - Une seule requête principale
     *
     * @param  string  $slug  Le slug du lieu dans la locale donnée
     * @param  string  $locale  La locale de recherche (fr, en)
     * @return Place|null Le lieu avec ses relations ou null si non trouvé
     */
    public function getPlaceBySlug(string $slug, string $locale): ?Place
    {

        // Charger le lieu avec toutes ses relations optimisées
        /** @var Place|null $place */
        $place = Place::query()
            ->withWhereHas('translations', function ($query) use ($slug, $locale) {
                $query->where('slug', $slug)->where('locale', $locale)->where('status', 'published');
            })
            ->with([
                // Admin qui a créé le lieu
                'admin',

                // Photos triées par sort_order avec leurs traductions
                'photos' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'photos.translations' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                },

                // Tags avec leurs traductions dans la locale donnée
                'tags.translations' => function ($query) use ($locale) {
                    $query->where('locale', $locale)
                        ->where('status', 'published');
                },
            ])
            ->first();

        return $place;
    }
}
