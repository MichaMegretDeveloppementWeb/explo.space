<?php

namespace Tests\Unit\Exceptions\Web\Place\PreviewModal;

use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;
use Tests\TestCase;

class PlaceTranslationNotFoundExceptionTest extends TestCase
{
    public function test_exception_can_be_instantiated_with_place_id_and_locale(): void
    {
        $exception = new PlaceTranslationNotFoundException(42, 'en');

        $this->assertInstanceOf(PlaceTranslationNotFoundException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_exception_message_includes_place_id_and_locale(): void
    {
        $placeId = 123;
        $locale = 'fr';
        $exception = new PlaceTranslationNotFoundException($placeId, $locale);

        $this->assertStringContainsString('123', $exception->getMessage());
        $this->assertStringContainsString('fr', $exception->getMessage());
        $this->assertStringContainsString('not found', $exception->getMessage());
    }

    public function test_exception_has_404_code_by_default(): void
    {
        $exception = new PlaceTranslationNotFoundException(42, 'en');

        $this->assertEquals(404, $exception->getCode());
    }

    public function test_exception_can_be_thrown_and_caught(): void
    {
        $this->expectException(PlaceTranslationNotFoundException::class);
        $this->expectExceptionMessage('Translation for place ID 99 not found for locale \'en\'');

        throw new PlaceTranslationNotFoundException(99, 'en');
    }

    public function test_exception_message_mentions_published_status(): void
    {
        $exception = new PlaceTranslationNotFoundException(42, 'en');

        $this->assertStringContainsString('published', $exception->getMessage());
    }

    public function test_exception_works_with_different_locales(): void
    {
        $exceptionFr = new PlaceTranslationNotFoundException(1, 'fr');
        $exceptionEn = new PlaceTranslationNotFoundException(1, 'en');
        $exceptionEs = new PlaceTranslationNotFoundException(1, 'es');

        $this->assertStringContainsString('fr', $exceptionFr->getMessage());
        $this->assertStringContainsString('en', $exceptionEn->getMessage());
        $this->assertStringContainsString('es', $exceptionEs->getMessage());
    }
}
