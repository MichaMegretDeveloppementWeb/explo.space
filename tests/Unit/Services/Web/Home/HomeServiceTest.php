<?php

namespace Tests\Unit\Services\Web\Home;

use Tests\TestCase;

/**
 * HomeService n'existe pas dans l'application.
 *
 * La logique métier de la page d'accueil est gérée par :
 * - App\Domain\Seo\Strategies\HomepageSeoStrategy pour le SEO
 * - Les composants Livewire directement pour les interactions
 * - Les repositories pour les données
 *
 * Ce fichier de test est présent pour maintenir la cohérence de l'architecture de tests,
 * mais il ne contient aucun test car il n'y a pas de service correspondant à tester.
 *
 * Si un HomeService est créé à l'avenir, les tests devront être ajoutés ici.
 */
class HomeServiceTest extends TestCase
{
    public function test_home_service_does_not_exist(): void
    {
        $this->assertFalse(
            class_exists('App\Services\Web\Home\HomeService'),
            'HomeService should not exist. Logic is in HomepageSeoStrategy and Livewire components.'
        );
    }
}
