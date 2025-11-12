<?php

namespace Tests\Unit\Domain\Seo\Strategies;

use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\Strategies\ExploreSeoStrategy;
use App\Domain\Seo\Strategies\HomepageSeoStrategy;
use App\Domain\Seo\Strategies\SeoStrategyResolver;
use InvalidArgumentException;
use Tests\TestCase;

class SeoStrategyResolverTest extends TestCase
{
    private SeoStrategyResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new SeoStrategyResolver;
    }

    public function test_resolve_returns_homepage_strategy_for_homepage(): void
    {
        $strategy = $this->resolver->resolve('homepage');

        $this->assertInstanceOf(SeoStrategyInterface::class, $strategy);
        $this->assertInstanceOf(HomepageSeoStrategy::class, $strategy);
    }

    public function test_resolve_returns_explore_strategy_for_explore(): void
    {
        $strategy = $this->resolver->resolve('explore');

        $this->assertInstanceOf(SeoStrategyInterface::class, $strategy);
        $this->assertInstanceOf(ExploreSeoStrategy::class, $strategy);
    }

    public function test_resolve_throws_exception_for_unsupported_page_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolver->resolve('unsupported');
    }

    public function test_resolve_throws_exception_with_correct_message(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SEO strategy not found for page type: unsupported');

        $this->resolver->resolve('unsupported');
    }

    public function test_resolve_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolver->resolve('');
    }

    public function test_get_supported_page_types_returns_all_types(): void
    {
        $types = $this->resolver->getSupportedPageTypes();

        $this->assertContains('homepage', $types);
        $this->assertContains('explore', $types);
    }

    public function test_get_supported_page_types_returns_array(): void
    {
        $types = $this->resolver->getSupportedPageTypes();

        $this->assertIsArray($types);
        $this->assertNotEmpty($types);
    }

    public function test_get_supported_page_types_has_exact_count(): void
    {
        $types = $this->resolver->getSupportedPageTypes();

        $this->assertCount(8, $types); // homepage, about, contact, legal, privacy, explore, place_show, place_request_create
    }

    public function test_is_supported_returns_true_for_homepage(): void
    {
        $result = $this->resolver->isSupported('homepage');

        $this->assertTrue($result);
    }

    public function test_is_supported_returns_true_for_explore(): void
    {
        $result = $this->resolver->isSupported('explore');

        $this->assertTrue($result);
    }

    public function test_is_supported_returns_false_for_unsupported(): void
    {
        $result = $this->resolver->isSupported('unsupported');

        $this->assertFalse($result);
    }

    public function test_is_supported_returns_false_for_empty_string(): void
    {
        $result = $this->resolver->isSupported('');

        $this->assertFalse($result);
    }

    public function test_is_supported_is_case_sensitive(): void
    {
        $result = $this->resolver->isSupported('Homepage');

        $this->assertFalse($result);
    }

    public function test_supported_types_match_between_methods(): void
    {
        $supportedTypes = $this->resolver->getSupportedPageTypes();

        foreach ($supportedTypes as $type) {
            $this->assertTrue(
                $this->resolver->isSupported($type),
                "Type {$type} should be supported"
            );
        }
    }

    public function test_all_supported_types_can_be_resolved(): void
    {
        $supportedTypes = $this->resolver->getSupportedPageTypes();

        foreach ($supportedTypes as $type) {
            // Skip place_show car il nécessite des données PlaceDetailDTO
            if ($type === 'place_show') {
                $this->assertTrue($this->resolver->isSupported($type));

                continue;
            }

            $strategy = $this->resolver->resolve($type);

            $this->assertInstanceOf(
                SeoStrategyInterface::class,
                $strategy,
                "Type {$type} should resolve to a valid strategy"
            );
        }
    }
}
