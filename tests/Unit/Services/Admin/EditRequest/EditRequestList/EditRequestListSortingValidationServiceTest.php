<?php

namespace Tests\Unit\Services\Admin\EditRequest\EditRequestList;

use App\Services\Admin\EditRequest\EditRequestList\EditRequestListSortingValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EditRequestListSortingValidationServiceTest extends TestCase
{
    protected EditRequestListSortingValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new EditRequestListSortingValidationService;
    }

    // ========================================
    // Valid Sorting Validation
    // ========================================

    public function test_validates_sort_by_place_asc(): void
    {
        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => 'asc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('place', $validated['column']);
        $this->assertEquals('asc', $validated['direction']);
    }

    public function test_validates_sort_by_type_desc(): void
    {
        $sorting = [
            'sortBy' => 'type',
            'sortDirection' => 'desc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('type', $validated['column']);
        $this->assertEquals('desc', $validated['direction']);
    }

    public function test_validates_sort_by_contact_email(): void
    {
        $sorting = [
            'sortBy' => 'contact_email',
            'sortDirection' => 'asc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('contact_email', $validated['column']);
    }

    public function test_validates_sort_by_status(): void
    {
        $sorting = [
            'sortBy' => 'status',
            'sortDirection' => 'asc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('status', $validated['column']);
    }

    public function test_validates_sort_by_created_at(): void
    {
        $sorting = [
            'sortBy' => 'created_at',
            'sortDirection' => 'desc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('created_at', $validated['column']);
    }

    public function test_returns_default_when_empty(): void
    {
        $sorting = [];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('created_at', $validated['column']);
        $this->assertEquals('desc', $validated['direction']);
    }

    public function test_returns_default_column_when_sort_by_is_null(): void
    {
        $sorting = [
            'sortBy' => null,
            'sortDirection' => 'asc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('created_at', $validated['column']);
        $this->assertEquals('asc', $validated['direction']);
    }

    public function test_returns_default_direction_when_sort_direction_is_null(): void
    {
        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => null,
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertEquals('place', $validated['column']);
        $this->assertEquals('desc', $validated['direction']);
    }

    // ========================================
    // Invalid Sorting Validation
    // ========================================

    public function test_throws_exception_for_invalid_sort_by(): void
    {
        $this->expectException(ValidationException::class);

        $sorting = [
            'sortBy' => 'invalid_column',
            'sortDirection' => 'asc',
        ];

        $this->service->validateAndClean($sorting);
    }

    public function test_throws_exception_for_invalid_sort_direction(): void
    {
        $this->expectException(ValidationException::class);

        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => 'invalid_direction',
        ];

        $this->service->validateAndClean($sorting);
    }

    public function test_throws_exception_for_non_string_sort_by(): void
    {
        $this->expectException(ValidationException::class);

        $sorting = [
            'sortBy' => 123,
            'sortDirection' => 'asc',
        ];

        $this->service->validateAndClean($sorting);
    }

    public function test_throws_exception_for_non_string_sort_direction(): void
    {
        $this->expectException(ValidationException::class);

        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => 123,
        ];

        $this->service->validateAndClean($sorting);
    }

    public function test_throws_exception_for_uppercase_direction(): void
    {
        $this->expectException(ValidationException::class);

        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => 'ASC',
        ];

        $this->service->validateAndClean($sorting);
    }

    // ========================================
    // Helper Methods
    // ========================================

    public function test_get_allowed_columns_returns_correct_array(): void
    {
        $allowedColumns = $this->service->getAllowedColumns();

        $this->assertEquals(['place', 'type', 'contact_email', 'status', 'created_at'], $allowedColumns);
    }

    public function test_get_allowed_directions_returns_correct_array(): void
    {
        $allowedDirections = $this->service->getAllowedDirections();

        $this->assertEquals(['asc', 'desc'], $allowedDirections);
    }

    public function test_get_default_sort_returns_correct_array(): void
    {
        $defaultSort = $this->service->getDefaultSort();

        $this->assertEquals([
            'column' => 'created_at',
            'direction' => 'desc',
        ], $defaultSort);
    }

    // ========================================
    // Edge Cases
    // ========================================

    public function test_ignores_extra_keys(): void
    {
        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => 'asc',
            'extra_key' => 'should_be_ignored',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertCount(2, $validated);
        $this->assertArrayHasKey('column', $validated);
        $this->assertArrayHasKey('direction', $validated);
    }

    // ========================================
    // Return Structure
    // ========================================

    public function test_returns_correct_array_structure(): void
    {
        $sorting = [
            'sortBy' => 'place',
            'sortDirection' => 'asc',
        ];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertIsArray($validated);
        $this->assertArrayHasKey('column', $validated);
        $this->assertArrayHasKey('direction', $validated);
    }

    public function test_always_returns_both_keys(): void
    {
        $sorting = [];

        $validated = $this->service->validateAndClean($sorting);

        $this->assertCount(2, $validated);
        $this->assertArrayHasKey('column', $validated);
        $this->assertArrayHasKey('direction', $validated);
    }

    // ========================================
    // All Columns Validation
    // ========================================

    public function test_validates_all_allowed_columns(): void
    {
        $columns = ['place', 'type', 'contact_email', 'status', 'created_at'];

        foreach ($columns as $column) {
            $sorting = [
                'sortBy' => $column,
                'sortDirection' => 'asc',
            ];

            $validated = $this->service->validateAndClean($sorting);

            $this->assertEquals($column, $validated['column'], "Column $column should be valid");
        }
    }

    public function test_validates_both_directions(): void
    {
        $directions = ['asc', 'desc'];

        foreach ($directions as $direction) {
            $sorting = [
                'sortBy' => 'place',
                'sortDirection' => $direction,
            ];

            $validated = $this->service->validateAndClean($sorting);

            $this->assertEquals($direction, $validated['direction'], "Direction $direction should be valid");
        }
    }
}
