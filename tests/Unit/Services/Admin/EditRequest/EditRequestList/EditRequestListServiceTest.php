<?php

namespace Tests\Unit\Services\Admin\EditRequest\EditRequestList;

use App\Contracts\Repositories\Admin\EditRequest\EditRequestListRepositoryInterface;
use App\Services\Admin\EditRequest\EditRequestList\EditRequestListFilterValidationService;
use App\Services\Admin\EditRequest\EditRequestList\EditRequestListPaginationValidationService;
use App\Services\Admin\EditRequest\EditRequestList\EditRequestListService;
use App\Services\Admin\EditRequest\EditRequestList\EditRequestListSortingValidationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class EditRequestListServiceTest extends TestCase
{
    protected EditRequestListRepositoryInterface $repository;

    protected EditRequestListFilterValidationService $filterValidation;

    protected EditRequestListSortingValidationService $sortingValidation;

    protected EditRequestListPaginationValidationService $paginationValidation;

    protected EditRequestListService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks
        $this->repository = Mockery::mock(EditRequestListRepositoryInterface::class);
        $this->filterValidation = Mockery::mock(EditRequestListFilterValidationService::class);
        $this->sortingValidation = Mockery::mock(EditRequestListSortingValidationService::class);
        $this->paginationValidation = Mockery::mock(EditRequestListPaginationValidationService::class);

        // Create service with mocked dependencies
        $this->service = new EditRequestListService(
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
    // Main Method Tests
    // ========================================

    public function test_get_paginated_edit_requests_validates_and_delegates_to_repository(): void
    {
        // Arrange
        $filters = ['search' => 'test', 'type' => 'modification', 'status' => 'pending'];
        $sorting = ['sortBy' => 'place', 'sortDirection' => 'asc'];
        $pagination = ['perPage' => 20];

        $cleanedFilters = ['search' => 'test', 'type' => 'modification', 'status' => 'pending'];
        $cleanedSorting = ['column' => 'place', 'direction' => 'asc'];
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

        $this->repository->shouldReceive('getPaginatedEditRequests')
            ->once()
            ->with($cleanedFilters, $cleanedSorting, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedEditRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_paginated_edit_requests_calls_validation_services_in_correct_order(): void
    {
        // Arrange
        $filters = [];
        $sorting = [];
        $pagination = [];

        $cleanedFilters = ['search' => '', 'type' => '', 'status' => ''];
        $cleanedSorting = ['column' => 'created_at', 'direction' => 'desc'];
        $perPage = 20;

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        // Set up ordered expectations
        $this->filterValidation->shouldReceive('validateAndClean')
            ->once()
            ->ordered()
            ->with($filters)
            ->andReturn($cleanedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->once()
            ->ordered()
            ->with($sorting)
            ->andReturn($cleanedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->once()
            ->ordered()
            ->with($pagination)
            ->andReturn($perPage);

        $this->repository->shouldReceive('getPaginatedEditRequests')
            ->once()
            ->ordered()
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedEditRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_paginated_edit_requests_with_only_search_filter(): void
    {
        // Arrange
        $filters = ['search' => 'kennedy'];
        $sorting = [];
        $pagination = [];

        $cleanedFilters = ['search' => 'kennedy', 'type' => '', 'status' => ''];
        $cleanedSorting = ['column' => 'created_at', 'direction' => 'desc'];
        $perPage = 20;

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->filterValidation->shouldReceive('validateAndClean')
            ->with($filters)
            ->andReturn($cleanedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->with($sorting)
            ->andReturn($cleanedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->with($pagination)
            ->andReturn($perPage);

        $this->repository->shouldReceive('getPaginatedEditRequests')
            ->with($cleanedFilters, $cleanedSorting, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedEditRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_paginated_edit_requests_with_only_type_filter(): void
    {
        // Arrange
        $filters = ['type' => 'photo_suggestion'];
        $sorting = [];
        $pagination = [];

        $cleanedFilters = ['search' => '', 'type' => 'photo_suggestion', 'status' => ''];
        $cleanedSorting = ['column' => 'created_at', 'direction' => 'desc'];
        $perPage = 20;

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->filterValidation->shouldReceive('validateAndClean')
            ->with($filters)
            ->andReturn($cleanedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->with($sorting)
            ->andReturn($cleanedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->with($pagination)
            ->andReturn($perPage);

        $this->repository->shouldReceive('getPaginatedEditRequests')
            ->with($cleanedFilters, $cleanedSorting, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedEditRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_paginated_edit_requests_with_custom_sorting(): void
    {
        // Arrange
        $filters = [];
        $sorting = ['sortBy' => 'status', 'sortDirection' => 'asc'];
        $pagination = [];

        $cleanedFilters = ['search' => '', 'type' => '', 'status' => ''];
        $cleanedSorting = ['column' => 'status', 'direction' => 'asc'];
        $perPage = 20;

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->filterValidation->shouldReceive('validateAndClean')
            ->with($filters)
            ->andReturn($cleanedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->with($sorting)
            ->andReturn($cleanedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->with($pagination)
            ->andReturn($perPage);

        $this->repository->shouldReceive('getPaginatedEditRequests')
            ->with($cleanedFilters, $cleanedSorting, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedEditRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    public function test_get_paginated_edit_requests_with_custom_pagination(): void
    {
        // Arrange
        $filters = [];
        $sorting = [];
        $pagination = ['perPage' => 50];

        $cleanedFilters = ['search' => '', 'type' => '', 'status' => ''];
        $cleanedSorting = ['column' => 'created_at', 'direction' => 'desc'];
        $perPage = 50;

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->filterValidation->shouldReceive('validateAndClean')
            ->with($filters)
            ->andReturn($cleanedFilters);

        $this->sortingValidation->shouldReceive('validateAndClean')
            ->with($sorting)
            ->andReturn($cleanedSorting);

        $this->paginationValidation->shouldReceive('validateAndClean')
            ->with($pagination)
            ->andReturn($perPage);

        $this->repository->shouldReceive('getPaginatedEditRequests')
            ->with($cleanedFilters, $cleanedSorting, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->service->getPaginatedEditRequests($filters, $sorting, $pagination);

        // Assert
        $this->assertSame($expectedPaginator, $result);
    }

    // ========================================
    // Default Values Tests
    // ========================================

    public function test_get_default_filters_returns_correct_structure(): void
    {
        // Act
        $result = $this->service->getDefaultFilters();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('search', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals('', $result['search']);
        $this->assertEquals('', $result['type']);
        $this->assertEquals('', $result['status']);
    }

    public function test_get_default_sorting_delegates_to_sorting_validation(): void
    {
        // Arrange
        $expectedSorting = ['column' => 'created_at', 'direction' => 'desc'];

        $this->sortingValidation->shouldReceive('getDefaultSort')
            ->once()
            ->andReturn($expectedSorting);

        // Act
        $result = $this->service->getDefaultSorting();

        // Assert
        $this->assertEquals($expectedSorting, $result);
    }

    public function test_get_default_per_page_delegates_to_pagination_validation(): void
    {
        // Arrange
        $expectedPerPage = 20;

        $this->paginationValidation->shouldReceive('getDefaultValue')
            ->once()
            ->andReturn($expectedPerPage);

        // Act
        $result = $this->service->getDefaultPerPage();

        // Assert
        $this->assertEquals($expectedPerPage, $result);
    }

    public function test_get_default_filters_returns_three_keys_only(): void
    {
        // Act
        $result = $this->service->getDefaultFilters();

        // Assert
        $this->assertCount(3, $result);
    }
}
