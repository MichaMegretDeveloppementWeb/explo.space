<?php

namespace App\Repositories\Admin\Place\Detail;

use App\Contracts\Repositories\Admin\Place\Detail\PlaceDetailRepositoryInterface;
use App\Models\Place;

class PlaceDetailRepository implements PlaceDetailRepositoryInterface
{
    /**
     * Récupérer un lieu avec toutes ses relations pour affichage détail
     */
    public function getPlaceWithRelations(int $placeId): ?Place
    {
        return Place::with([
            // Toutes les traductions (publiées et brouillons)
            'translations' => fn ($q) => $q->orderBy('locale'),

            // Admin qui a créé/géré le lieu
            'admin:id,name,email',

            // PlaceRequest d'origine (si le lieu est issu d'une proposition)
            'placeRequest:id,title,status,created_at',

            // Tags avec leurs traductions
            'tags' => fn ($q) => $q->where('is_active', true),
            'tags.translations' => fn ($q) => $q->orderBy('locale'),

            // Catégories avec leurs traductions
            'categories' => fn ($q) => $q->where('is_active', true),
            'categories.translations' => fn ($q) => $q->orderBy('locale'),

            // Photos triées (principale en premier)
            'photos' => fn ($q) => $q->orderByDesc('is_main')
                ->orderBy('created_at'),
        ])
            ->find($placeId);
    }

    /**
     * Vérifier si un lieu existe
     */
    public function exists(int $placeId): bool
    {
        return Place::where('id', $placeId)->exists();
    }
}
