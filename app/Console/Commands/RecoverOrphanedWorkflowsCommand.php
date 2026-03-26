<?php

namespace App\Console\Commands;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Console\Command;

class RecoverOrphanedWorkflowsCommand extends Command
{
    protected $signature = 'autofill:recover
        {--minutes=30 : Consider workflows stuck after this many minutes}
        {--dry-run : Show what would be done without making changes}';

    protected $description = 'Detect and pause orphaned autofill workflows stuck in processing states';

    public function handle(AutofillPipelineService $pipeline): int
    {
        $minutes = (int) $this->option('minutes');
        $dryRun = (bool) $this->option('dry-run');

        $stuckWorkflows = AutofillWorkflow::query()
            ->where('state', AutofillWorkflowState::Active)
            ->whereNotIn('status', [
                AutofillWorkflowStatus::AwaitingSelection,
                AutofillWorkflowStatus::AwaitingImages,
            ])
            ->where('updated_at', '<', now()->subMinutes($minutes))
            ->get();

        if ($stuckWorkflows->isEmpty()) {
            $this->info('No orphaned workflows found.');

            return self::SUCCESS;
        }

        $this->info("Found {$stuckWorkflows->count()} orphaned workflow(s):");

        foreach ($stuckWorkflows as $workflow) {
            $stuckItems = $workflow->items()
                ->whereIn('status', [
                    AutofillItemStatus::Enriching,
                    AutofillItemStatus::ImagesSearching,
                    AutofillItemStatus::Translating,
                    AutofillItemStatus::Saving,
                ])
                ->count();

            $this->line("  #{$workflow->id} — status: {$workflow->status->value}, stuck items: {$stuckItems}, last update: {$workflow->updated_at}");

            if (! $dryRun) {
                // Mark stuck items as failed so they can be resumed
                $stepMap = [
                    AutofillItemStatus::Enriching->value => 'enrichment',
                    AutofillItemStatus::ImagesSearching->value => 'images',
                    AutofillItemStatus::Translating->value => 'translation',
                    AutofillItemStatus::Saving->value => 'saving',
                ];

                $stuckItemModels = $workflow->items()
                    ->whereIn('status', [
                        AutofillItemStatus::Enriching,
                        AutofillItemStatus::ImagesSearching,
                        AutofillItemStatus::Translating,
                        AutofillItemStatus::Saving,
                    ])
                    ->get();

                foreach ($stuckItemModels as $stuckItem) {
                    /** @var \App\Models\AutofillItem $stuckItem */
                    $currentStatus = $stuckItem->status;
                    $stepFailed = $currentStatus instanceof AutofillItemStatus
                        ? ($stepMap[$currentStatus->value] ?? 'enrichment')
                        : 'enrichment';

                    $stuckItem->update([
                        'status' => AutofillItemStatus::Failed,
                        'error_message' => 'Interrompu (queue worker redémarré).',
                        'step_failed_at' => $stepFailed,
                    ]);
                }

                $pipeline->pause(
                    $workflow,
                    'Le workflow a été interrompu (redémarrage du serveur de tâches). Cliquez sur « Reprendre » pour continuer.',
                    'Workflow orphaned: no queue job running after '.$minutes.' minutes of inactivity.'
                );

                $this->info("  → Paused workflow #{$workflow->id}");
            }
        }

        if ($dryRun) {
            $this->warn('Dry run — no changes made. Remove --dry-run to apply.');
        }

        return self::SUCCESS;
    }
}
