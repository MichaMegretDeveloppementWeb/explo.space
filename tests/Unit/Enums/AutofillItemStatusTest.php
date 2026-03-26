<?php

namespace Tests\Unit\Enums;

use App\Enums\AutofillItemStatus;
use Tests\TestCase;

class AutofillItemStatusTest extends TestCase
{
    public function test_enum_has_all_expected_cases(): void
    {
        $this->assertCount(12, AutofillItemStatus::cases());
    }

    public function test_terminal_statuses(): void
    {
        $this->assertTrue(AutofillItemStatus::Saved->isTerminal());
        $this->assertTrue(AutofillItemStatus::Failed->isTerminal());
        $this->assertTrue(AutofillItemStatus::Skipped->isTerminal());
        $this->assertFalse(AutofillItemStatus::Enriching->isTerminal());
    }

    public function test_successful_status(): void
    {
        $this->assertTrue(AutofillItemStatus::Saved->isSuccessful());
        $this->assertFalse(AutofillItemStatus::Failed->isSuccessful());
    }

    public function test_labels_return_french_text(): void
    {
        $this->assertEquals('Découvert', AutofillItemStatus::Discovered->label());
        $this->assertEquals('Enregistré', AutofillItemStatus::Saved->label());
        $this->assertEquals('Ignoré', AutofillItemStatus::Skipped->label());
    }
}
