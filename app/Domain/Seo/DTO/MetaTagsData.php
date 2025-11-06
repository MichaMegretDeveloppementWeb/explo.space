<?php

namespace App\Domain\Seo\DTO;

/**
 * Value Object pour les données de meta tags
 */
class MetaTagsData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $keywords,
        public readonly string $robots,
        public readonly string $canonical,
        public readonly ?float $geoLatitude = null,
        public readonly ?float $geoLongitude = null,
    ) {}
}
