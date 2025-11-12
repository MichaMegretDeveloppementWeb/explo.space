<?php

namespace Tests\Feature\Web\Pages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function privacy_page_is_accessible_in_french(): void
    {
        $response = $this->get('/fr/politique-confidentialite');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function privacy_page_is_accessible_in_english(): void
    {
        $response = $this->get('/en/privacy-policy');

        $response->assertStatus(200);
    }
}
