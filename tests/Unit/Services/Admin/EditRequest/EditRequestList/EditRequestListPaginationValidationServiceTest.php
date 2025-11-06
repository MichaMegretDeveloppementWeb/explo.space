<?php

namespace Tests\Unit\Services\Admin\EditRequest\EditRequestList;

use App\Services\Admin\EditRequest\EditRequestList\EditRequestListPaginationValidationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EditRequestListPaginationValidationServiceTest extends TestCase
{
    protected EditRequestListPaginationValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new EditRequestListPaginationValidationService;
    }

    // ========================================
    // Valid Pagination Validation
    // ========================================

    public function test_validates_per_page_10(): void
    {
        $pagination = ['perPage' => 10];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(10, $validated);
    }

    public function test_validates_per_page_20(): void
    {
        $pagination = ['perPage' => 20];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(20, $validated);
    }

    public function test_validates_per_page_50(): void
    {
        $pagination = ['perPage' => 50];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(50, $validated);
    }

    public function test_validates_per_page_100(): void
    {
        $pagination = ['perPage' => 100];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(100, $validated);
    }

    public function test_returns_default_when_empty(): void
    {
        $pagination = [];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(20, $validated);
    }

    public function test_returns_default_when_per_page_is_null(): void
    {
        $pagination = ['perPage' => null];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(20, $validated);
    }

    // ========================================
    // Invalid Pagination Validation
    // ========================================

    public function test_throws_exception_for_invalid_per_page_5(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => 5];

        $this->service->validate($pagination);
    }

    public function test_throws_exception_for_invalid_per_page_15(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => 15];

        $this->service->validate($pagination);
    }

    public function test_throws_exception_for_invalid_per_page_200(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => 200];

        $this->service->validate($pagination);
    }

    public function test_throws_exception_for_negative_per_page(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => -10];

        $this->service->validate($pagination);
    }

    public function test_throws_exception_for_zero_per_page(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => 0];

        $this->service->validate($pagination);
    }

    public function test_throws_exception_for_non_integer_per_page(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => 'invalid'];

        $this->service->validate($pagination);
    }

    public function test_throws_exception_for_float_per_page(): void
    {
        $this->expectException(ValidationException::class);

        $pagination = ['perPage' => 20.5];

        $this->service->validate($pagination);
    }

    // ========================================
    // Helper Methods
    // ========================================

    public function test_get_allowed_values_returns_correct_array(): void
    {
        $allowedValues = $this->service->getAllowedValues();

        $this->assertEquals([10, 20, 50, 100], $allowedValues);
    }

    public function test_get_default_value_returns_20(): void
    {
        $defaultValue = $this->service->getDefaultValue();

        $this->assertEquals(20, $defaultValue);
    }

    // ========================================
    // Edge Cases
    // ========================================

    public function test_ignores_extra_keys(): void
    {
        $pagination = [
            'perPage' => 50,
            'extra_key' => 'should_be_ignored',
        ];

        $validated = $this->service->validate($pagination);

        $this->assertEquals(50, $validated);
    }

    public function test_returns_integer_type(): void
    {
        $pagination = ['perPage' => 20];

        $validated = $this->service->validate($pagination);

        $this->assertIsInt($validated);
    }
}
