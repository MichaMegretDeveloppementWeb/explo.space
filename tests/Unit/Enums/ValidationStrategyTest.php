<?php

namespace Tests\Unit\Enums;

use App\Enums\ValidationStrategy;
use Tests\TestCase;

class ValidationStrategyTest extends TestCase
{
    public function test_enum_has_throw_case(): void
    {
        $this->assertEquals('throw', ValidationStrategy::THROW->value);
    }

    public function test_enum_has_correct_silently_case(): void
    {
        $this->assertEquals('correct_silently', ValidationStrategy::CORRECT_SILENTLY->value);
    }

    public function test_enum_has_collect_errors_case(): void
    {
        $this->assertEquals('collect_errors', ValidationStrategy::COLLECT_ERRORS->value);
    }

    public function test_enum_can_be_instantiated_from_value(): void
    {
        $strategy = ValidationStrategy::from('throw');

        $this->assertInstanceOf(ValidationStrategy::class, $strategy);
        $this->assertEquals(ValidationStrategy::THROW, $strategy);
    }

    public function test_enum_try_from_returns_null_for_invalid_value(): void
    {
        $strategy = ValidationStrategy::tryFrom('invalid_strategy');

        $this->assertNull($strategy);
    }

    public function test_enum_all_cases_are_accessible(): void
    {
        $cases = ValidationStrategy::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(ValidationStrategy::THROW, $cases);
        $this->assertContains(ValidationStrategy::CORRECT_SILENTLY, $cases);
        $this->assertContains(ValidationStrategy::COLLECT_ERRORS, $cases);
    }
}
