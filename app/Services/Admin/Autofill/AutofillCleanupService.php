<?php

namespace App\Services\Admin\Autofill;

use App\Models\AutofillWorkflow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AutofillCleanupService
{
    /**
     * Purge intermediate data after a workflow completes successfully.
     *
     * Nullifies images_data on items and cleans up temp files.
     * Enrichment data is preserved for the workflow detail page (justification, description, etc.).
     * Messages are preserved so the chat can display the recap.
     * Step logs are preserved for the I/O modal.
     */
    public function purgeCompletedWorkflow(AutofillWorkflow $workflow): void
    {
        // Nullify only images_data (temp file references, no longer needed).
        // Keep enrichment_data for the detail page recap.
        $workflow->items()->update([
            'images_data' => null,
        ]);

        $this->deleteTempFiles($workflow);
    }

    /**
     * Delete a workflow and all associated data.
     *
     * Cascade on the database handles items, messages, and step logs.
     * This method also cleans up temp files if they still exist.
     * Places created by the workflow are NOT deleted.
     */
    public function deleteWorkflow(AutofillWorkflow $workflow): bool
    {
        if (! $workflow->isDismissed()) {
            return false;
        }

        $this->deleteTempFiles($workflow);

        $workflow->delete();

        return true;
    }

    /**
     * Clean up orphaned temp directories.
     *
     * Finds directories in autofill_temp/ that either:
     * 1. Have no associated workflow
     * 2. Have an associated workflow that has been terminal for over 24 hours
     *
     * @return int Number of directories cleaned up
     */
    public function cleanupOrphanedTempFiles(): int
    {
        $disk = config('autofill.images.temp_disk', 'autofill_temp');
        $cleaned = 0;

        try {
            $directories = Storage::disk($disk)->directories();

            foreach ($directories as $directory) {
                $workflowId = (int) basename($directory);

                if ($workflowId <= 0) {
                    continue;
                }

                $workflow = AutofillWorkflow::find($workflowId);

                // No associated workflow — orphan
                if (! $workflow) {
                    Storage::disk($disk)->deleteDirectory($directory);
                    $cleaned++;

                    continue;
                }

                // Dismissed workflow completed more than 24 hours ago
                if (
                    $workflow->isDismissed()
                    && $workflow->completed_at
                    && $workflow->completed_at->lt(now()->subDay())
                ) {
                    Storage::disk($disk)->deleteDirectory($directory);
                    $cleaned++;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to cleanup orphaned autofill temp files', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleaned;
    }

    /**
     * Delete temp files for a specific workflow.
     */
    private function deleteTempFiles(AutofillWorkflow $workflow): void
    {
        $disk = config('autofill.images.temp_disk', 'autofill_temp');

        try {
            $directory = (string) $workflow->id;

            if (Storage::disk($disk)->directoryExists($directory)) {
                Storage::disk($disk)->deleteDirectory($directory);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to cleanup temp files', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
