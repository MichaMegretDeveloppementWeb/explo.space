<?php

namespace App\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use InvalidArgumentException;

class SeoStrategyResolver
{
    /**
     * Résout la stratégie SEO appropriée pour un type de page
     *
     * @param  string  $pageType  Type de page
     * @param  array<string, mixed>  $context  Données contextuelles pour la stratégie
     */
    public function resolve(string $pageType, array $context = []): SeoStrategyInterface
    {
        return match ($pageType) {
            'homepage' => app(HomepageSeoStrategy::class),
            'explore' => app(ExploreSeoStrategy::class),
            'place-request' => app(PlaceRequestSeoStrategy::class),
            'place_show' => new PlaceShowSeoStrategy($context),
            default => throw new InvalidArgumentException("SEO strategy not found for page type: {$pageType}"),
        };
    }

    /**
     * Liste tous les types de pages supportés
     *
     * @return array<string>
     */
    public function getSupportedPageTypes(): array
    {
        return [
            'homepage',
            'explore',
            'place-request',
            'place_show',
        ];
    }

    /**
     * Vérifie si un type de page est supporté
     */
    public function isSupported(string $pageType): bool
    {
        return in_array($pageType, $this->getSupportedPageTypes());
    }
}
