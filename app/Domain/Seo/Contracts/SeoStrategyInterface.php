<?php

namespace App\Domain\Seo\Contracts;

use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;

interface SeoStrategyInterface
{
    /**
     * Génère les données de meta tags
     */
    public function getMetaTagsData(): MetaTagsData;

    /**
     * Génère les données Open Graph
     */
    public function getOpenGraphData(): OpenGraphData;

    /**
     * Génère les données Twitter Cards
     */
    public function getTwitterCardsData(): TwitterCardsData;

    /**
     * Génère les données JSON-LD
     *
     * @return array<int, array<string, mixed>>
     */
    public function getJsonLdData(): array;

    /**
     * Génère les données hreflang
     *
     * @return array<int, array<string, string>>
     */
    public function getHreflangData(): array;
}
