<?php

namespace Tests\Unit\Models;

use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillMessage;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutofillWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_can_be_created_with_factory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        $this->assertDatabaseHas('autofill_workflows', [
            'id' => $workflow->id,
            'admin_id' => $admin->id,
        ]);
    }

    public function test_workflow_casts_status_to_enum(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        $this->assertInstanceOf(AutofillWorkflowStatus::class, $workflow->status);
    }

    public function test_workflow_casts_state_to_enum(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        $this->assertInstanceOf(AutofillWorkflowState::class, $workflow->state);
    }

    public function test_workflow_has_required_relations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        $this->assertInstanceOf(BelongsTo::class, $workflow->admin());
        $this->assertInstanceOf(HasMany::class, $workflow->items());
        $this->assertInstanceOf(HasMany::class, $workflow->messages());
        $this->assertInstanceOf(HasMany::class, $workflow->stepLogs());
    }

    public function test_workflow_admin_relation_returns_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        $this->assertEquals($admin->id, $workflow->admin->id);
    }

    public function test_workflow_is_active_based_on_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $activeWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'state' => AutofillWorkflowState::Active,
        ]);

        $pausedWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'state' => AutofillWorkflowState::Paused,
        ]);

        $completedWorkflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $this->assertTrue($activeWorkflow->isActive());
        $this->assertFalse($pausedWorkflow->isActive());
        $this->assertFalse($completedWorkflow->isActive());
    }

    public function test_workflow_is_paused_based_on_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $pausedWorkflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
        ]);

        $activeWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
        ]);

        $this->assertTrue($pausedWorkflow->isPaused());
        $this->assertFalse($activeWorkflow->isPaused());
    }

    public function test_workflow_is_current_for_active_and_paused(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $activeWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'state' => AutofillWorkflowState::Active,
        ]);

        $pausedWorkflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
        ]);

        $completedWorkflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $abandonedWorkflow = AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $admin->id,
        ]);

        $this->assertTrue($activeWorkflow->isCurrent());
        $this->assertTrue($pausedWorkflow->isCurrent());
        $this->assertFalse($completedWorkflow->isCurrent());
        $this->assertFalse($abandonedWorkflow->isCurrent());
    }

    public function test_workflow_is_dismissed_for_completed_and_abandoned(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $completedWorkflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $abandonedWorkflow = AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $admin->id,
        ]);

        $activeWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
        ]);

        $this->assertTrue($completedWorkflow->isDismissed());
        $this->assertTrue($abandonedWorkflow->isDismissed());
        $this->assertFalse($activeWorkflow->isDismissed());
    }

    public function test_workflow_total_duration_returns_seconds(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
        ]);

        $duration = $workflow->totalDuration();

        $this->assertNotNull($duration);
        $this->assertGreaterThanOrEqual(299, $duration);
        $this->assertLessThanOrEqual(301, $duration);
    }

    public function test_workflow_total_duration_returns_null_when_not_started(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'started_at' => null,
        ]);

        $this->assertNull($workflow->totalDuration());
    }

    public function test_scope_active_returns_only_active_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'state' => AutofillWorkflowState::Active,
        ]);

        AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
        ]);

        AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $this->assertEquals(1, AutofillWorkflow::active()->count());
    }

    public function test_scope_current_returns_active_and_paused(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'state' => AutofillWorkflowState::Active,
        ]);

        AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
        ]);

        AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $admin->id,
        ]);

        $this->assertEquals(2, AutofillWorkflow::current()->count());
    }

    public function test_scope_for_admin_filters_by_admin_id(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        AutofillWorkflow::factory()->count(3)->create(['admin_id' => $admin1->id]);
        AutofillWorkflow::factory()->count(2)->create(['admin_id' => $admin2->id]);

        $this->assertEquals(3, AutofillWorkflow::forAdmin($admin1->id)->count());
        $this->assertEquals(2, AutofillWorkflow::forAdmin($admin2->id)->count());
    }

    public function test_workflow_defaults_tokens_and_cost_to_zero(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);

        $this->assertEquals(0, $workflow->total_tokens_in);
        $this->assertEquals(0, $workflow->total_tokens_out);
        $this->assertEquals('0.000000', $workflow->total_cost);
    }

    public function test_workflow_cascade_deletes_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        AutofillItem::factory()->count(3)->create(['workflow_id' => $workflow->id]);

        $this->assertEquals(3, AutofillItem::where('workflow_id', $workflow->id)->count());

        $workflow->delete();

        $this->assertEquals(0, AutofillItem::where('workflow_id', $workflow->id)->count());
    }

    public function test_workflow_cascade_deletes_messages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        AutofillMessage::factory()->count(2)->create(['workflow_id' => $workflow->id]);

        $workflow->delete();

        $this->assertEquals(0, AutofillMessage::where('workflow_id', $workflow->id)->count());
    }

    public function test_workflow_cascade_deletes_step_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $workflow = AutofillWorkflow::factory()->create(['admin_id' => $admin->id]);
        AutofillStepLog::factory()->count(2)->create(['workflow_id' => $workflow->id]);

        $workflow->delete();

        $this->assertEquals(0, AutofillStepLog::where('workflow_id', $workflow->id)->count());
    }
}
