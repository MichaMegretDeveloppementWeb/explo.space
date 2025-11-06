<?php

namespace App\Domain\Seo\Services;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\SeoData;

class SeoService
{
    /**
     * Génère les données SEO complètes en utilisant une stratégie
     */
    public function generate(SeoStrategyInterface $strategy): SeoData
    {
        $metaTags = $strategy->getMetaTagsData();
        $openGraph = $strategy->getOpenGraphData();
        $twitterCards = $strategy->getTwitterCardsData();
        $jsonLd = $strategy->getJsonLdData();
        $hreflang = $strategy->getHreflangData();

        return new SeoData(
            title: $metaTags->title,
            description: $metaTags->description,
            keywords: $metaTags->keywords,
            robots: $metaTags->robots,
            canonical: $metaTags->canonical,
            ogTitle: $openGraph->title,
            ogDescription: $openGraph->description,
            ogType: $openGraph->type,
            ogUrl: $openGraph->url,
            ogSiteName: $openGraph->siteName,
            ogImage: $openGraph->image,
            ogImageAlt: $openGraph->imageAlt,
            ogLocale: $openGraph->locale,
            ogLocaleAlternates: $openGraph->localeAlternates,
            twitterCard: $twitterCards->card,
            twitterTitle: $twitterCards->title,
            twitterDescription: $twitterCards->description,
            twitterImage: $twitterCards->image,
            twitterImageAlt: $twitterCards->imageAlt,
            twitterSite: $twitterCards->site,
            twitterCreator: $twitterCards->creator,
            hreflangs: $hreflang,
            jsonLdSchemas: $jsonLd,
            geo_latitude: $metaTags->geoLatitude,
            geo_longitude: $metaTags->geoLongitude,
        );
    }
}
