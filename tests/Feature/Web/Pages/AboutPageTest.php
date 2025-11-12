<?php

namespace Tests\Feature\Web\Pages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_about_page_can_be_accessed_in_french(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertStatus(200);
    }

    public function test_about_page_can_be_accessed_in_english(): void
    {
        $response = $this->get('/en/about');

        $response->assertStatus(200);
    }

    public function test_about_page_contains_breadcrumb(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Accueil');
        $response->assertSee('À propos');
    }

    public function test_about_page_contains_hero_section(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Découvrir l\'univers spatial');
    }

    public function test_about_page_contains_mission_section(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Notre mission');
        $response->assertSee('id="mission"', false);
    }

    public function test_about_page_contains_how_it_works_section(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Comment ça marche');
        $response->assertSee('id="how-it-works"', false);
    }

    public function test_about_page_contains_contribute_section(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Contribuer');
        $response->assertSee('id="contribute"', false);
    }

    public function test_about_page_contains_philosophy_section(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Notre philosophie');
        $response->assertSee('id="philosophy"', false);
    }

    public function test_about_page_has_correct_meta_tags(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="keywords"', false);
        $response->assertSee('<meta name="robots"', false);
    }

    public function test_about_page_has_canonical_link(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<link rel="canonical"', false);
    }

    public function test_about_page_has_open_graph_tags(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:type"', false);
        $response->assertSee('<meta property="og:url"', false);
    }

    public function test_about_page_has_twitter_cards(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<meta name="twitter:card"', false);
        $response->assertSee('<meta name="twitter:title"', false);
        $response->assertSee('<meta name="twitter:description"', false);
    }

    public function test_about_page_has_hreflang_tags(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<link rel="alternate" hreflang="fr"', false);
        $response->assertSee('<link rel="alternate" hreflang="en"', false);
        $response->assertSee('<link rel="alternate" hreflang="x-default"', false);
    }

    public function test_about_page_has_json_ld_schema(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('schema.org', false);
    }

    public function test_about_page_has_breadcrumb_schema(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('schema.org', false);
    }

    public function test_about_page_english_has_correct_translations(): void
    {
        $response = $this->get('/en/about');

        $response->assertSee('Discover the space universe');
        $response->assertSee('Our mission');
        $response->assertSee('How it works');
        $response->assertSee('Contribute');
        $response->assertSee('Our philosophy');
    }

    public function test_about_page_has_footer(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<footer', false);
    }

    public function test_about_page_has_navigation(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('<nav', false);
    }

    public function test_about_page_contains_cta_to_propose_place(): void
    {
        $response = $this->get('/fr/a-propos');

        $response->assertSee('Proposer un lieu maintenant');
    }
}
