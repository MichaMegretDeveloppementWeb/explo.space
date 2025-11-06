<?php

namespace App\Domain\Seo\DTO;

/**
 * Value Object pour les données Twitter Cards
 */
class TwitterCardsData
{
    public function __construct(
        public readonly string $card,
        public readonly string $title,
        public readonly string $description,
        public readonly string $url,
        public readonly ?string $image = null,
        public readonly ?string $imageAlt = null,
        public readonly ?string $site = null,
        public readonly ?string $creator = null,
    ) {}
}
