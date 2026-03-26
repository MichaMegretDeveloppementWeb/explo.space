<?php

namespace Tests\Unit\Jobs\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowStatus;
use App\Jobs\Autofill\WorkflowCompletionJob;
use App\Models\AutofillItem;
use App\Models\AutofillMessage;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkflowCompletionJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_completes_workflow_with_stats(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Saving,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Saved,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Failed,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Skipped,
        ]);

        $job = new WorkflowCompletionJob($workflow->id);
        $job->handle();

        $workflow->refresh();
        $this->assertSame(AutofillWorkflowStatus::Completed, $workflow->status);
        $this->assertNotNull($workflow->completed_at);
    }

    public function test_cleans_up_temp_files(): void
    {
        Storage::fake('autofill_temp');
        Storage::disk('autofill_temp')->put('1/42/image_001.jpg', 'fake');

        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'id' => 1,
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Saving,
        ]);

        $job = new WorkflowCompletionJob($workflow->id);
        $job->handle();

        Storage::disk('autofill_temp')->assertMissing('1/42/image_001.jpg');
    }

    public function test_creates_recap_message_and_cleans_progress_messages(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Saving,
        ]);

        AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Saved,
        ]);

        // Create a progress message (transient, should be cleaned up)
        AutofillMessage::factory()->create([
            'workflow_id' => $workflow->id,
            'type' => AutofillMessageType::Progress,
            'role' => AutofillMessageRole::System,
        ]);

        // Create a text message (permanent, should be preserved)
        AutofillMessage::factory()->create([
            'workflow_id' => $workflow->id,
            'type' => AutofillMessageType::Text,
            'role' => AutofillMessageRole::User,
        ]);

        $this->assertSame(2, $workflow->messages()->count());

        $job = new WorkflowCompletionJob($workflow->id);
        $job->handle();

        // Progress message deleted, text message preserved, recap added
        $this->assertSame(2, $workflow->messages()->count());
        $this->assertSame(0, $workflow->messages()->where('type', AutofillMessageType::Progress)->count());

        $recap = $workflow->messages()
            ->where('type', AutofillMessageType::Recap)
            ->first();

        $this->assertNotNull($recap);
        $this->assertStringContainsString('1 lieu(x) créé(s)', $recap->payload['text']);
    }

    public function test_nullifies_images_data_but_preserves_enrichment_data(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Saving,
        ]);

        $item = AutofillItem::factory()->create([
            'workflow_id' => $workflow->id,
            'status' => AutofillItemStatus::Saved,
            'enrichment_data' => ['title' => 'Test', 'description' => 'Some desc'],
            'images_data' => [['url' => 'https://example.com/img.jpg']],
        ]);

        $job = new WorkflowCompletionJob($workflow->id);
        $job->handle();

        $item->refresh();
        $this->assertNotNull($item->enrichment_data);
        $this->assertSame('Test', $item->enrichment_data['title']);
        $this->assertNull($item->images_data);
    }

    public function test_preserves_step_logs_after_completion(): void
    {
        $admin = User::factory()->create();
        $workflow = AutofillWorkflow::factory()->create([
            'admin_id' => $admin->id,
            'status' => AutofillWorkflowStatus::Saving,
        ]);

        AutofillStepLog::factory()->create([
            'workflow_id' => $workflow->id,
            'step' => 'discovery',
        ]);

        AutofillStepLog::factory()->create([
            'workflow_id' => $workflow->id,
            'step' => 'enrichment',
        ]);

        $job = new WorkflowCompletionJob($workflow->id);
        $job->handle();

        $this->assertSame(2, $workflow->stepLogs()->count());
    }
}
