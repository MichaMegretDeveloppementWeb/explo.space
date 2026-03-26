<?php

namespace App\Jobs\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillCleanupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WorkflowCompletionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 60;

    public function __construct(
        public readonly int $workflowId,
    ) {}

    public function handle(): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);

        if (! $workflow) {
            Log::warning('WorkflowCompletionJob: workflow not found, skipping.', ['workflow_id' => $this->workflowId]);

            return;
        }

        $savedCount = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->where('status', AutofillItemStatus::Saved)
            ->count();

        $failedCount = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->where('status', AutofillItemStatus::Failed)
            ->count();

        $skippedCount = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->where('status', AutofillItemStatus::Skipped)
            ->count();

        // Remove transient progress messages (they have a hardcoded spinner
        // that would be misleading on a completed workflow)
        $workflow->messages()
            ->where('type', AutofillMessageType::Progress)
            ->delete();

        // Create recap message BEFORE marking workflow as completed,
        // so polling can pick it up and display it to the user.
        $recapParts = [];

        if ($savedCount > 0) {
            $recapParts[] = "{$savedCount} lieu(x) créé(s)";
        }

        if ($failedCount > 0) {
            $recapParts[] = "{$failedCount} échoué(s)";
        }

        if ($skippedCount > 0) {
            $recapParts[] = "{$skippedCount} ignoré(s)";
        }

        $recapText = 'Workflow terminé. '.implode(', ', $recapParts).'.';

        $workflow->messages()->create([
            'type' => AutofillMessageType::Recap,
            'role' => AutofillMessageRole::System,
            'payload' => ['text' => $recapText],
            'created_at' => now(),
        ]);

        $workflow->update([
            'status' => AutofillWorkflowStatus::Completed,
            'state' => AutofillWorkflowState::Completed,
            'completed_at' => now(),
        ]);

        // Purge intermediate data (enrichment_data, images_data, temp files).
        // Messages and step logs are preserved so the chat and I/O modal remain useful.
        app(AutofillCleanupService::class)->purgeCompletedWorkflow($workflow);
    }
}
