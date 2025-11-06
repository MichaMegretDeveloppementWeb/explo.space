<?php

namespace Tests\Unit\Exceptions\Web\Place\Show;

use App\Exceptions\Web\Place\Show\PlaceTranslationNotFoundException;
use Tests\TestCase;

class PlaceTranslationNotFoundExceptionTest extends TestCase
{
    public function test_exception_can_be_created_with_place_id_and_locale(): void
    {
        $exception = new PlaceTranslationNotFoundException(123, 'fr');

        $this->assertInstanceOf(PlaceTranslationNotFoundException::class, $exception);
    }

    public function test_exception_message_contains_place_id_and_locale(): void
    {
        $exception = new PlaceTranslationNotFoundException(456, 'en');
        $message = $exception->getMessage();

        $this->assertStringContainsString('456', $message);
        $this->assertStringContainsString('en', $message);
        $this->assertStringContainsString('Translation not found', $message);
    }

    public function test_exception_code_is_404(): void
    {
        $exception = new PlaceTranslationNotFoundException(1, 'fr');

        $this->assertEquals(404, $exception->getCode());
    }

    public function test_context_returns_place_id_and_locale(): void
    {
        $exception = new PlaceTranslationNotFoundException(789, 'fr');
        $context = $exception->context();

        $this->assertIsArray($context);
        $this->assertEquals(789, $context['place_id']);
        $this->assertEquals('fr', $context['locale']);
    }

    public function test_exception_works_with_different_locales(): void
    {
        $exceptionFr = new PlaceTranslationNotFoundException(1, 'fr');
        $exceptionEn = new PlaceTranslationNotFoundException(2, 'en');

        $this->assertStringContainsString('fr', $exceptionFr->getMessage());
        $this->assertStringContainsString('en', $exceptionEn->getMessage());
    }

    public function test_exception_handles_large_place_ids(): void
    {
        $exception = new PlaceTranslationNotFoundException(999999999, 'fr');
        $context = $exception->context();

        $this->assertEquals(999999999, $context['place_id']);
    }

    public function test_exception_message_format_is_consistent(): void
    {
        $exception = new PlaceTranslationNotFoundException(123, 'fr');
        $message = $exception->getMessage();

        $this->assertMatchesRegularExpression('/Translation not found.*123.*fr/', $message);
    }

    public function test_context_place_id_is_integer(): void
    {
        $exception = new PlaceTranslationNotFoundException(42, 'en');
        $context = $exception->context();

        $this->assertIsInt($context['place_id']);
    }
}
