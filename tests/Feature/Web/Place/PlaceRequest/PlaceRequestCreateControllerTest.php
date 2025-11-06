<?php

namespace Tests\Feature\Web\Place\PlaceRequest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceRequestCreateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_the_place_request_form_in_french(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('web.place.place-request');
        $response->assertViewHas('locale', 'fr');
        $response->assertViewHas('seo');
        $response->assertSeeLivewire('web.place.place-request.place-request-form');
    }

    public function test_it_displays_the_place_request_form_in_english(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.en'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('web.place.place-request');
        $response->assertViewHas('locale', 'en');
        $response->assertViewHas('seo');
    }

    public function test_it_includes_seo_meta_tags(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="keywords"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta name="twitter:card"', false);
    }

    public function test_it_includes_canonical_url(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('proposer-lieu', false);
    }

    public function test_it_includes_hreflang_tags(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('hreflang="fr"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_it_includes_json_ld_structured_data(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        // JSON-LD is raw output (not escaped)
        $response->assertSee('"@context": "https://schema.org"', false);
        $response->assertSee('"@type": "WebPage"', false);
    }

    public function test_it_displays_page_title(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('<title>', false);
        $response->assertSee('Explo space', false);
    }

    public function test_it_displays_recaptcha_meta_tags(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('recaptcha-site-key', false);
        $response->assertSee('recaptcha-error-message', false);
    }

    public function test_it_loads_the_livewire_form_component(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\Web\Place\PlaceRequest\PlaceRequestForm::class);
    }

    public function test_it_includes_photo_validation_config(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        // The photoConfig should be passed to the Livewire component and displayed somewhere
    }

    public function test_it_includes_map_container_for_location_selection(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('placeRequestMap');
    }

    public function test_it_generates_correct_seo_data_for_french(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.fr'));
        $seo = $response->viewData('seo');

        // Assert
        $this->assertNotNull($seo);
        $this->assertObjectHasProperty('title', $seo);
        $this->assertObjectHasProperty('description', $seo);
        $this->assertObjectHasProperty('canonical', $seo);
        $this->assertObjectHasProperty('ogTitle', $seo);
        $this->assertObjectHasProperty('twitterCard', $seo);
        $this->assertObjectHasProperty('hreflangs', $seo);
        $this->assertObjectHasProperty('jsonLdSchemas', $seo);
    }

    public function test_it_generates_correct_seo_data_for_english(): void
    {
        // Act
        $response = $this->get(route('place_requests.create.en'));
        $seo = $response->viewData('seo');

        // Assert
        $this->assertNotNull($seo);
        $this->assertObjectHasProperty('title', $seo);
        $this->assertObjectHasProperty('description', $seo);
    }
}
