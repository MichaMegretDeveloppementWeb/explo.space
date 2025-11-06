<?php

namespace Tests\Unit\Domain\Seo\Actions;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Domain\Seo\Contracts\SeoStrategyInterface;
use App\Domain\Seo\DTO\SeoData;
use App\Domain\Seo\Services\SeoService;
use App\Domain\Seo\Strategies\SeoStrategyResolver;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class SeoBuilderActionTest extends TestCase
{
    private SeoStrategyResolver $resolver;

    private SeoService $service;

    private SeoBuilderAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = Mockery::mock(SeoStrategyResolver::class);
        $this->service = Mockery::mock(SeoService::class);
        $this->action = new SeoBuilderAction($this->resolver, $this->service);
    }

    public function test_execute_resolves_strategy_and_generates_seo_data(): void
    {
        $pageType = 'homepage';
        $strategy = Mockery::mock(SeoStrategyInterface::class);

        $seoData = Mockery::mock(SeoData::class);

        // Expectations
        $this->resolver
            ->shouldReceive('resolve')
            ->once()
            ->with($pageType, [])
            ->andReturn($strategy);

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->with($strategy)
            ->andReturn($seoData);

        // Execute
        $result = $this->action->execute($pageType);

        // Assert
        $this->assertSame($seoData, $result);
    }

    public function test_execute_with_homepage_page_type(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);
        $seoData = Mockery::mock(SeoData::class);

        $this->resolver
            ->shouldReceive('resolve')
            ->once()
            ->with('homepage', [])
            ->andReturn($strategy);

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->with($strategy)
            ->andReturn($seoData);

        $result = $this->action->execute('homepage');

        $this->assertInstanceOf(SeoData::class, $result);
    }

    public function test_execute_with_explore_page_type(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);
        $seoData = Mockery::mock(SeoData::class);

        $this->resolver
            ->shouldReceive('resolve')
            ->once()
            ->with('explore', [])
            ->andReturn($strategy);

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->with($strategy)
            ->andReturn($seoData);

        $result = $this->action->execute('explore');

        $this->assertInstanceOf(SeoData::class, $result);
    }

    public function test_execute_throws_exception_for_unsupported_page_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolver
            ->shouldReceive('resolve')
            ->once()
            ->with('unsupported', [])
            ->andThrow(new InvalidArgumentException('Unsupported page type: unsupported'));

        $this->action->execute('unsupported');
    }

    public function test_execute_passes_correct_page_type_to_resolver(): void
    {
        $pageType = 'homepage';
        $strategy = Mockery::mock(SeoStrategyInterface::class);
        $seoData = Mockery::mock(SeoData::class);

        $this->resolver
            ->shouldReceive('resolve')
            ->once()
            ->with($pageType, [])
            ->andReturn($strategy);

        $this->service
            ->shouldReceive('generate')
            ->andReturn($seoData);

        $this->action->execute($pageType);

        // Mockery will verify that resolve() was called with 'homepage'
    }

    public function test_execute_passes_resolved_strategy_to_service(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);
        $seoData = Mockery::mock(SeoData::class);

        $this->resolver
            ->shouldReceive('resolve')
            ->andReturn($strategy);

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->with($strategy)
            ->andReturn($seoData);

        $this->action->execute('homepage');

        // Mockery will verify that generate() was called with the strategy instance
    }

    public function test_execute_calls_resolver_before_service(): void
    {
        $strategy = Mockery::mock(SeoStrategyInterface::class);
        $seoData = Mockery::mock(SeoData::class);

        // Use ordered expectations
        $this->resolver
            ->shouldReceive('resolve')
            ->once()
            ->ordered()
            ->andReturn($strategy);

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->ordered()
            ->andReturn($seoData);

        $this->action->execute('homepage');
    }
}
