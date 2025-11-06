<?php

namespace App\Domain\Seo\DTO;

/**
 * Value Object pour les données Open Graph
 */
class OpenGraphData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $type,
        public readonly string $url,
        public readonly string $siteName,
        public readonly string $image,
        public readonly ?string $imageAlt = null,
        public readonly string $locale = 'fr_FR',
        /** @var array<string, string> */
        public readonly array $localeAlternates = [],
    ) {}
}
