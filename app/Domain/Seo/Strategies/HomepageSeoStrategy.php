<?php

namespace App\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;
use Illuminate\Support\Str;

class HomepageSeoStrategy implements SeoStrategyInterface
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
            canonical: url('/'.$this->locale),
        );
    }

    public function getOpenGraphData(): OpenGraphData
    {
        return new OpenGraphData(
            title: $this->buildFullTitle($this->pageData['seo']['title']),
            description: $this->truncateDescription($this->pageData['seo']['description']),
            type: $this->pageData['seo']['og']['type'],
            url: url('/'.$this->locale),
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
            url: url('/'.$this->locale),
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
            $this->buildWebsiteSchema(),
            $this->buildOrganizationSchema(),
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
        // Pour la homepage, nous construisons les URLs des langues alternatives
        $hreflangs = [];
        $supportedLocales = config('locales.supported', ['fr', 'en']);
        $defaultLocale = config('locales.default', 'fr');

        foreach ($supportedLocales as $locale) {
            $url = url('/'.$locale);
            $hreflangs[] = [
                'hreflang' => $locale,
                'href' => $url,
            ];
        }

        // x-default vers la langue par défaut
        $hreflangs[] = [
            'hreflang' => 'x-default',
            'href' => url('/'.config('locales.default', 'fr')),
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

    /**
     * @return array<string, mixed>
     */
    private function buildWebsiteSchema(): array
    {
        $data = [
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'description' => $this->pageData['seo']['json_ld']['website']['description'],
            'url' => url('/'),
            'inLanguage' => $this->locale,
        ];

        // Ajouter SearchAction si c'est la homepage
        if (request()->is('/') || request()->is($this->locale.'/')) {
            $searchAction = $this->pageData['seo']['json_ld']['website']['search_action'] ?? null;
            if ($searchAction) {
                $data['potentialAction'] = $searchAction;
            }
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOrganizationSchema(): array
    {
        $org = $this->pageData['seo']['json_ld']['organization'];

        return [
            '@type' => 'Organization',
            'name' => $org['name'],
            'legalName' => $org['legal_name'],
            'description' => $org['description'],
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => url(config('seo.images.logo')),
                'width' => 300,
                'height' => 300,
            ],
            'foundingDate' => $org['founding_date'],
            'founders' => $org['founders'],
            'contactPoint' => $org['contact_point'],
            'sameAs' => array_filter($org['same_as']),
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => config('company.country'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getPageData(): array
    {
        return __('web/pages/home');
    }
}
