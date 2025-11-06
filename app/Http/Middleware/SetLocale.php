<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $forcedLocale = null): Response
    {
        $supported = config('locales.supported');
        $cookieName = config('locales.cookie_name');
        $default = config('locales.default');

        $detectedLocale = $this->detectBrowserLocale($request);

        // Déterminer la locale
        $locale = $forcedLocale
            ?? $request->route('locale')
            ?? $request->cookie($cookieName)
            ?? $detectedLocale
            ?? $default;

        // Vérifier que la locale est supportée
        if (! in_array($locale, $supported, true)) {
            $locale = $default;
        }

        // Appliquer la locale
        app()->setLocale($locale);
        $request->attributes->set('current_locale', $locale);

        // Sauvegarder dans cookie si différent
        $response = $next($request);

        if ($request->cookie($cookieName) !== $locale) {
            $response = $response->withCookie(cookie(
                $cookieName,
                $locale,
                config('locales.cookie_lifetime')
            ));
        }

        return $response;
    }

    private function detectBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (! $acceptLanguage) {
            return null;
        }

        $supported = config('locales.supported');
        $preferred = [];

        // Parser Accept-Language header
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', $lang);
            $locale = trim($parts[0]);
            $quality = 1.0;

            if (isset($parts[1]) && strpos($parts[1], 'q=') === 0) {
                $quality = floatval(substr($parts[1], 2));
            }

            // Extraire code langue principal (fr-FR -> fr)
            $mainLocale = substr($locale, 0, 2);
            if (in_array($mainLocale, $supported)) {
                $preferred[$mainLocale] = $quality;
            }
        }

        if (empty($preferred)) {
            return null;
        }

        // Retourner la langue avec la plus haute priorité
        arsort($preferred);

        return array_key_first($preferred);
    }
}
