<?php

namespace Tests\Unit\Services\Admin\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Models\User;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AutofillPipelineServiceTest extends TestCase
{
    use RefreshDatabase;

    private AutofillPipelineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AutofillPipelineService;
    }

    public function test_start_creates_workflow_and_dispatches_discovery(): void
    {
        Queue::fake();

        $admin = User::factory()->create();

        $workflow = $this->service->start([
            'query' => 'Space launch sites in Europe',
            'provider' => 'openai',
            'requested_quantity' => 5,
            'admin_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('autofill_workflows', [
            'id' => $workflow->id,
            'query' => 'Space launch sites in Europe',
            'provider' => 'openai',
            'requested_quantity' => 5,
            'status' => AutofillWorkflowStatus::Pending->value,
            'state' => AutofillWorkflowState::Active->value,
        ]);

        $this->assertDatabaseHas('autofill_messages', [
            'workflow_id' => $workflow->id,
            'type' => AutofillMessageType::Progress->value,
        ]);

        Queue::assertPushed(\App\Jobs\Autofill\DiscoveryJob::class);
    }

    public function test_check_active_workflow_returns_false_when_none(): void
    {
        $result = $this->service->checkActiveWorkflow();

        $this->assertFalse($result['active']);
        $this->assertNull($result['workflow']);
    }

    public function test_check_active_workflow_returns_true_when_active(): void
    {
        $admin = User::factory()->create();

        AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        $result = $this->service->checkActiveWorkflow();

        $this->assertTrue($result['active']);
        $this->assertNotNull($result['workflow']);
    }

    public function test_check_active_workflow_returns_true_when_paused(): void
    {
        $admin = User::factory()->create();

        AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
        ]);

        $result = $this->service->checkActiveWorkflow();

        $this->assertTrue($result['active']);
        $this->assertNotNull($result['workflow']);
    }

    public function test_check_active_workflow_returns_false_when_only_dismissed(): void
    {
        $admin = User::factory()->create();

        AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        AutofillWorkflow::factory()->abandoned()->create([
            'admin_id' => $admin->id,
        ]);

        $result = $this->service->checkActiveWorkflow();

        $this->assertFalse($result['active']);
    }

    public function test_process_selection_marks_items_and_dispatches_enrichment(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->awaitingSelection()->create([
            'admin_id' => $admin->id,
        ]);

        $item1 = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Discovered,
            'name' => 'Place A',
        ]);

        $item2 = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Discovered,
            'name' => 'Place B',
        ]);

        $item3 = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Discovered,
            'name' => 'Place C',
        ]);

        $this->service->processSelection($workflow, [$item1->id, $item3->id]);

        $this->assertDatabaseHas('autofill_items', [
            'id' => $item1->id,
            'status' => AutofillItemStatus::Selected->value,
        ]);

        $this->assertDatabaseHas('autofill_items', [
            'id' => $item2->id,
            'status' => AutofillItemStatus::Skipped->value,
        ]);

        $this->assertDatabaseHas('autofill_items', [
            'id' => $item3->id,
            'status' => AutofillItemStatus::Selected->value,
        ]);

        Queue::assertPushed(\App\Jobs\Autofill\EnrichmentJob::class, 2);
    }

    public function test_process_empty_selection_completes_workflow(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->awaitingSelection()->create([
            'admin_id' => $admin->id,
        ]);

        $this->service->processSelection($workflow, []);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::Completed, $workflow->status);
        $this->assertSame(AutofillWorkflowState::Completed, $workflow->state);
    }

    public function test_pause_sets_state_to_paused_and_keeps_status(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        $this->service->pause($workflow, 'API error occurred', 'HTTP 429 Too Many Requests');

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Paused, $workflow->state);
        $this->assertSame(AutofillWorkflowStatus::Enriching, $workflow->status);
        $this->assertEquals('API error occurred', $workflow->error_message);
        $this->assertEquals('HTTP 429 Too Many Requests', $workflow->error_technical);

        $this->assertDatabaseHas('autofill_messages', [
            'workflow_id' => $workflow->id,
            'type' => AutofillMessageType::Error->value,
        ]);
    }

    public function test_pause_cleans_progress_messages(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        // Simulate progress message created during start()
        $this->service->createMessage($workflow, AutofillMessageType::Progress, \App\Enums\AutofillMessageRole::System, [
            'text' => 'Workflow démarré. Recherche en cours...',
        ]);

        $this->assertSame(1, $workflow->messages()->count());

        $this->service->pause($workflow, 'Erreur API', 'HTTP 500');

        // Progress message removed, only error message remains
        $this->assertSame(1, $workflow->messages()->count());
        $this->assertSame(0, $workflow->messages()->where('type', AutofillMessageType::Progress)->count());
        $this->assertSame(1, $workflow->messages()->where('type', AutofillMessageType::Error)->count());
    }

    public function test_abandon_sets_state_to_abandoned(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
            'state' => AutofillWorkflowState::Active,
        ]);

        $this->service->abandon($workflow);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Abandoned, $workflow->state);
        $this->assertNotNull($workflow->completed_at);
    }

    public function test_abandon_does_nothing_for_completed_workflow(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->completed()->create([
            'admin_id' => $admin->id,
        ]);

        $this->service->abandon($workflow);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Completed, $workflow->state);
    }

    public function test_abandon_works_for_paused_workflow(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
        ]);

        $this->service->abandon($workflow);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Abandoned, $workflow->state);
    }

    public function test_resume_dispatches_jobs_for_failed_items(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Enriching,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Failed,
            'step_failed_at' => 'enrichment',
        ]);

        $this->service->resume($workflow);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Active, $workflow->state);
        $this->assertNull($workflow->error_message);
        $this->assertNull($workflow->error_technical);

        Queue::assertPushed(\App\Jobs\Autofill\EnrichmentJob::class, 1);
    }

    public function test_resume_redispatches_discovery_when_no_failed_items(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->paused()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
        ]);

        $this->service->resume($workflow);

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowState::Active, $workflow->state);

        Queue::assertPushed(\App\Jobs\Autofill\DiscoveryJob::class, 1);
    }

    public function test_resume_does_nothing_for_non_paused_workflow(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'state' => AutofillWorkflowState::Active,
        ]);

        $this->service->resume($workflow);

        Queue::assertNothingPushed();
    }

    public function test_complete_with_message_sets_completed_and_creates_recap(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        $this->service->completeWithMessage($workflow, 'Test message');

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::Completed, $workflow->status);
        $this->assertSame(AutofillWorkflowState::Completed, $workflow->state);
        $this->assertNotNull($workflow->completed_at);

        $this->assertDatabaseHas('autofill_messages', [
            'workflow_id' => $workflow->id,
            'type' => AutofillMessageType::Recap->value,
        ]);
    }

    public function test_complete_with_message_cleans_progress_messages(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Discovering,
            'state' => AutofillWorkflowState::Active,
        ]);

        // Simulate progress message created during start()
        $this->service->createMessage($workflow, AutofillMessageType::Progress, \App\Enums\AutofillMessageRole::System, [
            'text' => 'Workflow démarré. Recherche en cours...',
        ]);

        $this->assertSame(1, $workflow->messages()->count());

        $this->service->completeWithMessage($workflow, 'Aucun lieu trouvé.');

        // Progress message removed, only recap remains
        $this->assertSame(1, $workflow->messages()->count());
        $this->assertSame(0, $workflow->messages()->where('type', AutofillMessageType::Progress)->count());
        $this->assertSame(1, $workflow->messages()->where('type', AutofillMessageType::Recap)->count());
    }
}
