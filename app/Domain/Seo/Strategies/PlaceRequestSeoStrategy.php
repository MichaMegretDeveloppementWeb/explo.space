<?php

namespace App\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;
use Illuminate\Support\Str;

class PlaceRequestSeoStrategy implements SeoStrategyInterface
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
        $description = $this->truncateDescription($this->pageData['seo']['description']);

        return new MetaTagsData(
            title: $title,
            description: $description,
            keywords: $this->pageData['seo']['keywords'],
            robots: $this->buildRobotsContent(),
            canonical: $this->getCurrentUrl(),
        );
    }

    public function getOpenGraphData(): OpenGraphData
    {
        return new OpenGraphData(
            title: $this->buildFullTitle($this->pageData['seo']['title']),
            description: $this->truncateDescription($this->pageData['seo']['description']),
            type: $this->pageData['seo']['og']['type'],
            url: $this->getCurrentUrl(),
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
            url: $this->getCurrentUrl(),
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
            $segment = __('web/url.segments.propose_place', [], $locale);
            $url = url('/'.$locale.'/'.$segment);
            $hreflangs[] = [
                'hreflang' => $locale,
                'href' => $url,
            ];
        }

        // x-default vers la langue par défaut
        $defaultLocale = config('locales.default', 'fr');
        $defaultSegment = __('web/url.segments.propose_place', [], $defaultLocale);
        $hreflangs[] = [
            'hreflang' => 'x-default',
            'href' => url('/'.$defaultLocale.'/'.$defaultSegment),
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

    /**
     * @return array<string, mixed>
     */
    private function buildWebPageSchema(): array
    {
        return [
            '@type' => 'WebPage',
            'name' => $this->pageData['seo']['title'],
            'description' => $this->truncateDescription($this->pageData['seo']['description']),
            'url' => $this->getCurrentUrl(),
            'inLanguage' => $this->locale,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => config('app.name'),
                'url' => url('/'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getPageData(): array
    {
        return __('web/pages/place-request');
    }

    private function getCurrentUrl(): string
    {
        $segment = __('web/url.segments.propose_place');

        return url('/'.$this->locale.'/'.$segment);
    }
}
