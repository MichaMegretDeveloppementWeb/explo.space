<?php

namespace Tests\Unit\Domain\Seo\Strategies;

use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;
use App\Domain\Seo\Strategies\PlaceRequestSeoStrategy;
use Tests\TestCase;

class PlaceRequestSeoStrategyTest extends TestCase
{
    private PlaceRequestSeoStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('fr');
        $this->strategy = new PlaceRequestSeoStrategy;
    }

    public function test_it_returns_correct_meta_tags_data(): void
    {
        // Act
        $metaTags = $this->strategy->getMetaTagsData();

        // Assert
        $this->assertInstanceOf(MetaTagsData::class, $metaTags);
        $this->assertNotEmpty($metaTags->title);
        $this->assertStringContainsString(config('app.name'), $metaTags->title);
        $this->assertNotEmpty($metaTags->description);
        $this->assertLessThanOrEqual(165, strlen($metaTags->description));
        $this->assertNotEmpty($metaTags->keywords);
        $this->assertEquals('index,follow', $metaTags->robots);
        $this->assertStringContainsString('/fr/', $metaTags->canonical);
    }

    public function test_it_returns_correct_open_graph_data(): void
    {
        // Act
        $openGraph = $this->strategy->getOpenGraphData();

        // Assert
        $this->assertInstanceOf(OpenGraphData::class, $openGraph);
        $this->assertNotEmpty($openGraph->title);
        $this->assertStringContainsString(config('app.name'), $openGraph->title);
        $this->assertNotEmpty($openGraph->description);
        $this->assertEquals('website', $openGraph->type);
        $this->assertEquals(config('app.name'), $openGraph->siteName);
        $this->assertStringContainsString('http', $openGraph->image);
        $this->assertNotEmpty($openGraph->imageAlt);
        $this->assertEquals('fr_FR', $openGraph->locale);
        $this->assertIsArray($openGraph->localeAlternates);
    }

    public function test_it_returns_correct_twitter_cards_data(): void
    {
        // Act
        $twitterCards = $this->strategy->getTwitterCardsData();

        // Assert
        $this->assertInstanceOf(TwitterCardsData::class, $twitterCards);
        $this->assertEquals('summary_large_image', $twitterCards->card);
        $this->assertNotEmpty($twitterCards->title);
        $this->assertNotEmpty($twitterCards->description);
        $this->assertStringContainsString('http', $twitterCards->image);
    }

    public function test_it_returns_correct_json_ld_data(): void
    {
        // Act
        $jsonLd = $this->strategy->getJsonLdData();

        // Assert
        $this->assertIsArray($jsonLd);
        $this->assertNotEmpty($jsonLd);
        $this->assertArrayHasKey('@context', $jsonLd[0]);
        $this->assertEquals('https://schema.org', $jsonLd[0]['@context']);
        $this->assertArrayHasKey('@graph', $jsonLd[0]);
        $this->assertIsArray($jsonLd[0]['@graph']);
    }

    public function test_it_includes_webpage_schema_in_json_ld(): void
    {
        // Act
        $jsonLd = $this->strategy->getJsonLdData();
        $schemas = $jsonLd[0]['@graph'];

        // Assert
        $webPage = collect($schemas)->firstWhere('@type', 'WebPage');
        $this->assertNotNull($webPage);
        $this->assertArrayHasKey('name', $webPage);
        $this->assertArrayHasKey('description', $webPage);
        $this->assertArrayHasKey('url', $webPage);
        $this->assertEquals('fr', $webPage['inLanguage']);
        $this->assertArrayHasKey('isPartOf', $webPage);
    }

    public function test_it_returns_correct_hreflang_data(): void
    {
        // Act
        $hreflangs = $this->strategy->getHreflangData();

        // Assert
        $this->assertIsArray($hreflangs);
        $this->assertNotEmpty($hreflangs);

        // Should have fr, en, and x-default
        $this->assertGreaterThanOrEqual(3, count($hreflangs));

        // Check structure
        foreach ($hreflangs as $hreflang) {
            $this->assertArrayHasKey('hreflang', $hreflang);
            $this->assertArrayHasKey('href', $hreflang);
            $this->assertStringContainsString('http', $hreflang['href']);
        }

        // Check x-default exists
        $xDefault = collect($hreflangs)->firstWhere('hreflang', 'x-default');
        $this->assertNotNull($xDefault);
    }

    public function test_it_truncates_description_to_160_characters(): void
    {
        // Act
        $metaTags = $this->strategy->getMetaTagsData();

        // Assert
        $this->assertLessThanOrEqual(165, strlen($metaTags->description));
    }

    public function test_it_builds_title_with_site_name(): void
    {
        // Act
        $metaTags = $this->strategy->getMetaTagsData();

        // Assert
        $separator = config('seo.title_separator');
        $this->assertStringContainsString($separator, $metaTags->title);
        $this->assertStringContainsString(config('app.name'), $metaTags->title);
    }

    public function test_it_does_not_duplicate_site_name_if_already_present(): void
    {
        // Note: This test validates the buildFullTitle logic
        // If the title already contains the site name, it shouldn't be added again

        $metaTags = $this->strategy->getMetaTagsData();
        $siteName = config('app.name');

        // Count occurrences of site name in title
        $count = substr_count($metaTags->title, $siteName);

        // Should only appear once
        $this->assertEquals(1, $count);
    }

    public function test_it_uses_correct_og_locale_for_french(): void
    {
        // Arrange
        app()->setLocale('fr');
        $strategy = new PlaceRequestSeoStrategy;

        // Act
        $openGraph = $strategy->getOpenGraphData();

        // Assert
        $this->assertEquals('fr_FR', $openGraph->locale);
    }

    public function test_it_uses_correct_og_locale_for_english(): void
    {
        // Arrange
        app()->setLocale('en');
        $strategy = new PlaceRequestSeoStrategy;

        // Act
        $openGraph = $strategy->getOpenGraphData();

        // Assert
        $this->assertEquals('en_US', $openGraph->locale);
    }

    public function test_it_builds_locale_alternates_excluding_current_locale(): void
    {
        // Arrange
        app()->setLocale('fr');
        $strategy = new PlaceRequestSeoStrategy;

        // Act
        $openGraph = $strategy->getOpenGraphData();

        // Assert
        $this->assertArrayNotHasKey('fr', $openGraph->localeAlternates);
        $this->assertArrayHasKey('en', $openGraph->localeAlternates);
        $this->assertEquals('en_US', $openGraph->localeAlternates['en']);
    }

    public function test_it_generates_canonical_url_with_locale(): void
    {
        // Arrange
        app()->setLocale('fr');
        $strategy = new PlaceRequestSeoStrategy;

        // Act
        $metaTags = $strategy->getMetaTagsData();

        // Assert
        $this->assertStringContainsString('/fr/', $metaTags->canonical);
        $this->assertStringContainsString('proposer-lieu', $metaTags->canonical);
    }

    public function test_it_uses_robots_index_follow_directive(): void
    {
        // Act
        $metaTags = $this->strategy->getMetaTagsData();

        // Assert
        $this->assertEquals('index,follow', $metaTags->robots);
    }

    public function test_it_uses_correct_image_paths(): void
    {
        // Act
        $openGraph = $this->strategy->getOpenGraphData();
        $twitterCards = $this->strategy->getTwitterCardsData();

        // Assert
        $this->assertStringStartsWith('http', $openGraph->image);
        $this->assertStringStartsWith('http', $twitterCards->image);
        $this->assertStringContainsString('.jpg', $openGraph->image);
    }

    public function test_it_strips_html_tags_from_description(): void
    {
        // Act
        $metaTags = $this->strategy->getMetaTagsData();

        // Assert
        $this->assertStringNotContainsString('<', $metaTags->description);
        $this->assertStringNotContainsString('>', $metaTags->description);
    }
}
