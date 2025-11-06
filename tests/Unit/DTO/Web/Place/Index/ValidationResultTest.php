<?php

namespace Tests\Unit\DTO\Web\Place\Index;

use App\DTO\Web\Place\Index\ValidationResult;
use Tests\TestCase;

class ValidationResultTest extends TestCase
{
    public function test_can_be_instantiated_as_valid(): void
    {
        $result = new ValidationResult(
            isValid: true,
            errors: [],
            correctedFilters: ['mode' => 'proximity'],
            originalFilters: ['mode' => 'proximity']
        );

        $this->assertTrue($result->isValid);
        $this->assertFalse($result->hasErrors());
    }

    public function test_can_be_instantiated_as_invalid(): void
    {
        $result = new ValidationResult(
            isValid: false,
            errors: ['mode' => 'invalid_mode'],
            correctedFilters: ['mode' => 'proximity'],
            originalFilters: ['mode' => 'invalid']
        );

        $this->assertFalse($result->isValid);
        $this->assertTrue($result->hasErrors());
    }

    public function test_has_error_detects_specific_error(): void
    {
        $result = new ValidationResult(
            isValid: false,
            errors: ['radius' => 'invalid_radius', 'mode' => 'invalid_mode'],
            correctedFilters: [],
            originalFilters: []
        );

        $this->assertTrue($result->hasError('radius'));
        $this->assertTrue($result->hasError('mode'));
        $this->assertFalse($result->hasError('latitude'));
    }

    public function test_get_error_returns_error_code(): void
    {
        $result = new ValidationResult(
            isValid: false,
            errors: ['radius' => 'invalid_radius'],
            correctedFilters: [],
            originalFilters: []
        );

        $this->assertEquals('invalid_radius', $result->getError('radius'));
        $this->assertNull($result->getError('mode'));
    }

    public function test_get_error_codes_returns_all_codes(): void
    {
        $result = new ValidationResult(
            isValid: false,
            errors: ['radius' => 'invalid_radius', 'mode' => 'invalid_mode'],
            correctedFilters: [],
            originalFilters: []
        );

        $codes = $result->getErrorCodes();

        $this->assertCount(2, $codes);
        $this->assertContains('invalid_radius', $codes);
        $this->assertContains('invalid_mode', $codes);
    }

    public function test_get_error_count_returns_correct_count(): void
    {
        $result = new ValidationResult(
            isValid: false,
            errors: ['a' => 'error1', 'b' => 'error2', 'c' => 'error3'],
            correctedFilters: [],
            originalFilters: []
        );

        $this->assertEquals(3, $result->getErrorCount());
    }

    public function test_has_errors_returns_true_when_invalid(): void
    {
        $result = new ValidationResult(
            isValid: false,
            errors: ['mode' => 'invalid'],
            correctedFilters: [],
            originalFilters: []
        );

        $this->assertTrue($result->hasErrors());
    }

    public function test_has_errors_returns_false_when_valid(): void
    {
        $result = new ValidationResult(
            isValid: true,
            errors: [],
            correctedFilters: [],
            originalFilters: []
        );

        $this->assertFalse($result->hasErrors());
    }
}
