<?php

namespace App\Domain\Seo\DTO;

/**
 * Data Transfer Object pour les données SEO
 */
class SeoData
{
    /**
     * @param  array<int, array<string, string>>  $hreflangs
     * @param  array<int, array<string, mixed>>  $jsonLdSchemas
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $keywords,
        public readonly string $robots,
        public readonly string $canonical,

        // Open Graph
        public readonly string $ogTitle,
        public readonly string $ogDescription,
        public readonly string $ogType,
        public readonly string $ogUrl,
        public readonly string $ogSiteName,
        public readonly string $ogImage,
        public readonly ?string $ogImageAlt,
        public readonly string $ogLocale,
        /** @var array<string, string> */
        public readonly array $ogLocaleAlternates,

        // Twitter Cards
        public readonly string $twitterCard,
        public readonly string $twitterTitle,
        public readonly string $twitterDescription,
        public readonly string $twitterImage,
        public readonly ?string $twitterImageAlt = null,
        public readonly ?string $twitterSite = null,
        public readonly ?string $twitterCreator = null,

        // Hreflangs
        public readonly array $hreflangs = [],

        // JSON-LD schemas (collection de schémas)
        public readonly array $jsonLdSchemas = [],

        // Breadcrumbs
        /** @var array<string, mixed> */
        public readonly array $breadcrumbs = [],

        // Geo (pour les lieux)
        public readonly ?float $geo_latitude = null,
        public readonly ?float $geo_longitude = null,
    ) {}
}
