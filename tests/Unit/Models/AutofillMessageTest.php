<?php

namespace Tests\Unit\Models;

use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Models\AutofillMessage;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutofillMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_can_be_created_with_factory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $message = AutofillMessage::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertDatabaseHas('autofill_messages', ['id' => $message->id]);
    }

    public function test_message_casts_enums(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $message = AutofillMessage::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertInstanceOf(AutofillMessageType::class, $message->type);
        $this->assertInstanceOf(AutofillMessageRole::class, $message->role);
    }

    public function test_message_casts_payload_to_array(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $message = AutofillMessage::factory()->create([
            'workflow_id' => $workflow->id,
            'payload' => ['text' => 'Recherche en cours...'],
        ]);

        $this->assertIsArray($message->payload);
        $this->assertEquals('Recherche en cours...', $message->payload['text']);
    }

    public function test_message_has_no_updated_at(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $message = AutofillMessage::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertFalse($message->timestamps);
        $this->assertNotNull($message->created_at);
    }

    public function test_message_belongs_to_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $message = AutofillMessage::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertEquals($workflow->id, $message->workflow->id);
    }

    public function test_user_message_factory_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $message = AutofillMessage::factory()->userMessage('musées spatiaux')->create(['workflow_id' => $workflow->id]);

        $this->assertEquals(AutofillMessageRole::User, $message->role);
        $this->assertEquals('musées spatiaux', $message->payload['text']);
    }
}
