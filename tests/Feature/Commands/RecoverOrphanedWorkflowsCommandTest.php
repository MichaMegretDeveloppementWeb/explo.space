<?php

namespace Tests\Feature\Commands;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecoverOrphanedWorkflowsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_orphans_reports_nothing(): void
    {
        $this->artisan('autofill:recover')
            ->assertExitCode(0)
            ->expectsOutputToContain('No orphaned workflows found.');
    }

    public function test_detects_stuck_workflow(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
            'updated_at' => now()->subMinutes(45),
        ]);

        $item = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Enriching,
        ]);

        $this->artisan('autofill:recover --minutes=30')
            ->assertExitCode(0)
            ->expectsOutputToContain('Paused workflow #'.$workflow->id);

        $workflow->refresh();
        $item->refresh();

        $this->assertSame(AutofillWorkflowState::Paused, $workflow->state);
        $this->assertSame(AutofillItemStatus::Failed, $item->status);
        $this->assertSame('enrichment', $item->step_failed_at);
    }

    public function test_ignores_awaiting_workflows(): void
    {
        $admin = User::factory()->create();
        AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::AwaitingSelection,
            'state' => AutofillWorkflowState::Active,
            'updated_at' => now()->subHours(2),
        ]);

        $this->artisan('autofill:recover --minutes=30')
            ->assertExitCode(0)
            ->expectsOutputToContain('No orphaned workflows found.');
    }

    public function test_ignores_recently_updated_workflows(): void
    {
        $admin = User::factory()->create();
        AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
            'updated_at' => now()->subMinutes(5),
        ]);

        $this->artisan('autofill:recover --minutes=30')
            ->assertExitCode(0)
            ->expectsOutputToContain('No orphaned workflows found.');
    }

    public function test_dry_run_does_not_modify(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
            'updated_at' => now()->subMinutes(45),
        ]);

        $this->artisan('autofill:recover --minutes=30 --dry-run')
            ->assertExitCode(0)
            ->expectsOutputToContain('Dry run');

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Active, $workflow->state);
    }

    public function test_maps_item_status_to_correct_step(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Saving,
            'state' => AutofillWorkflowState::Active,
            'updated_at' => now()->subMinutes(45),
        ]);

        $savingItem = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Saving,
        ]);

        $searchingItem = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::ImagesSearching,
        ]);

        $this->artisan('autofill:recover --minutes=30')
            ->assertExitCode(0);

        $savingItem->refresh();
        $searchingItem->refresh();

        $this->assertSame('saving', $savingItem->step_failed_at);
        $this->assertSame('images', $searchingItem->step_failed_at);
    }
}
