<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

class LocalHelperTest extends TestCase
{
    public function test_current_locale_helper(): void
    {
        app()->setLocale('fr');
        $this->assertEquals('fr', currentLocale());

        app()->setLocale('en');
        $this->assertEquals('en', currentLocale());
    }

    public function test_is_current_locale_helper(): void
    {
        app()->setLocale('fr');
        $this->assertTrue(isCurrentLocale('fr'));
        $this->assertFalse(isCurrentLocale('en'));

        app()->setLocale('en');
        $this->assertTrue(isCurrentLocale('en'));
        $this->assertFalse(isCurrentLocale('fr'));
    }

    public function test_available_locales_helper(): void
    {
        $locales = availableLocales();

        $this->assertIsArray($locales);
        $this->assertContains('fr', $locales);
        $this->assertContains('en', $locales);
    }

    public function test_localized_segment_helper(): void
    {
        $this->assertEquals('lieux', localizedSegment('places', 'fr'));
        $this->assertEquals('places', localizedSegment('places', 'en'));
    }

    public function test_is_rtl_locale_helper(): void
    {
        $this->assertFalse(isRtlLocale('fr'));
        $this->assertFalse(isRtlLocale('en'));
        $this->assertTrue(isRtlLocale('ar'));
        $this->assertTrue(isRtlLocale('he'));
    }

    public function test_get_locale_direction_helper(): void
    {
        $this->assertEquals('ltr', getLocaleDirection('fr'));
        $this->assertEquals('ltr', getLocaleDirection('en'));
        $this->assertEquals('rtl', getLocaleDirection('ar'));
    }

    public function test_local_route_helper(): void
    {
        app()->setLocale('fr');
        $url = localRoute('home');

        // Note: URL format is https://cosmap.test/fr (no trailing slash)
        $this->assertStringContainsString('/fr', $url);
        $this->assertStringEndsWith('/fr', $url);
    }

    public function test_localized_url_helper(): void
    {
        $url = localizedUrl('test/path', 'en');

        $this->assertStringContainsString('/en/test/path', $url);
    }
}
