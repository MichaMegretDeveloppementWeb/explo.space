<?php

namespace Tests\Unit\Services\Admin\Place\PlaceList;

use App\Services\Admin\Place\PlaceList\PlaceListPaginationValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaceListPaginationValidationServiceTest extends TestCase
{
    protected PlaceListPaginationValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlaceListPaginationValidationService;
    }

    public function test_validate_and_clean_with_valid_value(): void
    {
        // Arrange
        $pagination = ['perPage' => 20];

        // Act
        $result = $this->service->validate($pagination);

        // Assert
        $this->assertEquals(20, $result);
    }

    public function test_validate_and_clean_with_missing_field_uses_default(): void
    {
        // Arrange
        $pagination = [];

        // Act
        $result = $this->service->validate($pagination);

        // Assert
        $this->assertEquals(20, $result);
    }

    public function test_validate_and_clean_rejects_invalid_value(): void
    {
        // Arrange
        $pagination = ['perPage' => 100];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->validate($pagination);
    }

    public function test_get_allowed_values_returns_array(): void
    {
        // Act
        $result = $this->service->getAllowedValues();

        // Assert
        $this->assertIsArray($result);
        $this->assertContains(10, $result);
        $this->assertContains(20, $result);
        $this->assertContains(30, $result);
        $this->assertContains(50, $result);
    }

    public function test_get_default_value_returns_correct_value(): void
    {
        // Act
        $result = $this->service->getDefaultValue();

        // Assert
        $this->assertEquals(20, $result);
    }
}
