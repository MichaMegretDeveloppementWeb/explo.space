<?php

namespace Tests\Unit\Services\Admin\PlaceRequest\PlaceRequestList;

use App\Services\Admin\PlaceRequest\PlaceRequestList\PlaceRequestListFilterValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaceRequestListFilterValidationServiceTest extends TestCase
{
    protected PlaceRequestListFilterValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PlaceRequestListFilterValidationService;
    }

    // ========================================
    // Valid Filter Validation
    // ========================================

    public function test_validates_empty_filters(): void
    {
        $filters = [];

        $validated = $this->service->validateAndClean($filters);

        $this->assertIsArray($validated);
        $this->assertArrayHasKey('status', $validated);
        $this->assertEquals([], $validated['status']);
    }

    public function test_validates_single_status_as_array(): void
    {
        $filters = ['status' => ['pending']];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals(['pending'], $validated['status']);
    }

    public function test_validates_multiple_statuses_as_array(): void
    {
        $filters = ['status' => ['pending', 'submitted']];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals(['pending', 'submitted'], $validated['status']);
    }

    public function test_validates_all_valid_statuses(): void
    {
        $filters = ['status' => ['submitted', 'pending', 'accepted', 'refused']];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals(['submitted', 'pending', 'accepted', 'refused'], $validated['status']);
    }

    // ========================================
    // String to Array Normalization
    // ========================================

    public function test_normalizes_comma_separated_string_to_array(): void
    {
        $filters = ['status' => 'pending,submitted'];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals(['pending', 'submitted'], $validated['status']);
    }

    public function test_normalizes_single_status_string_to_array(): void
    {
        $filters = ['status' => 'pending'];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals(['pending'], $validated['status']);
    }

    public function test_normalizes_string_with_spaces(): void
    {
        $filters = ['status' => 'pending, submitted, accepted'];

        $validated = $this->service->validateAndClean($filters);

        // Should trim spaces
        $this->assertEquals(['pending', 'submitted', 'accepted'], $validated['status']);
    }

    public function test_handles_empty_string(): void
    {
        $filters = ['status' => ''];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals([], $validated['status']);
    }

    public function test_handles_all_keyword(): void
    {
        $filters = ['status' => 'all'];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals([], $validated['status']);
    }

    public function test_filters_empty_values_after_splitting(): void
    {
        $filters = ['status' => 'pending,,submitted,,'];

        $validated = $this->service->validateAndClean($filters);

        // Should filter out empty strings
        $this->assertEquals(['pending', 'submitted'], $validated['status']);
    }

    // ========================================
    // Invalid Status Validation
    // ========================================

    public function test_throws_exception_for_invalid_status(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['status' => ['invalid_status']];

        $this->service->validateAndClean($filters);
    }

    public function test_throws_exception_for_partially_invalid_statuses(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['status' => ['pending', 'invalid', 'submitted']];

        $this->service->validateAndClean($filters);
    }

    public function test_throws_exception_for_invalid_string_status(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['status' => 'invalid'];

        $this->service->validateAndClean($filters);
    }

    public function test_throws_exception_for_invalid_comma_separated_statuses(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['status' => 'pending,invalid,submitted'];

        $this->service->validateAndClean($filters);
    }

    // ========================================
    // Empty Filter Detection
    // ========================================

    public function test_are_filters_empty_returns_true_for_empty_status(): void
    {
        $filters = ['status' => []];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertTrue($isEmpty);
    }

    public function test_are_filters_empty_returns_true_for_missing_status(): void
    {
        $filters = [];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertTrue($isEmpty);
    }

    public function test_are_filters_empty_returns_false_for_single_status(): void
    {
        $filters = ['status' => ['pending']];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertFalse($isEmpty);
    }

    public function test_are_filters_empty_returns_false_for_multiple_statuses(): void
    {
        $filters = ['status' => ['pending', 'submitted']];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertFalse($isEmpty);
    }

    // ========================================
    // Edge Cases
    // ========================================

    public function test_handles_null_status(): void
    {
        $filters = ['status' => null];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals([], $validated['status']);
    }

    public function test_preserves_status_order(): void
    {
        $filters = ['status' => ['refused', 'accepted', 'pending', 'submitted']];

        $validated = $this->service->validateAndClean($filters);

        $this->assertEquals(['refused', 'accepted', 'pending', 'submitted'], $validated['status']);
    }

    public function test_handles_duplicate_statuses(): void
    {
        $filters = ['status' => ['pending', 'pending', 'submitted']];

        $validated = $this->service->validateAndClean($filters);

        // Should keep duplicates as-is (repository can handle this)
        $this->assertEquals(['pending', 'pending', 'submitted'], $validated['status']);
    }

    public function test_handles_comma_separated_duplicates(): void
    {
        $filters = ['status' => 'pending,pending,submitted'];

        $validated = $this->service->validateAndClean($filters);

        // Should keep duplicates after normalization
        $this->assertEquals(['pending', 'pending', 'submitted'], $validated['status']);
    }

    // ========================================
    // Return Structure
    // ========================================

    public function test_returns_correct_array_structure(): void
    {
        $filters = ['status' => ['pending']];

        $validated = $this->service->validateAndClean($filters);

        $this->assertIsArray($validated);
        $this->assertArrayHasKey('status', $validated);
        $this->assertIsArray($validated['status']);
    }

    public function test_only_returns_status_key(): void
    {
        $filters = [
            'status' => ['pending'],
            'extra_key' => 'should_be_ignored',
        ];

        $validated = $this->service->validateAndClean($filters);

        $this->assertCount(1, $validated);
        $this->assertArrayHasKey('status', $validated);
        $this->assertArrayNotHasKey('extra_key', $validated);
    }
}
