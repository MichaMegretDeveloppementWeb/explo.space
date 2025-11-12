<?php

namespace Tests\Feature\Web\Pages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_contact_page_can_be_accessed_in_french(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertStatus(200);
    }

    public function test_contact_page_can_be_accessed_in_english(): void
    {
        $response = $this->get('/en/contact');

        $response->assertStatus(200);
    }

    public function test_contact_page_contains_breadcrumb(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('Accueil');
        $response->assertSee('Contact');
    }

    public function test_contact_page_contains_hero_section(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('Une question ?');
        $response->assertSee('Contactez-nous');
    }

    public function test_contact_page_contains_contact_info(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('Informations de contact');
        $response->assertSee('contact@explo.space');
    }

    public function test_contact_page_contains_contact_form(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('Envoyez-nous un message');
        $response->assertSeeLivewire('web.contact.contact-form');
    }

    public function test_contact_page_has_correct_meta_tags(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="keywords"', false);
        $response->assertSee('<meta name="robots"', false);
    }

    public function test_contact_page_has_canonical_link(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<link rel="canonical"', false);
    }

    public function test_contact_page_has_open_graph_tags(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:type"', false);
        $response->assertSee('<meta property="og:url"', false);
    }

    public function test_contact_page_has_twitter_cards(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<meta name="twitter:card"', false);
        $response->assertSee('<meta name="twitter:title"', false);
        $response->assertSee('<meta name="twitter:description"', false);
    }

    public function test_contact_page_has_hreflang_tags(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<link rel="alternate" hreflang="fr"', false);
        $response->assertSee('<link rel="alternate" hreflang="en"', false);
        $response->assertSee('<link rel="alternate" hreflang="x-default"', false);
    }

    public function test_contact_page_has_json_ld_schema(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('schema.org', false);
    }

    public function test_contact_page_has_breadcrumb_schema(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('schema.org', false);
    }

    public function test_contact_page_has_contact_page_schema(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('ContactPage', false);
    }

    public function test_contact_page_english_has_correct_translations(): void
    {
        $response = $this->get('/en/contact');

        $response->assertSee('Have a question?');
        $response->assertSee('Contact us');
        $response->assertSee('Contact information');
    }

    public function test_contact_page_has_footer(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<footer', false);
    }

    public function test_contact_page_has_navigation(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('<nav', false);
    }

    public function test_contact_page_has_recaptcha_script(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('recaptcha/api.js', false);
    }
}
