<?php

namespace Tests\Unit\Exceptions\Place;

use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use Tests\TestCase;

class InvalidPlaceFiltersExceptionTest extends TestCase
{
    public function test_exception_stores_user_message(): void
    {
        $exception = new InvalidPlaceFiltersException('Invalid input', 'test_key');

        $this->assertEquals('Invalid input', $exception->getUserMessage());
    }

    public function test_exception_stores_filter_key(): void
    {
        $exception = new InvalidPlaceFiltersException('Invalid input', 'radius');

        $this->assertEquals('radius', $exception->getFilterKey());
    }

    public function test_invalid_radius_factory_creates_exception(): void
    {
        $exception = InvalidPlaceFiltersException::invalidRadius(50000, 200000, 1500000);

        $this->assertInstanceOf(InvalidPlaceFiltersException::class, $exception);
        $this->assertEquals('radius', $exception->getFilterKey());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_invalid_mode_factory_creates_exception(): void
    {
        $exception = InvalidPlaceFiltersException::invalidMode('invalid_mode');

        $this->assertInstanceOf(\App\Exceptions\Admin\Place\InvalidPlaceFiltersException::class, $exception);
        $this->assertEquals('mode', $exception->getFilterKey());
        $this->assertStringContainsString('invalid_mode', $exception->getMessage());
    }

    public function test_invalid_coordinates_factory_for_latitude(): void
    {
        $exception = InvalidPlaceFiltersException::invalidCoordinates('latitude', 95.0);

        $this->assertInstanceOf(InvalidPlaceFiltersException::class, $exception);
        $this->assertEquals('latitude', $exception->getFilterKey());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_invalid_coordinates_factory_for_longitude(): void
    {
        $exception = InvalidPlaceFiltersException::invalidCoordinates('longitude', -185.0);

        $this->assertInstanceOf(InvalidPlaceFiltersException::class, $exception);
        $this->assertEquals('longitude', $exception->getFilterKey());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_invalid_tags_factory_creates_exception(): void
    {
        $invalidSlugs = ['nonexistent-tag', 'another-invalid'];
        $exception = \App\Exceptions\Admin\Place\InvalidPlaceFiltersException::invalidTags($invalidSlugs);

        $this->assertInstanceOf(\App\Exceptions\Admin\Place\InvalidPlaceFiltersException::class, $exception);
        $this->assertEquals('tags', $exception->getFilterKey());
        $this->assertNotEmpty($exception->getUserMessage());
    }
}
