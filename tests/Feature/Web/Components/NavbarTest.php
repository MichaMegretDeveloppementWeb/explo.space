<?php

namespace Tests\Feature\Web\Components;

use Tests\TestCase;

class NavbarTest extends TestCase
{
    public function test_navbar_contains_correct_navigation_links_fr(): void
    {
        $response = $this->get('/fr/');

        // Vérifier que les 4 liens corrects sont présents
        $response->assertSee('Accueil');
        $response->assertSee('Explorer');
        $response->assertSee('À propos');
        $response->assertSee('Contact');
    }

    public function test_navbar_contains_correct_navigation_links_en(): void
    {
        $response = $this->get('/en/');

        // Vérifier que les 4 liens corrects sont présents
        $response->assertSee('Home');
        $response->assertSee('Explore');
        $response->assertSee('About');
        $response->assertSee('Contact');
    }

    public function test_navbar_links_are_functional(): void
    {
        // Tester que les liens pointent vers les bonnes routes
        $response = $this->get('/fr/');

        $response->assertSee(localRoute('home'));
        $response->assertSee(localRoute('explore'));
        $response->assertSee(localRoute('about'));
        $response->assertSee(localRoute('contact'));
    }

    public function test_navbar_primary_action_is_present(): void
    {
        $response = $this->get('/fr/');

        // Vérifier que le bouton "Proposer un lieu" est présent
        $response->assertSee('Proposer un lieu');
    }
}
