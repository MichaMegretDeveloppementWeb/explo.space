<?php

use App\Support\LocaleUrl;

if (! function_exists('localRoute')) {
    /**
     * Génère une route localisée pour la langue courante ou spécifiée
     *
     * @param  array<string, mixed>  $parameters
     */
    function localRoute(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return route("$name.$locale", $parameters);
    }
}

if (! function_exists('switchLocalRoute')) {
    /**
     * Génère l'URL de la page courante dans une autre langue
     * Conserve les paramètres de route ET les query parameters
     */
    function switchLocalRoute(?string $targetLocale = null): string
    {
        $targetLocale = $targetLocale ?? config('locales.default');
        $currentRoute = request()->route()->getName();
        $routeParams = request()->route()->parameters();
        $queryParams = request()->query();

        return LocaleUrl::switchRoute($currentRoute, $routeParams, $queryParams, $targetLocale);
    }
}

if (! function_exists('currentLocale')) {
    /**
     * Retourne la locale courante
     */
    function currentLocale(): string
    {
        return app()->getLocale();
    }
}

if (! function_exists('isCurrentLocale')) {
    /**
     * Vérifie si la locale donnée est la locale courante
     */
    function isCurrentLocale(string $locale): bool
    {
        return app()->getLocale() === $locale;
    }
}

if (! function_exists('localizedSegment')) {
    /**
     * Retourne le segment traduit pour une clé donnée
     */
    function localizedSegment(string $key, ?string $locale = null): string
    {
        return LocaleUrl::segment($key, $locale);
    }
}

if (! function_exists('availableLocales')) {
    /**
     * Retourne la liste des locales supportées
     *
     * @return array<int, string>
     */
    function availableLocales(): array
    {
        return config('locales.supported', []);
    }
}

if (! function_exists('isRtlLocale')) {
    /**
     * Vérifie si la locale courante est RTL (right-to-left)
     * Utile pour futures langues arabes/hébraïques
     */
    function isRtlLocale(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        $rtlLocales = ['ar', 'he', 'fa', 'ur']; // Langues RTL communes

        return in_array($locale, $rtlLocales);
    }
}

if (! function_exists('localizedUrl')) {
    /**
     * Génère une URL complète avec préfixe de locale
     */
    function localizedUrl(string $path = '', ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $path = ltrim($path, '/');

        return url("/{$locale}/".$path);
    }
}

if (! function_exists('getLocaleDirection')) {
    /**
     * Retourne la direction du texte pour la locale (ltr ou rtl)
     */
    function getLocaleDirection(?string $locale = null): string
    {
        return isRtlLocale($locale) ? 'rtl' : 'ltr';
    }
}
