<?php

namespace Tests\Unit\Services\Admin\Place\PlaceList;

use App\Services\Admin\Place\PlaceList\PlaceListFilterValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaceListFilterValidationServiceTest extends TestCase
{
    protected PlaceListFilterValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlaceListFilterValidationService;
    }

    public function test_validate_and_clean_with_valid_data(): void
    {
        // Arrange
        $filters = [
            'search' => 'Kennedy',
            'tags' => ['nasa', 'spacex'],
            'locale' => 'fr',
        ];

        // Act
        $result = $this->service->validateAndClean($filters);

        // Assert
        $this->assertEquals('Kennedy', $result['search']);
        $this->assertEquals(['nasa', 'spacex'], $result['tags']);
        $this->assertEquals('fr', $result['locale']);
    }

    public function test_validate_and_clean_trims_search(): void
    {
        // Arrange
        $filters = [
            'search' => '  Kennedy  ',
            'tags' => [],
            'locale' => 'fr',
        ];

        // Act
        $result = $this->service->validateAndClean($filters);

        // Assert
        $this->assertEquals('Kennedy', $result['search']);
    }

    public function test_validate_and_clean_with_missing_fields_uses_defaults(): void
    {
        // Arrange
        $filters = [];

        // Act
        $result = $this->service->validateAndClean($filters);

        // Assert
        $this->assertEquals('', $result['search']);
        $this->assertEquals([], $result['tags']);
        $this->assertEquals('fr', $result['locale']);
    }

    public function test_validate_and_clean_rejects_invalid_locale(): void
    {
        // Arrange
        $filters = [
            'search' => '',
            'tags' => [],
            'locale' => 'invalid',
        ];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->validateAndClean($filters);
    }

    public function test_validate_and_clean_rejects_search_too_long(): void
    {
        // Arrange
        $filters = [
            'search' => str_repeat('a', 256),
            'tags' => [],
            'locale' => 'fr',
        ];

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->validateAndClean($filters);
    }

    public function test_are_filters_empty_returns_true_when_empty(): void
    {
        // Arrange
        $filters = [
            'search' => '',
            'tags' => [],
            'locale' => 'fr',
        ];

        // Act
        $result = $this->service->areFiltersEmpty($filters);

        // Assert
        $this->assertTrue($result);
    }

    public function test_are_filters_empty_returns_false_when_search_present(): void
    {
        // Arrange
        $filters = [
            'search' => 'Kennedy',
            'tags' => [],
            'locale' => 'fr',
        ];

        // Act
        $result = $this->service->areFiltersEmpty($filters);

        // Assert
        $this->assertFalse($result);
    }

    public function test_are_filters_empty_returns_false_when_tags_present(): void
    {
        // Arrange
        $filters = [
            'search' => '',
            'tags' => ['nasa'],
            'locale' => 'fr',
        ];

        // Act
        $result = $this->service->areFiltersEmpty($filters);

        // Assert
        $this->assertFalse($result);
    }
}
