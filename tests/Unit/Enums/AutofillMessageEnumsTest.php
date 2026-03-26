<?php

namespace Tests\Unit\Enums;

use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use Tests\TestCase;

class AutofillMessageEnumsTest extends TestCase
{
    public function test_message_type_has_all_cases(): void
    {
        $this->assertCount(6, AutofillMessageType::cases());
        $this->assertEquals('text', AutofillMessageType::Text->value);
        $this->assertEquals('selection', AutofillMessageType::Selection->value);
        $this->assertEquals('progress', AutofillMessageType::Progress->value);
        $this->assertEquals('images', AutofillMessageType::Images->value);
        $this->assertEquals('recap', AutofillMessageType::Recap->value);
        $this->assertEquals('error', AutofillMessageType::Error->value);
    }

    public function test_message_role_has_all_cases(): void
    {
        $this->assertCount(2, AutofillMessageRole::cases());
        $this->assertEquals('system', AutofillMessageRole::System->value);
        $this->assertEquals('user', AutofillMessageRole::User->value);
    }
}
