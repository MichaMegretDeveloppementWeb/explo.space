<?php

namespace Tests\Unit\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\MetaTagsData;
use App\Domain\Seo\DTO\OpenGraphData;
use App\Domain\Seo\DTO\TwitterCardsData;
use App\Domain\Seo\Strategies\HomepageSeoStrategy;
use Tests\TestCase;

class HomepageSeoStrategyTest extends TestCase
{
    private HomepageSeoStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
        $this->strategy = new HomepageSeoStrategy;
    }

    public function test_strategy_implements_interface(): void
    {
        $this->assertInstanceOf(SeoStrategyInterface::class, $this->strategy);
    }

    public function test_get_meta_tags_data_returns_correct_structure(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertInstanceOf(MetaTagsData::class, $result);
    }

    public function test_get_meta_tags_data_includes_title(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertNotEmpty($result->title);
        $this->assertIsString($result->title);
        $this->assertStringContainsString('Explo', $result->title);
    }

    public function test_get_meta_tags_data_includes_description(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertNotEmpty($result->description);
        $this->assertIsString($result->description);
        // Extended limit: 160 is recommended but 250 is acceptable for SEO
        $this->assertLessThanOrEqual(250, strlen($result->description));
    }

    public function test_get_meta_tags_data_includes_keywords(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertNotEmpty($result->keywords);
        $this->assertIsString($result->keywords);
    }

    public function test_get_meta_tags_data_includes_robots(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertNotEmpty($result->robots);
        $this->assertIsString($result->robots);
        $this->assertStringContainsString('index', $result->robots);
    }

    public function test_get_meta_tags_data_includes_canonical(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertNotEmpty($result->canonical);
        $this->assertIsString($result->canonical);
        $this->assertStringStartsWith('http', $result->canonical);
    }

    public function test_get_meta_tags_data_geo_coordinates_are_null_for_homepage(): void
    {
        $result = $this->strategy->getMetaTagsData();

        $this->assertNull($result->geoLatitude);
        $this->assertNull($result->geoLongitude);
    }

    public function test_get_open_graph_data_returns_correct_structure(): void
    {
        $result = $this->strategy->getOpenGraphData();

        $this->assertInstanceOf(OpenGraphData::class, $result);
    }

    public function test_get_open_graph_data_includes_all_required_properties(): void
    {
        $result = $this->strategy->getOpenGraphData();

        $this->assertNotEmpty($result->title);
        $this->assertNotEmpty($result->description);
        $this->assertEquals('website', $result->type);
        $this->assertNotEmpty($result->url);
        $this->assertNotEmpty($result->siteName);
        $this->assertNotEmpty($result->image);
    }

    public function test_get_open_graph_data_includes_locale(): void
    {
        $result = $this->strategy->getOpenGraphData();

        $this->assertNotEmpty($result->locale);
        $this->assertStringContainsString('_', $result->locale);
        $this->assertMatchesRegularExpression('/^[a-z]{2}_[A-Z]{2}$/', $result->locale);
    }

    public function test_get_open_graph_data_includes_locale_alternates(): void
    {
        $result = $this->strategy->getOpenGraphData();

        $this->assertIsArray($result->localeAlternates);
        $this->assertNotEmpty($result->localeAlternates);
    }

    public function test_get_twitter_cards_data_returns_correct_structure(): void
    {
        $result = $this->strategy->getTwitterCardsData();

        $this->assertInstanceOf(TwitterCardsData::class, $result);
    }

    public function test_get_twitter_cards_data_includes_all_required_properties(): void
    {
        $result = $this->strategy->getTwitterCardsData();

        $this->assertNotEmpty($result->card);
        $this->assertNotEmpty($result->title);
        $this->assertNotEmpty($result->description);
        $this->assertNotEmpty($result->url);
    }

    public function test_get_twitter_cards_data_card_type_is_valid(): void
    {
        $result = $this->strategy->getTwitterCardsData();

        $validCardTypes = ['summary', 'summary_large_image', 'app', 'player'];
        $this->assertContains($result->card, $validCardTypes);
    }

    public function test_get_json_ld_data_returns_array(): void
    {
        $result = $this->strategy->getJsonLdData();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function test_get_json_ld_data_includes_website_schema(): void
    {
        $result = $this->strategy->getJsonLdData();

        // Schemas are in @graph
        $schemas = $result[0]['@graph'] ?? [];

        $hasWebsiteSchema = false;
        foreach ($schemas as $schema) {
            if (isset($schema['@type']) && $schema['@type'] === 'WebSite') {
                $hasWebsiteSchema = true;
                $this->assertArrayHasKey('url', $schema);
                break;
            }
        }

        $this->assertTrue($hasWebsiteSchema, 'JSON-LD data should include a WebSite schema');
    }

    public function test_get_json_ld_data_includes_organization_schema(): void
    {
        $result = $this->strategy->getJsonLdData();

        // Schemas are in @graph
        $schemas = $result[0]['@graph'] ?? [];

        $hasOrganizationSchema = false;
        foreach ($schemas as $schema) {
            if (isset($schema['@type']) && $schema['@type'] === 'Organization') {
                $hasOrganizationSchema = true;
                $this->assertArrayHasKey('name', $schema);
                $this->assertArrayHasKey('url', $schema);
                break;
            }
        }

        $this->assertTrue($hasOrganizationSchema, 'JSON-LD data should include an Organization schema');
    }

    public function test_get_hreflang_data_returns_array(): void
    {
        $result = $this->strategy->getHreflangData();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function test_get_hreflang_data_includes_all_supported_locales(): void
    {
        $result = $this->strategy->getHreflangData();

        $locales = array_column($result, 'hreflang');

        $this->assertContains('fr', $locales);
        $this->assertContains('en', $locales);
    }

    public function test_get_hreflang_data_includes_x_default(): void
    {
        $result = $this->strategy->getHreflangData();

        $locales = array_column($result, 'hreflang');

        $this->assertContains('x-default', $locales);
    }

    public function test_get_hreflang_data_structure_is_correct(): void
    {
        $result = $this->strategy->getHreflangData();

        foreach ($result as $hreflang) {
            $this->assertArrayHasKey('hreflang', $hreflang);
            $this->assertArrayHasKey('href', $hreflang);
            $this->assertIsString($hreflang['hreflang']);
            $this->assertIsString($hreflang['href']);
            $this->assertStringStartsWith('http', $hreflang['href']);
        }
    }

    public function test_strategy_works_with_english_locale(): void
    {
        app()->setLocale('en');
        $strategy = new HomepageSeoStrategy;

        $metaTags = $strategy->getMetaTagsData();
        $openGraph = $strategy->getOpenGraphData();
        $twitterCards = $strategy->getTwitterCardsData();

        $this->assertInstanceOf(MetaTagsData::class, $metaTags);
        $this->assertInstanceOf(OpenGraphData::class, $openGraph);
        $this->assertInstanceOf(TwitterCardsData::class, $twitterCards);
    }

    public function test_all_data_methods_return_non_null_values(): void
    {
        $this->assertNotNull($this->strategy->getMetaTagsData());
        $this->assertNotNull($this->strategy->getOpenGraphData());
        $this->assertNotNull($this->strategy->getTwitterCardsData());
        $this->assertNotNull($this->strategy->getJsonLdData());
        $this->assertNotNull($this->strategy->getHreflangData());
    }
}
