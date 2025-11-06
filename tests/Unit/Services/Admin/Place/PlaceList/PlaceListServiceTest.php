<?php

namespace Tests\Unit\Services\Admin\Place\PlaceList;

use App\Contracts\Repositories\Admin\Place\PlaceListRepositoryInterface;
use App\Services\Admin\Place\PlaceList\PlaceListFilterValidationService;
use App\Services\Admin\Place\PlaceList\PlaceListPaginationValidationService;
use App\Services\Admin\Place\PlaceList\PlaceListService;
use App\Services\Admin\Place\PlaceList\PlaceListSortingValidationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class PlaceListServiceTest extends TestCase
{
    protected PlaceListRepositoryInterface $repository;

    protected PlaceListFilterValidationService $filterValidation;

    protected PlaceListSortingValidationService $sortingValidation;

    protected PlaceListPaginationValidationService $paginationValidation;

    protected PlaceListService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->repository = Mockery::mock(PlaceListRepositoryInterface::class);
        $this->filterValidation = Mockery::mock(PlaceListFilterValidationService::class);
        $this->sortingValidation = Mockery::mock(PlaceListSortingValidationService::class);
        $this->paginationValidation = Mockery::mock(PlaceListPaginationValidationService::class);

        // Create service with mocked dependencies
        $this->service = new PlaceListService(
            $this->repository,
            $this->filterValidation,
            $this->sortingValidation,
            $this->paginationValidation
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_paginated_places_validates_and_delegates_to_repository(): void
    {
        // Arrange
        $filters = ['search' => 'test', 'tags' => ['nasa'], 'locale' => 'fr'];
        $sorting = ['sortBy' => 'title', 'sortDirection' => 'asc'];
        $pagination = ['perPage' => 20];

        $cleanedFilters = ['search' => 'test', 'tags' => ['nasa'], 'locale' => 'fr'];
        $cleanedSorting = ['column' => 'title', 'direction' => 'asc'];
        $perPage = 20;

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($filters)
            ->andReturn($cleanedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($sorting)
            ->andReturn($cleanedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($pagination)
            ->andReturn($perPage);

        $this->repository->shouldReceive('getPaginatedPlaces')
            ->once()
            ->with($cleanedFilters, $cleanedSorting, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedPlaces($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_default_filters_returns_correct_structure(): void
    {
        // Act
        $result = $this->service->getDefaultFilters();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('search', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('locale', $result);
        $this->assertEquals('', $result['search']);
        $this->assertEquals([], $result['tags']);
        $this->assertIsString($result['locale']);
    }

    public function test_get_default_sorting_delegates_to_validation_service(): void
    {
        // Arrange
        $expectedSorting = ['column' => 'created_at', 'direction' => 'desc'];

        $this->sortingValidation->shouldReceive('getDefaultSort')
            ->once()
            ->andReturn($expectedSorting);

        // Act
        $result = $this->service->getDefaultSorting();

        // Assert
        $this->assertSame($expectedSorting, $result);
    }

    public function test_get_default_per_page_delegates_to_validation_service(): void
    {
        // Arrange
        $expectedPerPage = 20;

        $this->paginationValidation->shouldReceive('getDefaultValue')
            ->once()
            ->andReturn($expectedPerPage);

        // Act
        $result = $this->service->getDefaultPerPage();

        // Assert
        $this->assertSame($expectedPerPage, $result);
    }
}
