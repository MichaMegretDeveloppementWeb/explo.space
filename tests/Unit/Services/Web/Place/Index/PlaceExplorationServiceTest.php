<?php

namespace Tests\Unit\Services\Web\Place\Index;

use App\Contracts\Repositories\Web\Place\Index\PlaceExplorationRepositoryInterface;
use App\Services\Web\Place\Index\PlaceExplorationService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class PlaceExplorationServiceTest extends TestCase
{
    private PlaceExplorationRepositoryInterface $mockRepository;

    private PlaceExplorationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(PlaceExplorationRepositoryInterface::class);
        $this->service = new PlaceExplorationService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test getPlacesForList returns correct structure
     */
    public function test_get_places_for_list_returns_correct_structure(): void
    {
        $filters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => [],
        ];

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $mockPlaces = [
            ['id' => 1, 'title' => 'Place 1'],
            ['id' => 2, 'title' => 'Place 2'],
        ];

        // Repository returns directly the expected array structure
        $expectedReturn = [
            'data' => $mockPlaces,
            'next_cursor' => 'cursor123',
            'has_more_pages' => true,
        ];

        $this->mockRepository
            ->shouldReceive('getPlacesInBoundingBoxAsArrays')
            ->once()
            ->with(
                Mockery::type('array'),
                $boundingBox,
                app()->getLocale(),
                30,
                null
            )
            ->andReturn($expectedReturn);

        $result = $this->service->getPlacesForList($filters, $boundingBox, 30);

        // Service returns exactly what repository returns (no transformation)
        $this->assertSame($expectedReturn, $result);
    }

    /**
     * Test getPlacesForList with cursor pagination
     */
    public function test_get_places_for_list_with_cursor(): void
    {
        $filters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => [],
        ];

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $cursor = 'existing_cursor';

        $mockPlaces = [
            ['id' => 3, 'title' => 'Place 3'],
        ];

        $expectedReturn = [
            'data' => $mockPlaces,
            'next_cursor' => null,
            'has_more_pages' => false,
        ];

        $this->mockRepository
            ->shouldReceive('getPlacesInBoundingBoxAsArrays')
            ->once()
            ->with(
                Mockery::type('array'),
                $boundingBox,
                app()->getLocale(),
                30,
                $cursor
            )
            ->andReturn($expectedReturn);

        $result = $this->service->getPlacesForList($filters, $boundingBox, 30, $cursor);

        $this->assertSame($expectedReturn, $result);
    }

    /**
     * Test getPlacesForList with no results
     */
    public function test_get_places_for_list_with_no_results(): void
    {
        $filters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => [],
        ];

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $expectedReturn = [
            'data' => [],
            'next_cursor' => null,
            'has_more_pages' => false,
        ];

        $this->mockRepository
            ->shouldReceive('getPlacesInBoundingBoxAsArrays')
            ->once()
            ->andReturn($expectedReturn);

        $result = $this->service->getPlacesForList($filters, $boundingBox, 30);

        $this->assertSame($expectedReturn, $result);
    }

    /**
     * Test getPlacesForMap returns correct structure
     */
    public function test_get_places_for_map_returns_correct_structure(): void
    {
        $filters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => [],
        ];

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $mockCoordinates = new Collection([
            ['id' => 1, 'latitude' => 48.8566, 'longitude' => 2.3522],
            ['id' => 2, 'latitude' => 48.8606, 'longitude' => 2.3376],
        ]);

        $this->mockRepository
            ->shouldReceive('getPlacesCoordinates')
            ->once()
            ->with(Mockery::type('array'), $boundingBox)
            ->andReturn($mockCoordinates);

        $result = $this->service->getPlacesForMap($filters, $boundingBox);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('coordinates', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertArrayHasKey('bounding_box', $result);
        $this->assertSame($mockCoordinates, $result['coordinates']);
        $this->assertEquals(2, $result['count']);
        $this->assertEquals($boundingBox, $result['bounding_box']);
    }

    /**
     * Test getPlacesForMap with null bounding box
     */
    public function test_get_places_for_map_with_null_bounding_box(): void
    {
        $filters = [
            'mode' => 'worldwide',
            'tags' => ['space'],
        ];

        $mockCoordinates = new Collection([
            ['id' => 1, 'latitude' => 48.8566, 'longitude' => 2.3522],
        ]);

        $this->mockRepository
            ->shouldReceive('getPlacesCoordinates')
            ->once()
            ->with(Mockery::type('array'), null)
            ->andReturn($mockCoordinates);

        $result = $this->service->getPlacesForMap($filters, null);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('coordinates', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertArrayHasKey('bounding_box', $result);
        $this->assertSame($mockCoordinates, $result['coordinates']);
        $this->assertEquals(1, $result['count']);
        $this->assertNull($result['bounding_box']);
    }

    /**
     * Test getPlacesForMap with worldwide mode
     */
    public function test_get_places_for_map_worldwide_mode(): void
    {
        $filters = [
            'mode' => 'worldwide',
            'tags' => ['nasa', 'spacex'],
        ];

        $boundingBox = [
            'north' => 85,
            'south' => -85,
            'east' => 180,
            'west' => -180,
        ];

        $mockCoordinates = new Collection([
            ['id' => 1, 'latitude' => 28.5728, 'longitude' => -80.6490], // Kennedy
            ['id' => 2, 'latitude' => 25.9970, 'longitude' => -97.1559], // SpaceX
        ]);

        $this->mockRepository
            ->shouldReceive('getPlacesCoordinates')
            ->once()
            ->with(
                Mockery::on(function ($arg) {
                    return $arg['mode'] === 'worldwide'
                        && is_array($arg['tags'])
                        && in_array('nasa', $arg['tags'])
                        && in_array('spacex', $arg['tags']);
                }),
                $boundingBox
            )
            ->andReturn($mockCoordinates);

        $result = $this->service->getPlacesForMap($filters, $boundingBox);

        $this->assertEquals(2, $result['count']);
        $this->assertCount(2, $result['coordinates']);
    }

    /**
     * Test getPlacesForList with tags filter
     */
    public function test_get_places_for_list_with_tags_filter(): void
    {
        $filters = [
            'mode' => 'worldwide',
            'tags' => ['nasa', 'space'],
        ];

        $boundingBox = [
            'north' => 85,
            'south' => -85,
            'east' => 180,
            'west' => -180,
        ];

        $mockPlaces = [
            ['id' => 1, 'title' => 'NASA Center'],
        ];

        $expectedReturn = [
            'data' => $mockPlaces,
            'next_cursor' => null,
            'has_more_pages' => false,
        ];

        $this->mockRepository
            ->shouldReceive('getPlacesInBoundingBoxAsArrays')
            ->once()
            ->with(
                Mockery::on(function ($arg) {
                    return is_array($arg['tags'])
                        && in_array('nasa', $arg['tags'])
                        && in_array('space', $arg['tags']);
                }),
                $boundingBox,
                app()->getLocale(),
                30,
                null
            )
            ->andReturn($expectedReturn);

        $result = $this->service->getPlacesForList($filters, $boundingBox, 30);

        $this->assertSame($expectedReturn, $result);
    }
}
