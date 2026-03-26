<?php

namespace Tests\Unit\Enums;

use App\Enums\AutofillWorkflowState;
use Tests\TestCase;

class AutofillWorkflowStateTest extends TestCase
{
    public function test_enum_has_all_expected_cases(): void
    {
        $cases = AutofillWorkflowState::cases();

        $this->assertCount(4, $cases);
    }

    public function test_labels_return_french_text(): void
    {
        $this->assertEquals('Actif', AutofillWorkflowState::Active->label());
        $this->assertEquals('En pause', AutofillWorkflowState::Paused->label());
        $this->assertEquals('Abandonné', AutofillWorkflowState::Abandoned->label());
        $this->assertEquals('Terminé', AutofillWorkflowState::Completed->label());
    }

    public function test_current_includes_active_and_paused(): void
    {
        $this->assertTrue(AutofillWorkflowState::Active->isCurrent());
        $this->assertTrue(AutofillWorkflowState::Paused->isCurrent());
        $this->assertFalse(AutofillWorkflowState::Completed->isCurrent());
        $this->assertFalse(AutofillWorkflowState::Abandoned->isCurrent());
    }

    public function test_dismissed_includes_completed_and_abandoned(): void
    {
        $this->assertTrue(AutofillWorkflowState::Completed->isDismissed());
        $this->assertTrue(AutofillWorkflowState::Abandoned->isDismissed());
        $this->assertFalse(AutofillWorkflowState::Active->isDismissed());
        $this->assertFalse(AutofillWorkflowState::Paused->isDismissed());
    }

    public function test_enum_can_be_instantiated_from_value(): void
    {
        $state = AutofillWorkflowState::from('paused');

        $this->assertEquals(AutofillWorkflowState::Paused, $state);
    }

    public function test_enum_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(AutofillWorkflowState::tryFrom('invalid'));
    }
}
