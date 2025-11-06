<?php

namespace Tests\Unit\Exceptions\Web\Place\PreviewModal;

use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use Tests\TestCase;

class PlaceNotFoundExceptionTest extends TestCase
{
    public function test_exception_can_be_instantiated_with_place_id(): void
    {
        $exception = new PlaceNotFoundException(42);

        $this->assertInstanceOf(PlaceNotFoundException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_exception_message_includes_place_id(): void
    {
        $placeId = 123;
        $exception = new PlaceNotFoundException($placeId);

        $this->assertStringContainsString('123', $exception->getMessage());
        $this->assertStringContainsString('not found', $exception->getMessage());
    }

    public function test_exception_has_404_code_by_default(): void
    {
        $exception = new PlaceNotFoundException(42);

        $this->assertEquals(404, $exception->getCode());
    }

    public function test_exception_can_be_thrown_and_caught(): void
    {
        $this->expectException(PlaceNotFoundException::class);
        $this->expectExceptionMessage('Place with ID 99 not found');

        throw new PlaceNotFoundException(99);
    }

    public function test_exception_message_mentions_preview_modal_context(): void
    {
        $exception = new PlaceNotFoundException(42);

        $this->assertStringContainsString('preview modal', $exception->getMessage());
    }
}
