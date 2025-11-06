<?php

namespace Tests\Unit\Exceptions\Web\Place\Show;

use App\Exceptions\Web\Place\Show\PlaceNotFoundException;
use Tests\TestCase;

class PlaceNotFoundExceptionTest extends TestCase
{
    public function test_exception_can_be_created_with_slug_and_locale(): void
    {
        $exception = new PlaceNotFoundException('test-place', 'fr');

        $this->assertInstanceOf(PlaceNotFoundException::class, $exception);
    }

    public function test_exception_message_contains_slug_and_locale(): void
    {
        $exception = new PlaceNotFoundException('test-place', 'en');
        $message = $exception->getMessage();

        $this->assertStringContainsString('test-place', $message);
        $this->assertStringContainsString('en', $message);
        $this->assertStringContainsString('Place not found', $message);
    }

    public function test_exception_code_is_404(): void
    {
        $exception = new PlaceNotFoundException('test-place', 'fr');

        $this->assertEquals(404, $exception->getCode());
    }

    public function test_context_returns_slug_and_locale(): void
    {
        $exception = new PlaceNotFoundException('centre-spatial-kennedy', 'fr');
        $context = $exception->context();

        $this->assertIsArray($context);
        $this->assertEquals('centre-spatial-kennedy', $context['slug']);
        $this->assertEquals('fr', $context['locale']);
    }

    public function test_exception_works_with_different_locales(): void
    {
        $exceptionFr = new PlaceNotFoundException('lieu-test', 'fr');
        $exceptionEn = new PlaceNotFoundException('test-place', 'en');

        $this->assertStringContainsString('fr', $exceptionFr->getMessage());
        $this->assertStringContainsString('en', $exceptionEn->getMessage());
    }

    public function test_exception_handles_special_characters_in_slug(): void
    {
        $slug = "café-de-l'espace";
        $exception = new PlaceNotFoundException($slug, 'fr');
        $context = $exception->context();

        $this->assertEquals($slug, $context['slug']);
    }

    public function test_exception_message_format_is_consistent(): void
    {
        $exception = new PlaceNotFoundException('test-slug', 'fr');
        $message = $exception->getMessage();

        $this->assertMatchesRegularExpression('/Place not found.*test-slug.*fr/', $message);
    }
}
