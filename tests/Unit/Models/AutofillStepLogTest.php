<?php

namespace Tests\Unit\Models;

use App\Models\AutofillItem;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutofillStepLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_log_can_be_created_with_factory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $log = AutofillStepLog::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertDatabaseHas('autofill_step_logs', ['id' => $log->id]);
    }

    public function test_step_log_casts_input_data_to_array(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $log = AutofillStepLog::factory()->create([
            'workflow_id' => $workflow->id,
            'input_data' => ['query' => 'test'],
        ]);

        $this->assertIsArray($log->input_data);
        $this->assertEquals('test', $log->input_data['query']);
    }

    public function test_step_log_has_no_updated_at(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $log = AutofillStepLog::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertFalse($log->timestamps);
        $this->assertNotNull($log->created_at);
    }

    public function test_step_log_belongs_to_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $log = AutofillStepLog::factory()->create(['workflow_id' => $workflow->id]);

        $this->assertEquals($workflow->id, $log->workflow->id);
    }

    public function test_step_log_can_belong_to_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $item = AutofillItem::factory()->create(['workflow_id' => $workflow->id]);
        $log = AutofillStepLog::factory()->create([
            'workflow_id' => $workflow->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals($item->id, $log->item->id);
    }

    public function test_step_log_item_is_nullable(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        $log = AutofillStepLog::factory()->create([
            'workflow_id' => $workflow->id,
            'item_id' => null,
        ]);

        $this->assertNull($log->item);
    }
}
