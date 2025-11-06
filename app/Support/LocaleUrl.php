<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class LocaleUrl
{
    /**
     * Obtenir le segment traduit pour une clé donnée
     */
    public static function segment(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        $translation = trans("web/url.segments.{$key}", [], $locale);

        // Si la traduction n'existe pas, trans() retourne la clé complète
        // On veut retourner juste la clé originale comme fallback
        if ($translation === "web/url.segments.{$key}") {
            return $key;
        }

        return $translation;
    }

    /**
     * Générer le nom de route avec locale
     */
    public static function routeName(string $base, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return "$base.$locale";
    }

    /**
     * Générer une URL de route avec locale
     *
     * @param  array<string, mixed>  $params
     */
    public static function route(string $base, array $params = [], ?string $locale = null): string
    {
        return route(self::routeName($base, $locale), $params);
    }

    /**
     * Changer de langue en conservant le contexte de la route actuelle
     *
     * @param  array<string, mixed>  $routeParams  Paramètres de route (segments dynamiques comme {slug})
     * @param  array<string, mixed>  $queryParams  Paramètres de requête (après ? dans l'URL)
     */
    public static function switchRoute(string $currentRoute, array $routeParams, array $queryParams, string $targetLocale): string
    {
        // Extraire le nom de base de la route (sans la locale)
        $baseName = preg_replace('/\.(fr|en)$/', '', $currentRoute);

        // Cas spécial : page de détail de lieu (places.show)
        // Il faut récupérer le slug traduit depuis la base de données
        if ($baseName === 'places.show' && isset($routeParams['slug'])) {

            $currentLocale = app()->getLocale();
            $currentSlug = $routeParams['slug'];

            // Récupérer la traduction correspondante
            $placeTranslation = \App\Models\PlaceTranslation::query()
                ->where('slug', $currentSlug)
                ->where('locale', $currentLocale)
                ->where('status', 'published')
                ->first();

            if ($placeTranslation) {
                // Récupérer la traduction dans la langue cible
                $targetTranslation = \App\Models\PlaceTranslation::query()
                    ->where('place_id', $placeTranslation->place_id)
                    ->where('locale', $targetLocale)
                    ->where('status', 'published')
                    ->first();

                // Si la traduction cible existe, utiliser son slug
                if ($targetTranslation) {
                    $routeParams['slug'] = $targetTranslation->slug;
                } else {
                    // Si pas de traduction dans la langue cible, rediriger vers la page d'accueil
                    return url('/'.$targetLocale);
                }
            }
        }

        // Générer l'URL de base avec les paramètres de route (slug potentiellement traduit)
        $url = self::route($baseName, $routeParams, $targetLocale);

        // Ajouter les query parameters s'il y en a
        if (! empty($queryParams)) {
            $url .= '?'.http_build_query($queryParams);
        }

        return $url;
    }
}
