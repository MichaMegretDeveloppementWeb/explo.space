<?php

namespace App\Contracts\Repositories\Web\Place\Show;

use App\Models\Place;

interface PlaceDetailRepositoryInterface
{
    /**
     * Récupérer un lieu par son slug et sa locale avec toutes ses relations
     *
     * Eager loading appliqué :
     * - translations (filtrées par locale et status published)
     * - photos (triées par sort_order)
     * - tags.translations (filtrées par locale et status published)
     * - admin (utilisateur ayant créé le lieu)
     *
     * @param  string  $slug  Le slug du lieu dans la locale donnée
     * @param  string  $locale  La locale de recherche (fr, en)
     * @return Place|null Le lieu avec ses relations ou null si non trouvé
     */
    public function getPlaceBySlug(string $slug, string $locale): ?Place;
}
