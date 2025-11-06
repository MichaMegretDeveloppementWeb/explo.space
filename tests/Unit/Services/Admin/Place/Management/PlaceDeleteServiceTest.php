<?php

namespace Tests\Unit\Services\Admin\Place\Management;

use App\Contracts\Repositories\Admin\Place\Management\PlaceDeleteRepositoryInterface;
use App\Services\Admin\Place\Management\PlaceDeleteService;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class PlaceDeleteServiceTest extends TestCase
{
    private PlaceDeleteRepositoryInterface $mockRepository;

    private PlaceDeleteService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(PlaceDeleteRepositoryInterface::class);
        $this->service = new PlaceDeleteService($this->mockRepository);

        Log::spy();
    }

    public function test_delete_place_successfully(): void
    {
        $placeId = 1;

        $this->mockRepository
            ->shouldReceive('placeExists')
            ->with($placeId)
            ->once()
            ->andReturn(true);

        $this->mockRepository
            ->shouldReceive('deletePlace')
            ->with($placeId)
            ->once()
            ->andReturn(true);

        $result = $this->service->deletePlace($placeId);

        $this->assertTrue($result);
    }

    public function test_throws_exception_when_place_not_found(): void
    {
        $placeId = 999;

        $this->mockRepository
            ->shouldReceive('placeExists')
            ->with($placeId)
            ->once()
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Le lieu avec l'ID 999 n'existe pas.");

        $this->service->deletePlace($placeId);
    }

    public function test_returns_false_when_deletion_fails(): void
    {
        $placeId = 1;

        $this->mockRepository
            ->shouldReceive('placeExists')
            ->with($placeId)
            ->once()
            ->andReturn(true);

        $this->mockRepository
            ->shouldReceive('deletePlace')
            ->with($placeId)
            ->once()
            ->andReturn(false);

        $result = $this->service->deletePlace($placeId);

        $this->assertFalse($result);
    }

    public function test_can_delete_place_returns_true_when_place_exists(): void
    {
        $placeId = 1;

        $this->mockRepository
            ->shouldReceive('placeExists')
            ->with($placeId)
            ->once()
            ->andReturn(true);

        $result = $this->service->canDeletePlace($placeId);

        $this->assertTrue($result);
    }

    public function test_can_delete_place_returns_false_when_place_not_exists(): void
    {
        $placeId = 999;

        $this->mockRepository
            ->shouldReceive('placeExists')
            ->with($placeId)
            ->once()
            ->andReturn(false);

        $result = $this->service->canDeletePlace($placeId);

        $this->assertFalse($result);
    }
}
