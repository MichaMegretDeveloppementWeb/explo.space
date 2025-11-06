<?php

namespace Tests\Unit\Services\Admin\PlaceRequest\PlaceRequestList;

use App\Contracts\Repositories\Admin\PlaceRequest\PlaceRequestListRepositoryInterface;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListFilterValidationService;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListPaginationValidationService;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListService;
use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListSortingValidationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class PlaceRequestListServiceTest extends TestCase
{
    protected PlaceRequestListRepositoryInterface $repository;

    protected PlaceRequestListFilterValidationService $filterValidation;

    protected PlaceRequestListSortingValidationService $sortingValidation;

    protected PlaceRequestListPaginationValidationService $paginationValidation;

    protected PlaceRequestListService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock repository
        $this->repository = Mockery::mock(PlaceRequestListRepositoryInterface::class);

        // Create mock validation services
        $this->filterValidation = Mockery::mock(PlaceRequestListFilterValidationService::class);
        $this->sortingValidation = Mockery::mock(PlaceRequestListSortingValidationService::class);
        $this->paginationValidation = Mockery::mock(PlaceRequestListPaginationValidationService::class);

        // Create service with all 4 mocked dependencies
        $this->service = new PlaceRequestListService(
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

    // ========================================
    // Get Paginated Place Requests
    // ========================================

    public function test_get_paginated_place_requests_validates_filters(): void
    {
        // Arrange
        $rawFilters = ['status' => 'pending,submitted'];
        $validatedFilters = ['status' => ['pending', 'submitted']];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $validatedSorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($rawFilters)
            ->andReturn($validatedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($sorting)
            ->andReturn($validatedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($pagination)
            ->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($validatedFilters, $validatedSorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($rawFilters, $sorting, $pagination);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_get_paginated_place_requests_passes_validated_filters_to_repository(): void
    {
        // Arrange
        $rawFilters = ['status' => ['pending']];
        $validatedFilters = ['status' => ['pending']];
        $sorting = ['sortBy' => 'title', 'sortDirection' => 'asc'];
        $pagination = ['perPage' => 30];

        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($rawFilters)
            ->andReturn($validatedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($sorting)
            ->andReturn($sorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($pagination)
            ->andReturn(30);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($validatedFilters, $sorting, 30)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($rawFilters, $sorting, $pagination);

        // Assert
        $this->assertSame($paginator, $result);
    }

    public function test_get_paginated_place_requests_uses_default_per_page(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20]; // Default value

        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->andReturn($filters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($sorting)
            ->andReturn($sorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($pagination)
            ->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_accepts_custom_per_page(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 50];

        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->andReturn($filters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($sorting)
            ->andReturn($sorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($pagination)
            ->andReturn(50);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 50)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_throws_exception_for_invalid_filters(): void
    {
        // Arrange
        $this->expectException(ValidationException::class);

        $filters = ['status' => ['invalid_status']];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->with($filters)
            ->andThrow(new ValidationException(validator([], [])));

        // Act
        $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);
    }

    // ========================================
    // Different Sorting Options
    // ========================================

    public function test_get_paginated_place_requests_with_title_sorting(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'title', 'sortDirection' => 'asc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_with_status_sorting(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'status', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_with_created_at_sorting(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'asc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    // ========================================
    // Different Filter Combinations
    // ========================================

    public function test_get_paginated_place_requests_with_empty_filters(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_with_single_status_filter(): void
    {
        // Arrange
        $filters = ['status' => ['pending']];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_with_multiple_status_filters(): void
    {
        // Arrange
        $filters = ['status' => ['pending', 'submitted', 'accepted']];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_place_requests_with_all_statuses(): void
    {
        // Arrange
        $filters = ['status' => ['submitted', 'pending', 'accepted', 'refused']];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    // ========================================
    // Method Call Verification
    // ========================================

    public function test_delegates_to_repository(): void
    {
        // Arrange
        $filters = ['status' => []];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        $this->filterValidation->shouldReceive('validateAndClean')->once()->andReturn($filters);
        $this->sortingValidation->shouldReceive('validateAndClean')->once()->with($sorting)->andReturn($sorting);
        $this->paginationValidation->shouldReceive('validateAndClean')->once()->with($pagination)->andReturn(20);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->with($filters, $sorting, 20)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_calls_validation_service_before_repository(): void
    {
        // Arrange
        $filters = ['status' => 'pending,submitted'];
        $validatedFilters = ['status' => ['pending', 'submitted']];
        $sorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $validatedSorting = ['sortBy' => 'created_at', 'sortDirection' => 'desc'];
        $pagination = ['perPage' => 20];

        // Validation should be called first (ordered)
        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->ordered()
            ->with($filters)
            ->andReturn($validatedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->ordered()
            ->with($sorting)
            ->andReturn($validatedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->ordered()
            ->with($pagination)
            ->andReturn(20);

        // Repository should be called after all validations
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->repository->shouldReceive('getPaginatedPlaceRequests')
            ->once()
            ->ordered()
            ->with($validatedFilters, $validatedSorting, 20)
            ->andReturn($paginator);

        // Act
        $result = $this->service->getPaginatedPlaceRequests($filters, $sorting, $pagination);

        // Assert - Verify the method calls were made in correct order (Mockery does this automatically)
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
