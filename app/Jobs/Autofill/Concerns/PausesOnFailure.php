<?php

namespace App\Jobs\Autofill\Concerns;

use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Support\Facades\Log;

/**
 * Safety net: when a job exhausts its attempts (timeout, unhandled
 * exception, worker crash…), Laravel calls failed(). This trait
 * ensures the workflow is paused so the UI reflects the error
 * instead of spinning indefinitely.
 */
trait PausesOnFailure
{
    /**
     * Called by Laravel when the job has failed after all retries.
     * This is the LAST chance to update the workflow state.
     */
    public function failed(\Throwable $exception): void
    {
        try {
            $workflow = AutofillWorkflow::find($this->workflowId);

            if (! $workflow || ! $workflow->isActive()) {
                return;
            }

            $userMessage = 'Une erreur inattendue est survenue. Vous pouvez réessayer.';

            app(AutofillPipelineService::class)->pause(
                $workflow,
                $userMessage,
                $exception->getMessage()
            );
        } catch (\Throwable $e) {
            // Absolute last resort — at least log it
            Log::critical('PausesOnFailure: could not pause workflow after job failure', [
                'workflow_id' => $this->workflowId,
                'original_error' => $exception->getMessage(),
                'pause_error' => $e->getMessage(),
            ]);
        }
    }
}
