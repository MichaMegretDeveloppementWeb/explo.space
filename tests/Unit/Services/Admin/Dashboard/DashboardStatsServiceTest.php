<?php

namespace Tests\Unit\Services\Admin\Dashboard;

use App\Contracts\Repositories\Admin\Dashboard\DashboardStatsRepositoryInterface;
use App\Services\Admin\Dashboard\DashboardStatsService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class DashboardStatsServiceTest extends TestCase
{
    protected DashboardStatsRepositoryInterface $repository;

    protected DashboardStatsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock repository
        $this->repository = Mockery::mock(DashboardStatsRepositoryInterface::class);

        // Create service with mocked repository
        $this->service = new DashboardStatsService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_dashboard_stats_returns_all_statistics(): void
    {
        // Arrange
        $this->repository->shouldReceive('getTotalPlaces')->once()->andReturn(10);
        $this->repository->shouldReceive('getPendingPlaceRequests')->once()->andReturn(5);
        $this->repository->shouldReceive('getPendingEditRequests')->once()->andReturn(3);
        $this->repository->shouldReceive('getTotalTags')->once()->andReturn(8);
        $this->repository->shouldReceive('getTotalCategories')->once()->andReturn(4);

        // Act
        $stats = $this->service->getDashboardStats();

        // Assert
        $this->assertIsArray($stats);
        $this->assertEquals(10, $stats['total_places']);
        $this->assertEquals(5, $stats['pending_place_requests']);
        $this->assertEquals(3, $stats['pending_edit_requests']);
        $this->assertEquals(8, $stats['total_tags']);
        $this->assertEquals(4, $stats['total_categories']);
    }

    public function test_get_dashboard_stats_returns_correct_array_keys(): void
    {
        // Arrange
        $this->repository->shouldReceive('getTotalPlaces')->once()->andReturn(0);
        $this->repository->shouldReceive('getPendingPlaceRequests')->once()->andReturn(0);
        $this->repository->shouldReceive('getPendingEditRequests')->once()->andReturn(0);
        $this->repository->shouldReceive('getTotalTags')->once()->andReturn(0);
        $this->repository->shouldReceive('getTotalCategories')->once()->andReturn(0);

        // Act
        $stats = $this->service->getDashboardStats();

        // Assert
        $this->assertArrayHasKey('total_places', $stats);
        $this->assertArrayHasKey('pending_place_requests', $stats);
        $this->assertArrayHasKey('pending_edit_requests', $stats);
        $this->assertArrayHasKey('total_tags', $stats);
        $this->assertArrayHasKey('total_categories', $stats);
    }

    public function test_get_recent_place_requests_delegates_to_repository(): void
    {
        // Arrange
        $expectedCollection = new Collection(['request1', 'request2']);
        $this->repository->shouldReceive('getRecentPlaceRequests')
            ->once()
            ->with(5)
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentPlaceRequests(5);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_place_requests_uses_default_limit(): void
    {
        // Arrange
        $expectedCollection = new Collection(['request1', 'request2']);
        $this->repository->shouldReceive('getRecentPlaceRequests')
            ->once()
            ->with(5) // Default limit
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentPlaceRequests();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_place_requests_accepts_custom_limit(): void
    {
        // Arrange
        $expectedCollection = new Collection(['request1', 'request2', 'request3']);
        $this->repository->shouldReceive('getRecentPlaceRequests')
            ->once()
            ->with(10)
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentPlaceRequests(10);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_places_delegates_to_repository(): void
    {
        // Arrange
        $expectedCollection = new Collection(['place1', 'place2']);
        $this->repository->shouldReceive('getRecentPlaces')
            ->once()
            ->with(5)
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentPlaces(5);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_places_uses_default_limit(): void
    {
        // Arrange
        $expectedCollection = new Collection(['place1', 'place2']);
        $this->repository->shouldReceive('getRecentPlaces')
            ->once()
            ->with(5) // Default limit
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentPlaces();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_edit_requests_delegates_to_repository(): void
    {
        // Arrange
        $expectedCollection = new Collection(['edit1', 'edit2']);
        $this->repository->shouldReceive('getRecentEditRequests')
            ->once()
            ->with(5)
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentEditRequests(5);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_edit_requests_uses_default_limit(): void
    {
        // Arrange
        $expectedCollection = new Collection(['edit1', 'edit2']);
        $this->repository->shouldReceive('getRecentEditRequests')
            ->once()
            ->with(5) // Default limit
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentEditRequests();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_recent_edit_requests_accepts_custom_limit(): void
    {
        // Arrange
        $expectedCollection = new Collection(['edit1', 'edit2', 'edit3']);
        $this->repository->shouldReceive('getRecentEditRequests')
            ->once()
            ->with(3)
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getRecentEditRequests(3);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }
}
