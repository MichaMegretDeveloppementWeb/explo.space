<?php

namespace Tests\Unit\Services\Admin\Place\PlaceList;

use App\Services\Admin\Place\PlaceList\PlaceListSortingValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaceListSortingValidationServiceTest extends TestCase
{
    protected PlaceListSortingValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlaceListSortingValidationService;
    }

    public function test_validate_and_clean_with_valid_data(): void
    {
        // Arrange
        $sorting = [
            'sortBy' => 'title',
            'sortDirection' => 'asc',
        ];

        // Act
        $result = $this->service->validateAndClean($sorting);

        // Assert
        $this->assertEquals('title', $result['column']);
        $this->assertEquals('asc', $result['direction']);
    }

    public function test_validate_and_clean_with_missing_fields_uses_defaults(): void
    {
        // Arrange
        $sorting = [];

        // Act
        $result = $this->service->validateAndClean($sorting);

        // Assert
        $this->assertEquals('created_at', $result['column']);
        $this->assertEquals('desc', $result['direction']);
    }

    public function test_validate_and_clean_rejects_invalid_column(): void
    {
        // Arrange
        $sorting = [
            'sortBy' => 'invalid_column',
            'sortDirection' => 'asc',
        ];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->validateAndClean($sorting);
    }

    public function test_validate_and_clean_rejects_invalid_direction(): void
    {
        // Arrange
        $sorting = [
            'sortBy' => 'title',
            'sortDirection' => 'invalid',
        ];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->validateAndClean($sorting);
    }

    public function test_get_allowed_columns_returns_array(): void
    {
        // Act
        $result = $this->service->getAllowedColumns();

        // Assert
        $this->assertIsArray($result);
        $this->assertContains('title', $result);
        $this->assertContains('created_at', $result);
        $this->assertContains('updated_at', $result);
        $this->assertContains('is_featured', $result);
    }

    public function test_get_allowed_directions_returns_array(): void
    {
        // Act
        $result = $this->service->getAllowedDirections();

        // Assert
        $this->assertIsArray($result);
        $this->assertContains('asc', $result);
        $this->assertContains('desc', $result);
    }

    public function test_get_default_sort_returns_correct_structure(): void
    {
        // Act
        $result = $this->service->getDefaultSort();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('column', $result);
        $this->assertArrayHasKey('direction', $result);
        $this->assertEquals('created_at', $result['column']);
        $this->assertEquals('desc', $result['direction']);
    }
}
