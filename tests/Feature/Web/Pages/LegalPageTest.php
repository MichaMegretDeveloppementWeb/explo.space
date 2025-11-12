<?php

namespace Tests\Feature\Web\Pages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_page_can_be_accessed_in_french(): void
    {
        $response = $this->get('/fr/mentions-legales');

        $response->assertStatus(200);
    }

    public function test_legal_page_can_be_accessed_in_english(): void
    {
        $response = $this->get('/en/legal-notice');

        $response->assertStatus(200);
    }
}
