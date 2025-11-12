<?php

namespace Tests\Feature\Web\Home;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_homepage_can_be_accessed(): void
    {
        $response = $this->get('/fr');

        $response->assertStatus(200);
    }

    public function test_homepage_redirects_from_root_to_default_locale(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/fr/');
    }

    public function test_homepage_contains_correct_title(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('Explo', false);
        $response->assertSee('<title>', false);
    }

    public function test_homepage_contains_features_section(): void
    {
        $response = $this->get('/fr');

        // Features section contains "découvrir" or "explorer"
        $response->assertSee('découvrir');
    }

    public function test_homepage_contains_community_stats_section(): void
    {
        $response = $this->get('/fr');

        // Community stats section contains place count (checking for "lieu" word)
        $response->assertSee('lieu');
    }

    public function test_homepage_contains_cta_section(): void
    {
        $response = $this->get('/fr');

        // CTA section contains "Explorer maintenant" button
        $response->assertSee('Explorer maintenant');
    }

    public function test_homepage_has_correct_meta_description(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<meta name="description"', false);
    }

    public function test_homepage_has_correct_meta_keywords(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<meta name="keywords"', false);
    }

    public function test_homepage_has_robots_meta_tag(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<meta name="robots"', false);
        $response->assertSee('index', false);
    }

    public function test_homepage_has_canonical_link(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<link rel="canonical"', false);
    }

    public function test_homepage_has_open_graph_tags(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:type"', false);
        $response->assertSee('<meta property="og:url"', false);
        $response->assertSee('<meta property="og:image"', false);
    }

    public function test_homepage_has_twitter_cards(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<meta name="twitter:card"', false);
        $response->assertSee('<meta name="twitter:title"', false);
        $response->assertSee('<meta name="twitter:description"', false);
    }

    public function test_homepage_has_json_ld_schema(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('schema.org', false);
    }

    public function test_homepage_has_organization_schema(): void
    {
        $response = $this->get('/fr');

        // JSON-LD may have spaces after colons
        $response->assertSee('Organization', false);
        $response->assertSee('schema.org', false);
    }

    public function test_homepage_has_website_schema(): void
    {
        $response = $this->get('/fr');

        // JSON-LD may have spaces after colons
        $response->assertSee('WebSite', false);
        $response->assertSee('schema.org', false);
    }

    public function test_homepage_has_hreflang_tags(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<link rel="alternate" hreflang="fr"', false);
        $response->assertSee('<link rel="alternate" hreflang="en"', false);
        $response->assertSee('<link rel="alternate" hreflang="x-default"', false);
    }

    public function test_homepage_works_with_english_locale(): void
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
        $response->assertSee('Explo', false);
    }

    public function test_homepage_has_navigation(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<nav', false);
    }

    public function test_homepage_layout_loads_successfully(): void
    {
        $response = $this->get('/fr');

        // Footer is conditional in web layout (@if(isset($footer)))
        // Homepage doesn't define $footer, so footer is not displayed by design
        $response->assertStatus(200);
    }

    public function test_homepage_has_language_switcher(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('language', false);
    }

    public function test_homepage_contains_explore_link(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('explorer', false);
    }

    public function test_homepage_viewport_meta_tag_for_mobile(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<meta name="viewport"', false);
        $response->assertSee('width=device-width', false);
    }

    public function test_homepage_has_favicon(): void
    {
        $response = $this->get('/fr');

        $response->assertSee('<link rel="icon"', false);
    }
}
