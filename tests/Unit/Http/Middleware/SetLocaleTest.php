<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class SetLocaleTest extends TestCase
{
    private SetLocale $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SetLocale;
    }

    public function test_middleware_can_be_instantiated(): void
    {
        $this->assertInstanceOf(SetLocale::class, $this->middleware);
    }

    public function test_detects_browser_language(): void
    {
        $request = Request::create('/test');
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9,fr;q=0.8');

        $response = $this->middleware->handle($request, function ($req) {
            return new Response;
        });

        // Le middleware devrait détecter une langue supportée
        $this->assertContains(app()->getLocale(), ['en', 'fr']);
    }

    public function test_forced_locale_takes_priority(): void
    {
        $request = Request::create('/test');
        $request->headers->set('Accept-Language', 'en-US');

        $response = $this->middleware->handle($request, function ($req) {
            return new Response;
        }, 'fr'); // Force français

        $this->assertEquals('fr', app()->getLocale());
    }

    public function test_returns_response_instance(): void
    {
        $request = Request::create('/test');

        $response = $this->middleware->handle($request, function ($req) {
            return new Response;
        });

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_falls_back_to_default_when_unsupported(): void
    {
        $request = Request::create('/test');

        $response = $this->middleware->handle($request, function ($req) {
            return new Response;
        }, 'invalid-locale');

        $this->assertEquals(config('locales.default'), app()->getLocale());
    }
}
