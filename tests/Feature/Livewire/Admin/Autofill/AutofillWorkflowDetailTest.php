<?php

namespace Tests\Feature\Livewire\Admin\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Livewire\Admin\Autofill\AutofillWorkflowDetail;
use App\Models\AutofillItem;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class AutofillWorkflowDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private AutofillWorkflow $workflow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);

        $this->workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $this->admin->id,
            'query' => 'Space launch sites',
            'provider' => 'openai',
            'total_tokens_in' => 1500,
            'total_tokens_out' => 500,
            'total_cost' => 0.0042,
        ]);
    }

    public function test_component_renders_with_workflow(): void
    {
        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->assertStatus(200)
            ->assertSee('Space launch sites')
            ->assertSee('Terminé')
            ->assertSee('Openai');
    }

    public function test_displays_items(): void
    {
        AutofillItem::factory()->create([
            'workflow_id' => $this->workflow->id,
            'name' => 'Baikonur Cosmodrome',
            'status' => AutofillItemStatus::Saved,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $this->workflow->id,
            'name' => 'Cape Canaveral',
            'status' => AutofillItemStatus::Failed,
        ]);

        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->assertSee('Baikonur Cosmodrome')
            ->assertSee('Cape Canaveral')
            ->assertSee('Lieux (2)');
    }

    public function test_toggle_item_expands_and_collapses(): void
    {
        $item = AutofillItem::factory()->create([
            'workflow_id' => $this->workflow->id,
            'name' => 'Test Place',
            'status' => AutofillItemStatus::Saved,
        ]);

        $component = Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->assertSet('expandedItems', [])
            ->call('toggleItem', $item->id)
            ->assertSet('expandedItems', [$item->id]);

        $component->call('toggleItem', $item->id)
            ->assertSet('expandedItems', []);
    }

    public function test_io_modal_opens_and_closes(): void
    {
        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->assertSet('showIoModal', false)
            ->call('openIoModal')
            ->assertSet('showIoModal', true)
            ->call('closeIoModal')
            ->assertSet('showIoModal', false);
    }

    public function test_io_modal_shows_step_logs(): void
    {
        AutofillStepLog::factory()->create([
            'workflow_id' => $this->workflow->id,
            'step' => 'discovery',
            'model' => 'gpt-4o-mini',
            'tokens_in' => 100,
            'tokens_out' => 50,
        ]);

        $component = Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->call('openIoModal');

        $logs = $component->get('stepLogs');
        $this->assertCount(1, $logs);
        $this->assertSame('discovery', $logs->first()->step);
    }

    public function test_io_modal_filters_by_step(): void
    {
        AutofillStepLog::factory()->create([
            'workflow_id' => $this->workflow->id,
            'step' => 'discovery',
        ]);

        AutofillStepLog::factory()->create([
            'workflow_id' => $this->workflow->id,
            'step' => 'enrichment',
        ]);

        $component = Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->call('openIoModal')
            ->set('ioFilterStep', 'discovery');

        $logs = $component->get('stepLogs');
        $this->assertCount(1, $logs);
    }

    public function test_polling_active_for_active_workflow(): void
    {
        $activeWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $activeWorkflow->id])
            ->assertSet('isPolling', true);
    }

    public function test_polling_inactive_for_completed_workflow(): void
    {
        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->assertSet('isPolling', false);
    }

    public function test_polling_inactive_for_paused_workflow(): void
    {
        $pausedWorkflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
        ]);

        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $pausedWorkflow->id])
            ->assertSet('isPolling', false);
    }

    public function test_abandon_workflow(): void
    {
        $activeWorkflow = AutofillWorkflow::factory()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $activeWorkflow->id])
            ->call('abandonWorkflow')
            ->assertSet('isPolling', false);

        $activeWorkflow->refresh();
        $this->assertSame(AutofillWorkflowState::Abandoned, $activeWorkflow->state);
    }

    public function test_resume_paused_workflow(): void
    {
        Queue::fake();

        $pausedWorkflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $pausedWorkflow->id,
            'status' => AutofillItemStatus::Failed,
            'step_failed_at' => 'enrichment',
        ]);

        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $pausedWorkflow->id])
            ->call('resumeWorkflow')
            ->assertSet('isPolling', true);

        $pausedWorkflow->refresh();
        $this->assertSame(AutofillWorkflowState::Active, $pausedWorkflow->state);

        Queue::assertPushed(\App\Jobs\Autofill\EnrichmentJob::class);
    }

    public function test_progress_bar_reflects_workflow_state(): void
    {
        $component = Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id]);

        $progress = $component->get('progress');
        $this->assertCount(5, $progress['steps']);
        $this->assertTrue($progress['isDismissed']);
        $this->assertFalse($progress['isPaused']);
    }

    public function test_progress_bar_shows_paused_state(): void
    {
        $pausedWorkflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $this->admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
        ]);

        $component = Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $pausedWorkflow->id]);

        $progress = $component->get('progress');
        $this->assertTrue($progress['isPaused']);
        $this->assertFalse($progress['isDismissed']);
    }

    public function test_displays_stats(): void
    {
        Livewire::test(AutofillWorkflowDetail::class, ['workflowId' => $this->workflow->id])
            ->assertSee('1,500')
            ->assertSee('500')
            ->assertSee('$0.0042');
    }
}
