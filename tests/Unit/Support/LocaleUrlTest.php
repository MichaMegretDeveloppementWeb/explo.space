<?php

namespace Tests\Unit\Support;

use App\Support\LocaleUrl;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocaleUrlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set default locale
        app()->setLocale('fr');

        // Register test routes for both locales
        Route::get('/fr/lieux', fn () => 'liste')->name('places.index.fr');
        Route::get('/en/places', fn () => 'list')->name('places.index.en');

        Route::get('/fr/lieux/{slug}', fn ($slug) => $slug)->name('places.show.fr');
        Route::get('/en/places/{slug}', fn ($slug) => $slug)->name('places.show.en');

        Route::get('/fr/explorer', fn () => 'explore')->name('explore.fr');
        Route::get('/en/explore', fn () => 'explore')->name('explore.en');
    }

    /**
     * Test segment() returns correct translation for French
     */
    public function test_segment_returns_french_translation(): void
    {
        app()->setLocale('fr');

        $result = LocaleUrl::segment('places');

        $this->assertEquals('lieux', $result);
    }

    /**
     * Test segment() returns correct translation for English
     */
    public function test_segment_returns_english_translation(): void
    {
        app()->setLocale('en');

        $result = LocaleUrl::segment('places');

        $this->assertEquals('places', $result);
    }

    /**
     * Test segment() with explicit locale parameter
     */
    public function test_segment_with_explicit_locale(): void
    {
        app()->setLocale('fr');

        $frResult = LocaleUrl::segment('places', 'fr');
        $enResult = LocaleUrl::segment('places', 'en');

        $this->assertEquals('lieux', $frResult);
        $this->assertEquals('places', $enResult);
    }

    /**
     * Test segment() returns fallback for missing translation
     */
    public function test_segment_returns_fallback_for_missing_translation(): void
    {
        $result = LocaleUrl::segment('nonexistent_key');

        // Should return the key itself as fallback
        $this->assertEquals('nonexistent_key', $result);
    }

    /**
     * Test routeName() generates correct route name for French
     */
    public function test_route_name_generates_french_route(): void
    {
        app()->setLocale('fr');

        $result = LocaleUrl::routeName('places.show');

        $this->assertEquals('places.show.fr', $result);
    }

    /**
     * Test routeName() generates correct route name for English
     */
    public function test_route_name_generates_english_route(): void
    {
        app()->setLocale('en');

        $result = LocaleUrl::routeName('places.show');

        $this->assertEquals('places.show.en', $result);
    }

    /**
     * Test routeName() with explicit locale parameter
     */
    public function test_route_name_with_explicit_locale(): void
    {
        app()->setLocale('fr');

        $frResult = LocaleUrl::routeName('places.show', 'fr');
        $enResult = LocaleUrl::routeName('places.show', 'en');

        $this->assertEquals('places.show.fr', $frResult);
        $this->assertEquals('places.show.en', $enResult);
    }

    /**
     * Test route() generates correct URL for index route
     */
    public function test_route_generates_url_for_index_route(): void
    {
        $frUrl = LocaleUrl::route('explore', [], 'fr');
        $enUrl = LocaleUrl::route('explore', [], 'en');

        $this->assertEquals(url('/fr/explorer'), $frUrl);
        $this->assertEquals(url('/en/explore'), $enUrl);
    }

    /**
     * Test route() generates correct URL with parameters
     */
    public function test_route_generates_url_with_parameters(): void
    {
        $frUrl = LocaleUrl::route('places.show', ['slug' => 'centre-spatial-kennedy'], 'fr');
        $enUrl = LocaleUrl::route('places.show', ['slug' => 'kennedy-space-center'], 'en');

        $this->assertEquals(url('/fr/lieux/centre-spatial-kennedy'), $frUrl);
        $this->assertEquals(url('/en/places/kennedy-space-center'), $enUrl);
    }

    /**
     * Test route() uses current locale when not specified
     */
    public function test_route_uses_current_locale(): void
    {
        app()->setLocale('fr');
        $frUrl = LocaleUrl::route('explore');

        app()->setLocale('en');
        $enUrl = LocaleUrl::route('explore');

        $this->assertEquals(url('/fr/explorer'), $frUrl);
        $this->assertEquals(url('/en/explore'), $enUrl);
    }

    /**
     * Test switchRoute() switches language for simple route
     */
    public function test_switch_route_switches_language_for_simple_route(): void
    {
        $result = LocaleUrl::switchRoute(
            'explore.fr',
            [],
            [],
            'en'
        );

        $this->assertEquals(url('/en/explore'), $result);
    }

    /**
     * Test switchRoute() switches language with route parameters
     */
    public function test_switch_route_with_route_parameters(): void
    {
        $result = LocaleUrl::switchRoute(
            'places.show.fr',
            ['slug' => 'kennedy-space-center'],
            [],
            'en'
        );

        $this->assertEquals(url('/en/places/kennedy-space-center'), $result);
    }

    /**
     * Test switchRoute() preserves query parameters
     */
    public function test_switch_route_preserves_query_parameters(): void
    {
        $result = LocaleUrl::switchRoute(
            'explore.fr',
            [],
            ['mode' => 'proximity', 'radius' => '200000'],
            'en'
        );

        $this->assertEquals(url('/en/explore?mode=proximity&radius=200000'), $result);
    }

    /**
     * Test switchRoute() with both route and query parameters
     */
    public function test_switch_route_with_route_and_query_parameters(): void
    {
        $result = LocaleUrl::switchRoute(
            'places.show.fr',
            ['slug' => 'centre-spatial-kennedy'],
            ['ref' => 'homepage', 'utm_source' => 'test'],
            'en'
        );

        $this->assertEquals(
            url('/en/places/centre-spatial-kennedy?ref=homepage&utm_source=test'),
            $result
        );
    }

    /**
     * Test switchRoute() from English to French
     */
    public function test_switch_route_from_english_to_french(): void
    {
        $result = LocaleUrl::switchRoute(
            'places.show.en',
            ['slug' => 'kennedy-space-center'],
            [],
            'fr'
        );

        $this->assertEquals(url('/fr/lieux/kennedy-space-center'), $result);
    }

    /**
     * Test switchRoute() handles empty query parameters correctly
     */
    public function test_switch_route_handles_empty_query_parameters(): void
    {
        $result = LocaleUrl::switchRoute(
            'explore.fr',
            [],
            [],
            'en'
        );

        // Should not have trailing ?
        $this->assertEquals(url('/en/explore'), $result);
        $this->assertStringNotContainsString('?', $result);
    }

    /**
     * Test switchRoute() extracts base name correctly
     */
    public function test_switch_route_extracts_base_name_correctly(): void
    {
        // Test with .fr suffix
        $frResult = LocaleUrl::switchRoute('explore.fr', [], [], 'en');
        $this->assertEquals(url('/en/explore'), $frResult);

        // Test with .en suffix
        $enResult = LocaleUrl::switchRoute('explore.en', [], [], 'fr');
        $this->assertEquals(url('/fr/explorer'), $enResult);
    }
}
