<?php

namespace Tests\Unit\Services\Admin\EditRequest\EditRequestList;

use App\Services\Admin\EditRequest\EditRequestList\EditRequestListFilterValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EditRequestListFilterValidationServiceTest extends TestCase
{
    protected EditRequestListFilterValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new EditRequestListFilterValidationService;
    }

    // ========================================
    // Valid Filter Validation
    // ========================================

    public function test_validates_empty_filters(): void
    {
        $filters = [];

        $validated = $this->service->validate($filters);

        $this->assertIsArray($validated);
        $this->assertArrayHasKey('search', $validated);
        $this->assertArrayHasKey('type', $validated);
        $this->assertArrayHasKey('status', $validated);
        $this->assertEquals('', $validated['search']);
        $this->assertEquals('', $validated['type']);
        $this->assertEquals([], $validated['status']);
    }

    public function test_validates_search_filter(): void
    {
        $filters = ['search' => 'Centre Kennedy'];

        $validated = $this->service->validate($filters);

        $this->assertEquals('Centre Kennedy', $validated['search']);
    }

    public function test_validates_type_modification(): void
    {
        $filters = ['type' => 'modification'];

        $validated = $this->service->validate($filters);

        $this->assertEquals('modification', $validated['type']);
    }

    public function test_validates_type_signalement(): void
    {
        $filters = ['type' => 'signalement'];

        $validated = $this->service->validate($filters);

        $this->assertEquals('signalement', $validated['type']);
    }

    public function test_validates_type_photo_suggestion(): void
    {
        $filters = ['type' => 'photo_suggestion'];

        $validated = $this->service->validate($filters);

        $this->assertEquals('photo_suggestion', $validated['type']);
    }

    public function test_validates_status_submitted(): void
    {
        $filters = ['status' => ['submitted']];

        $validated = $this->service->validate($filters);

        $this->assertEquals(['submitted'], $validated['status']);
    }

    public function test_validates_status_pending(): void
    {
        $filters = ['status' => ['pending']];

        $validated = $this->service->validate($filters);

        $this->assertEquals(['pending'], $validated['status']);
    }

    public function test_validates_status_accepted(): void
    {
        $filters = ['status' => ['accepted']];

        $validated = $this->service->validate($filters);

        $this->assertEquals(['accepted'], $validated['status']);
    }

    public function test_validates_status_refused(): void
    {
        $filters = ['status' => ['refused']];

        $validated = $this->service->validate($filters);

        $this->assertEquals(['refused'], $validated['status']);
    }

    public function test_validates_all_filters_together(): void
    {
        $filters = [
            'search' => 'test@example.com',
            'type' => 'modification',
            'status' => ['pending'],
        ];

        $validated = $this->service->validate($filters);

        $this->assertEquals('test@example.com', $validated['search']);
        $this->assertEquals('modification', $validated['type']);
        $this->assertEquals(['pending'], $validated['status']);
    }

    // ========================================
    // Search Normalization
    // ========================================

    public function test_trims_search_input(): void
    {
        $filters = ['search' => '  Centre Kennedy  '];

        $validated = $this->service->validate($filters);

        $this->assertEquals('Centre Kennedy', $validated['search']);
    }

    public function test_handles_empty_search_string(): void
    {
        $filters = ['search' => ''];

        $validated = $this->service->validate($filters);

        $this->assertEquals('', $validated['search']);
    }

    public function test_handles_whitespace_only_search(): void
    {
        $filters = ['search' => '   '];

        $validated = $this->service->validate($filters);

        $this->assertEquals('', $validated['search']);
    }

    // ========================================
    // Invalid Filter Validation
    // ========================================

    public function test_throws_exception_for_invalid_type(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['type' => 'invalid_type'];

        $this->service->validate($filters);
    }

    public function test_throws_exception_for_invalid_status(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['status' => 'invalid_status'];

        $this->service->validate($filters);
    }

    public function test_throws_exception_for_search_too_long(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['search' => str_repeat('a', 256)];

        $this->service->validate($filters);
    }

    public function test_throws_exception_for_non_string_search(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['search' => 123];

        $this->service->validate($filters);
    }

    public function test_throws_exception_for_non_string_type(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['type' => ['modification']];

        $this->service->validate($filters);
    }

    public function test_throws_exception_for_invalid_status_value_in_array(): void
    {
        $this->expectException(ValidationException::class);

        $filters = ['status' => ['invalid_status']];

        $this->service->validate($filters);
    }

    // ========================================
    // Empty Filter Detection
    // ========================================

    public function test_are_filters_empty_returns_true_for_empty_filters(): void
    {
        $filters = [
            'search' => '',
            'type' => '',
            'status' => '',
        ];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertTrue($isEmpty);
    }

    public function test_are_filters_empty_returns_false_with_search(): void
    {
        $filters = [
            'search' => 'test',
            'type' => '',
            'status' => '',
        ];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertFalse($isEmpty);
    }

    public function test_are_filters_empty_returns_false_with_type(): void
    {
        $filters = [
            'search' => '',
            'type' => 'modification',
            'status' => '',
        ];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertFalse($isEmpty);
    }

    public function test_are_filters_empty_returns_false_with_status(): void
    {
        $filters = [
            'search' => '',
            'type' => '',
            'status' => ['pending'],
        ];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertFalse($isEmpty);
    }

    public function test_are_filters_empty_returns_false_with_all_filters(): void
    {
        $filters = [
            'search' => 'test',
            'type' => 'modification',
            'status' => ['pending'],
        ];

        $isEmpty = $this->service->areFiltersEmpty($filters);

        $this->assertFalse($isEmpty);
    }

    // ========================================
    // Edge Cases
    // ========================================

    public function test_handles_null_values(): void
    {
        $filters = [
            'search' => null,
            'type' => null,
            'status' => null,
        ];

        $validated = $this->service->validate($filters);

        $this->assertEquals('', $validated['search']);
        $this->assertEquals('', $validated['type']);
        $this->assertEquals([], $validated['status']);
    }

    public function test_ignores_extra_keys(): void
    {
        $filters = [
            'search' => 'test',
            'type' => 'modification',
            'status' => ['pending'],
            'extra_key' => 'should_be_ignored',
        ];

        $validated = $this->service->validate($filters);

        $this->assertCount(3, $validated);
        $this->assertArrayHasKey('search', $validated);
        $this->assertArrayHasKey('type', $validated);
        $this->assertArrayHasKey('status', $validated);
        $this->assertArrayNotHasKey('extra_key', $validated);
    }

    // ========================================
    // Return Structure
    // ========================================

    public function test_returns_correct_array_structure(): void
    {
        $filters = ['search' => 'test'];

        $validated = $this->service->validate($filters);

        $this->assertIsArray($validated);
        $this->assertArrayHasKey('search', $validated);
        $this->assertArrayHasKey('type', $validated);
        $this->assertArrayHasKey('status', $validated);
    }

    public function test_always_returns_all_three_keys(): void
    {
        $filters = [];

        $validated = $this->service->validate($filters);

        $this->assertCount(3, $validated);
        $this->assertArrayHasKey('search', $validated);
        $this->assertArrayHasKey('type', $validated);
        $this->assertArrayHasKey('status', $validated);
    }
}
