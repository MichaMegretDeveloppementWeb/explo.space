<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\GeocodingException;
use Tests\TestCase;

class GeocodingExceptionTest extends TestCase
{
    public function test_exception_stores_user_message(): void
    {
        $exception = new GeocodingException('User message', 'general', 'Technical message');

        $this->assertStringContainsString('User message', $exception->getUserMessage());
    }

    public function test_exception_stores_error_type(): void
    {
        $exception = new GeocodingException('User message', 'connection', 'Technical message');

        $this->assertEquals('connection', $exception->getErrorType());
    }

    public function test_no_results_factory_creates_exception(): void
    {
        $exception = GeocodingException::noResults('Paris, France');

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('no_results', $exception->getErrorType());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_connection_failed_factory_creates_exception(): void
    {
        $exception = GeocodingException::connectionFailed('Connection timeout');

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('connection', $exception->getErrorType());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_rate_limited_factory_creates_exception(): void
    {
        $exception = GeocodingException::rateLimited(60);

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('rate_limit', $exception->getErrorType());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_service_error_factory_handles_500_status(): void
    {
        $exception = GeocodingException::serviceError(500, 'Internal server error');

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('service', $exception->getErrorType());
        $this->assertNotEmpty($exception->getUserMessage());
    }

    public function test_service_error_factory_handles_429_status(): void
    {
        $exception = GeocodingException::serviceError(429, 'Too many requests');

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('service', $exception->getErrorType());
    }

    public function test_request_failed_factory_creates_exception(): void
    {
        $exception = GeocodingException::requestFailed('Request failed');

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('request', $exception->getErrorType());
    }

    public function test_unexpected_error_factory_creates_exception(): void
    {
        $exception = GeocodingException::unexpectedError('Something went wrong');

        $this->assertInstanceOf(GeocodingException::class, $exception);
        $this->assertEquals('unexpected', $exception->getErrorType());
    }
}
