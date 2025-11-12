<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap XML
     */
    public function index(): Response
    {
        $baseUrl = config('app.url');
        $locales = config('locales.supported');

        $xml = $this->generateSitemap($baseUrl, $locales);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Generate sitemap content
     */
    private function generateSitemap(string $baseUrl, array $locales): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n\n";

        // Static pages configuration
        $staticPages = $this->getStaticPages();

        foreach ($staticPages as $page) {
            foreach ($locales as $locale) {
                $url = $baseUrl . $page['path'][$locale];

                $xml .= "    <!-- {$page['name']} {$locale} -->\n";
                $xml .= "    <url>\n";
                $xml .= "        <loc>{$url}</loc>\n";

                // Add alternate language links for all locales
                foreach ($locales as $altLocale) {
                    $altUrl = $baseUrl . $page['path'][$altLocale];
                    $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"{$altLocale}\" href=\"{$altUrl}\" />\n";
                }

                // Add x-default (French as default)
                $defaultUrl = $baseUrl . $page['path']['fr'];
                $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$defaultUrl}\" />\n";

                $xml .= "        <changefreq>{$page['changefreq']}</changefreq>\n";
                $xml .= "        <priority>{$page['priority']}</priority>\n";
                $xml .= "    </url>\n\n";
            }
        }

        // Dynamic places - Get all published places with their translations
        $this->addPlacesToSitemap($xml, $baseUrl, $locales);

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get static pages configuration
     */
    private function getStaticPages(): array
    {
        return [
            [
                'name' => 'Homepage',
                'path' => [
                    'fr' => '/fr/',
                    'en' => '/en/',
                ],
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'name' => 'Explorer',
                'path' => [
                    'fr' => '/fr/explorer',
                    'en' => '/en/explore',
                ],
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'name' => 'Propose place',
                'path' => [
                    'fr' => '/fr/proposer-lieu',
                    'en' => '/en/propose-place',
                ],
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'name' => 'About',
                'path' => [
                    'fr' => '/fr/a-propos',
                    'en' => '/en/about',
                ],
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'name' => 'Contact',
                'path' => [
                    'fr' => '/fr/contact',
                    'en' => '/en/contact',
                ],
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'name' => 'Legal',
                'path' => [
                    'fr' => '/fr/mentions-legales',
                    'en' => '/en/legal-notice',
                ],
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'name' => 'Privacy',
                'path' => [
                    'fr' => '/fr/politique-confidentialite',
                    'en' => '/en/privacy-policy',
                ],
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ];
    }

    /**
     * Add places to sitemap dynamically
     */
    private function addPlacesToSitemap(string &$xml, string $baseUrl, array $locales): void
    {
        // Get all places with their published translations
        $places = Place::with(['translations' => function ($query) {
            $query->where('status', 'published');
        }])->get();

        foreach ($places as $place) {
            // Get all published translations for this place
            $publishedTranslations = $place->translations->where('status', 'published');

            // Skip if no published translations
            if ($publishedTranslations->isEmpty()) {
                continue;
            }

            // For each locale, add a URL entry if translation exists
            foreach ($locales as $locale) {
                $translation = $publishedTranslations->firstWhere('locale', $locale);

                // Skip if no translation for this locale
                if (! $translation) {
                    continue;
                }

                // Build the URL
                $segment = $locale === 'fr' ? 'lieux' : 'places';
                $url = "{$baseUrl}/{$locale}/{$segment}/{$translation->slug}";

                $xml .= "    <!-- Place: {$translation->title} ({$locale}) -->\n";
                $xml .= "    <url>\n";
                $xml .= "        <loc>{$url}</loc>\n";

                // Add alternate language links for all available translations
                foreach ($publishedTranslations as $altTranslation) {
                    $altLocale = $altTranslation->locale;
                    $altSegment = $altLocale === 'fr' ? 'lieux' : 'places';
                    $altUrl = "{$baseUrl}/{$altLocale}/{$altSegment}/{$altTranslation->slug}";
                    $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"{$altLocale}\" href=\"{$altUrl}\" />\n";
                }

                // Add x-default (use French if available, otherwise first translation)
                $defaultTranslation = $publishedTranslations->firstWhere('locale', 'fr') ?? $publishedTranslations->first();
                $defaultSegment = $defaultTranslation->locale === 'fr' ? 'lieux' : 'places';
                $defaultUrl = "{$baseUrl}/{$defaultTranslation->locale}/{$defaultSegment}/{$defaultTranslation->slug}";
                $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$defaultUrl}\" />\n";

                // Add lastmod from place updated_at
                if ($place->updated_at) {
                    $lastmod = $place->updated_at->toIso8601String();
                    $xml .= "        <lastmod>{$lastmod}</lastmod>\n";
                }

                $xml .= "        <changefreq>weekly</changefreq>\n";
                $xml .= "        <priority>0.7</priority>\n";
                $xml .= "    </url>\n\n";
            }
        }
    }
}
