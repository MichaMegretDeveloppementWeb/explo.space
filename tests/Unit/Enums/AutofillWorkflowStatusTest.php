<?php

namespace Tests\Unit\Enums;

use App\Enums\AutofillWorkflowStatus;
use Tests\TestCase;

class AutofillWorkflowStatusTest extends TestCase
{
    public function test_enum_has_all_expected_cases(): void
    {
        $cases = AutofillWorkflowStatus::cases();

        $this->assertCount(8, $cases);
    }

    public function test_awaiting_statuses_are_identified(): void
    {
        $this->assertTrue(AutofillWorkflowStatus::AwaitingSelection->isAwaiting());
        $this->assertTrue(AutofillWorkflowStatus::AwaitingImages->isAwaiting());
        $this->assertFalse(AutofillWorkflowStatus::Discovering->isAwaiting());
        $this->assertFalse(AutofillWorkflowStatus::Pending->isAwaiting());
        $this->assertFalse(AutofillWorkflowStatus::Enriching->isAwaiting());
        $this->assertFalse(AutofillWorkflowStatus::Completed->isAwaiting());
    }

    public function test_labels_return_french_text(): void
    {
        $this->assertEquals('En attente', AutofillWorkflowStatus::Pending->label());
        $this->assertEquals('Terminé', AutofillWorkflowStatus::Completed->label());
        $this->assertEquals('Recherche en cours', AutofillWorkflowStatus::Discovering->label());
        $this->assertEquals('Sélection requise', AutofillWorkflowStatus::AwaitingSelection->label());
        $this->assertEquals('Enrichissement en cours', AutofillWorkflowStatus::Enriching->label());
    }

    public function test_enum_can_be_instantiated_from_value(): void
    {
        $status = AutofillWorkflowStatus::from('discovering');

        $this->assertEquals(AutofillWorkflowStatus::Discovering, $status);
    }

    public function test_enum_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(AutofillWorkflowStatus::tryFrom('invalid'));
    }
}
