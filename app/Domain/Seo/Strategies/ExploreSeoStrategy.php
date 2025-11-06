<?php

namespace App\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;
use App\Support\LocaleUrl;
use Illuminate\Support\Str;

class ExploreSeoStrategy implements SeoStrategyInterface
{
    private string $locale;

    /** @var array<string, mixed> */
    private array $pageData;

    public function __construct()
    {
        $this->locale = app()->getLocale();
        $this->pageData = $this->getPageData();
    }

    public function getMetaTagsData(): MetaTagsData
    {
        $title = $this->buildFullTitle($this->pageData['seo']['title']);
        $description = $this->truncateDescription($this->pageData['seo']['description'], 200);

        return new MetaTagsData(
            title: $title,
            description: $description,
            keywords: $this->pageData['seo']['keywords'],
            robots: $this->buildRobotsContent(),
            canonical: $this->buildCanonicalUrl(),
        );
    }

    public function getOpenGraphData(): OpenGraphData
    {
        return new OpenGraphData(
            title: $this->buildFullTitle($this->pageData['seo']['title']),
            description: $this->truncateDescription($this->pageData['seo']['description']),
            type: $this->pageData['seo']['og']['type'],
            url: $this->buildCanonicalUrl(),
            siteName: config('app.name'),
            image: $this->getDefaultImage('og'),
            imageAlt: $this->pageData['seo']['og']['image_alt'],
            locale: $this->getOgLocale(),
            localeAlternates: $this->buildLocaleAlternates(),
        );
    }

    public function getTwitterCardsData(): TwitterCardsData
    {
        return new TwitterCardsData(
            card: $this->pageData['seo']['twitter']['card'],
            title: $this->buildFullTitle($this->pageData['seo']['title']),
            description: $this->truncateDescription($this->pageData['seo']['description']),
            url: $this->buildCanonicalUrl(),
            image: $this->getDefaultImage('twitter'),
            imageAlt: $this->pageData['seo']['og']['image_alt'],
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
            $this->buildWebPageSchema(),
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
            $segment = LocaleUrl::segment('explore', $locale);
            $url = url("/{$locale}/{$segment}");

            $hreflangs[] = [
                'hreflang' => $locale,
                'href' => $url,
            ];
        }

        // x-default vers la langue par défaut
        $defaultLocale = config('locales.default', 'fr');
        $defaultSegment = LocaleUrl::segment('explore', $defaultLocale);
        $hreflangs[] = [
            'hreflang' => 'x-default',
            'href' => url("/{$defaultLocale}/{$defaultSegment}"),
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
        $directives = [];
        $directives[] = 'index';
        $directives[] = 'follow';
        $directives[] = 'archive';
        $directives[] = 'snippet';
        $directives[] = 'imageindex';

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

    private function buildCanonicalUrl(): string
    {
        $segment = LocaleUrl::segment('explore', $this->locale);

        return url("/{$this->locale}/{$segment}");
    }

    /**
     * @return array<string, mixed>
     */
    private function buildWebPageSchema(): array
    {
        $websiteData = $this->pageData['seo']['json_ld']['website'];

        $data = [
            '@type' => 'WebPage',
            'name' => $this->pageData['seo']['title'],
            'description' => $websiteData['description'],
            'url' => $this->buildCanonicalUrl(),
            'inLanguage' => $this->locale,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => config('app.name'),
                'url' => url('/'),
            ],
            'about' => [
                '@type' => 'Thing',
                'name' => 'Exploration spatiale',
                'description' => 'Lieux emblématiques de la conquête spatiale et de l\'exploration de l\'univers',
            ],
        ];

        // Ajouter SearchAction pour la page d'exploration
        if (isset($websiteData['search_action'])) {
            $data['potentialAction'] = $websiteData['search_action'];
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function getPageData(): array
    {
        return __('web/pages/explore');
    }
}
