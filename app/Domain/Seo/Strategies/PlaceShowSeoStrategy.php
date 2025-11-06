<?php

namespace App\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;
use App\DTO\Web\Place\PlaceDetailDTO;
use App\Support\LocaleUrl;
use Illuminate\Support\Str;

class PlaceShowSeoStrategy implements SeoStrategyInterface
{
    private string $locale;

    private PlaceDetailDTO $place;

    /**
     * @param  array<string, mixed>  $context  Données contextuelles (place, locale)
     */
    public function __construct(array $context = [])
    {
        $this->place = $context['place'];
        $this->locale = $context['locale'] ?? app()->getLocale();
    }

    public function getMetaTagsData(): MetaTagsData
    {
        $title = $this->buildFullTitle($this->place->title);
        $description = $this->truncateDescription($this->place->description);
        $keywords = $this->buildKeywords();

        return new MetaTagsData(
            title: $title,
            description: $description,
            keywords: $keywords,
            robots: $this->buildRobotsContent(),
            canonical: $this->getCanonicalUrl(),
        );
    }

    public function getOpenGraphData(): OpenGraphData
    {
        return new OpenGraphData(
            title: $this->buildFullTitle($this->place->title),
            description: $this->truncateDescription($this->place->description),
            type: 'place',
            url: $this->getCanonicalUrl(),
            siteName: config('app.name'),
            image: $this->place->mainPhotoUrl ?? $this->getDefaultImage('og'),
            imageAlt: $this->place->title,
            locale: $this->getOgLocale(),
            localeAlternates: $this->buildLocaleAlternates(),
        );
    }

    public function getTwitterCardsData(): TwitterCardsData
    {
        return new TwitterCardsData(
            card: 'summary_large_image',
            title: $this->buildFullTitle($this->place->title),
            description: $this->truncateDescription($this->place->description),
            url: $this->getCanonicalUrl(),
            image: $this->place->mainPhotoUrl ?? $this->getDefaultImage('twitter'),
            imageAlt: $this->place->title,
            site: config('company.social.twitter_url'),
            creator: config('company.social.twitter_name'),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getJsonLdData(): array
    {
        $schemas = [
            $this->buildBreadcrumbListSchema(),
            $this->buildPlaceSchema(),
        ];

        return [
            [
                '@context' => 'https://schema.org',
                '@graph' => $schemas,
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getHreflangData(): array
    {
        $hreflangs = [];
        $supportedLocales = config('locales.supported', ['fr', 'en']);

        foreach ($supportedLocales as $locale) {
            $url = url('/'.$locale.'/'.LocaleUrl::segment('places', $locale).'/'.$this->place->slug);
            $hreflangs[] = [
                'hreflang' => $locale,
                'href' => $url,
            ];
        }

        // x-default vers la langue par défaut
        $defaultLocale = config('locales.default', 'fr');
        $hreflangs[] = [
            'hreflang' => 'x-default',
            'href' => url('/'.$defaultLocale.'/'.LocaleUrl::segment('places', $defaultLocale).'/'.$this->place->slug),
        ];

        return $hreflangs;
    }

    /* ------------PRIVATE METHODS------------- */

    private function buildFullTitle(string $title): string
    {
        $separator = config('seo.title_separator');
        $suffix = config('app.name');

        if (str_contains($title, $suffix)) {
            return $title;
        }

        return $title.$separator.$suffix;
    }

    private function buildRobotsContent(): string
    {
        $directives = ['index', 'follow', 'archive', 'snippet', 'imageindex'];

        return implode(',', $directives);
    }

    private function truncateDescription(string $description, ?int $maxLength = null): string
    {
        $maxLength = $maxLength ?: 160;

        return Str::limit(strip_tags($description), $maxLength);
    }

    private function getDefaultImage(string $type): string
    {
        $imagePath = config("seo.images.default_{$type}") ?? config('seo.images.default_og');

        return url($imagePath);
    }

    private function getOgLocale(): string
    {
        return $this->locale === 'fr' ? 'fr_FR' : 'en_US';
    }

    /**
     * @return array<string, string>
     */
    private function buildLocaleAlternates(): array
    {
        $localeAlternates = [];
        $supportedLocales = config('locales.supported', ['fr', 'en']);

        foreach ($supportedLocales as $locale) {
            if ($locale !== $this->locale) {
                $ogLocale = $locale === 'fr' ? 'fr_FR' : 'en_US';
                $localeAlternates[$locale] = $ogLocale;
            }
        }

        return $localeAlternates;
    }

    private function buildKeywords(): string
    {
        $keywords = [];

        // Ajouter le titre du lieu
        $keywords[] = $this->place->title;

        // Ajouter tous les tags
        foreach ($this->place->tags as $tag) {
            $keywords[] = $tag['name'];
        }

        // Ajouter des mots-clés génériques traduits
        $genericKeywords = __('web/pages/place-show.seo.keywords', [], $this->locale);
        if (is_array($genericKeywords)) {
            $keywords = array_merge($keywords, $genericKeywords);
        }

        return implode(', ', array_unique($keywords));
    }

    private function getCanonicalUrl(): string
    {
        return url('/'.$this->locale.'/'.LocaleUrl::segment('places', $this->locale).'/'.$this->place->slug);
    }

    /**
     * Construire le schéma BreadcrumbList JSON-LD
     *
     * @return array<string, mixed>
     */
    private function buildBreadcrumbListSchema(): array
    {
        $homeLabel = __('web/common.navigation.home');
        $exploreLabel = __('web/common.navigation.explore');

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => $homeLabel,
                    'item' => url('/'.$this->locale),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $exploreLabel,
                    'item' => url('/'.$this->locale.'/'.LocaleUrl::segment('explore', $this->locale)),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $this->place->title,
                    'item' => $this->getCanonicalUrl(),
                ],
            ],
        ];
    }

    /**
     * Construire le schéma Place JSON-LD (TouristAttraction ou LandmarksOrHistoricalBuildings)
     *
     * @return array<string, mixed>
     */
    private function buildPlaceSchema(): array
    {
        $schema = [
            '@type' => 'TouristAttraction',
            'name' => $this->place->title,
            'description' => $this->truncateDescription($this->place->description, 250),
            'url' => $this->getCanonicalUrl(),
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $this->place->latitude,
                'longitude' => $this->place->longitude,
            ],
        ];

        // Ajouter l'adresse si disponible
        if ($this->place->address) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->place->address,
            ];
        }

        // Ajouter la photo principale si disponible
        if ($this->place->mainPhotoUrl) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $this->place->mainPhotoUrl,
                'description' => $this->place->title,
            ];
        }

        return $schema;
    }
}
