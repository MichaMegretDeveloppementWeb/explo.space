<?php

namespace App\Domain\Seo\Actions;

use App\Domain\Seo\DTO\SeoData;
use App\Domain\Seo\Services\SeoService;
use App\Domain\Seo\Strategies\SeoStrategyResolver;

class SeoBuilderAction
{
    public function __construct(
        private SeoStrategyResolver $strategyResolver,
        private SeoService $seoService,
    ) {}

    /**
     * Exécuter la génération des données SEO
     *
     * @param  string  $pageType  Type de page (homepage, explore, place_show, etc.)
     * @param  array<string, mixed>  $context  Données contextuelles optionnelles pour la stratégie
     * @return SeoData Données SEO générées
     */
    public function execute(string $pageType, array $context = []): SeoData
    {
        $strategy = $this->strategyResolver->resolve($pageType, $context);

        return $this->seoService->generate($strategy);
    }
}
