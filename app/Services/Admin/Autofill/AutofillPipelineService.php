<?php

namespace App\Services\Admin\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Jobs\Autofill\DiscoveryJob;
use App\Jobs\Autofill\EnrichmentJob;
use App\Jobs\Autofill\ImageSearchJob;
use App\Jobs\Autofill\SavePlaceJob;
use App\Jobs\Autofill\TranslationJob;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use Illuminate\Support\Facades\Log;

class AutofillPipelineService
{
    /**
     * Create and start a new autofill workflow.
     *
     * @param  array{query: string, provider: string, requested_quantity: int, admin_id: int}  $data
     */
    public function start(array $data): AutofillWorkflow
    {
        $workflow = AutofillWorkflow::create([
            'admin_id' => $data['admin_id'],
            'query' => $data['query'],
            'provider' => $data['provider'],
            'requested_quantity' => $data['requested_quantity'],
            'status' => AutofillWorkflowStatus::Pending,
            'state' => AutofillWorkflowState::Active,
            'started_at' => now(),
        ]);

        $this->createMessage($workflow, AutofillMessageType::Progress, AutofillMessageRole::System, [
            'text' => 'Workflow démarré. Recherche en cours',
        ]);

        DiscoveryJob::dispatch($workflow->id)
            ->onQueue(config('autofill.queue', 'autofill'));

        return $workflow;
    }

    /**
     * Check if a workflow is currently occupying a slot (active or paused).
     *
     * @return array{active: bool, workflow: ?AutofillWorkflow}
     */
    public function checkActiveWorkflow(): array
    {
        $currentWorkflow = AutofillWorkflow::current()->first();

        if (! $currentWorkflow) {
            return ['active' => false, 'workflow' => null];
        }

        return ['active' => true, 'workflow' => $currentWorkflow];
    }

    /**
     * Process place selection from the admin (Checkpoint 1).
     *
     * @param  array<int>  $selectedItemIds
     */
    public function processSelection(AutofillWorkflow $workflow, array $selectedItemIds): void
    {
        if ($workflow->status !== AutofillWorkflowStatus::AwaitingSelection) {
            return;
        }

        if (empty($selectedItemIds)) {
            $this->completeWithMessage($workflow, 'Aucun lieu sélectionné. Workflow terminé.');

            return;
        }

        // Mark selected items
        AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->whereIn('id', $selectedItemIds)
            ->update(['status' => AutofillItemStatus::Selected]);

        // Mark unselected items as skipped
        AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->whereNotIn('id', $selectedItemIds)
            ->where('status', AutofillItemStatus::Discovered)
            ->update(['status' => AutofillItemStatus::Skipped]);

        $workflow->update(['status' => AutofillWorkflowStatus::Enriching]);

        // Update selection message payload with selected IDs for visual recap
        $selectionMessage = $workflow->messages()
            ->where('type', AutofillMessageType::Selection)
            ->latest('id')
            ->first();

        if ($selectionMessage) {
            $payload = $selectionMessage->payload;
            $payload['selected_ids'] = $selectedItemIds;
            $selectionMessage->update(['payload' => $payload]);
        }

        $this->createMessage($workflow, AutofillMessageType::Progress, AutofillMessageRole::System, [
            'text' => count($selectedItemIds).' lieu(x) sélectionné(s). Enrichissement en cours',
        ]);

        // Dispatch enrichment for each selected item
        foreach ($selectedItemIds as $itemId) {
            EnrichmentJob::dispatch($workflow->id, $itemId)
                ->onQueue(config('autofill.queue', 'autofill'));
        }
    }

    /**
     * Process image selection from the admin (Checkpoint 2).
     *
     * @param  array<int, array<int>>  $selectedImagesPerItem  item_id => [image indices]
     */
    public function processImageSelection(AutofillWorkflow $workflow, array $selectedImagesPerItem): void
    {
        if ($workflow->status !== AutofillWorkflowStatus::AwaitingImages) {
            return;
        }

        // Filter images_data per item to keep only selected images
        foreach ($selectedImagesPerItem as $itemId => $selectedIndices) {
            $item = AutofillItem::find($itemId);

            if (! $item) {
                continue;
            }

            $selectedIndices = is_array($selectedIndices) ? $selectedIndices : [];

            $imagesData = $item->images_data ?? [];
            $filteredImages = array_values(array_filter(
                $imagesData,
                fn ($image, $index) => in_array($index, $selectedIndices, true),
                ARRAY_FILTER_USE_BOTH
            ));

            $item->update([
                'images_data' => $filteredImages,
                'images_count' => count($filteredImages),
            ]);
        }

        $workflow->update(['status' => AutofillWorkflowStatus::Translating]);

        // Update images message payload with selected counts for visual recap
        $imagesMessage = $workflow->messages()
            ->where('type', AutofillMessageType::Images)
            ->latest('id')
            ->first();

        if ($imagesMessage) {
            $payload = $imagesMessage->payload;
            $updatedItems = collect($payload['items'] ?? [])->map(function (array $itemData) use ($selectedImagesPerItem) {
                $itemId = $itemData['id'];
                $selectedIndices = $selectedImagesPerItem[$itemId] ?? [];
                $selectedIndices = is_array($selectedIndices) ? $selectedIndices : [];

                $itemData['images'] = array_values(array_filter(
                    $itemData['images'] ?? [],
                    fn ($image, $index) => in_array($index, $selectedIndices, true),
                    ARRAY_FILTER_USE_BOTH
                ));

                return $itemData;
            })->toArray();

            $payload['items'] = $updatedItems;
            $imagesMessage->update(['payload' => $payload]);
        }

        $this->createMessage($workflow, AutofillMessageType::Progress, AutofillMessageRole::System, [
            'text' => 'Images validées. Traduction en cours',
        ]);

        // Dispatch translation for each item with images
        $items = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->where('status', AutofillItemStatus::ImagesFound)
            ->get();

        foreach ($items as $item) {
            TranslationJob::dispatch($workflow->id, $item->id)
                ->onQueue(config('autofill.queue', 'autofill'));
        }
    }

    /**
     * Pause a workflow due to an error (called by jobs).
     * The workflow stays at its current pipeline step.
     */
    public function pause(AutofillWorkflow $workflow, string $userMessage, string $technicalError): void
    {
        // Remove transient progress messages (they have a hardcoded spinner
        // that would be misleading alongside the error message)
        $workflow->messages()
            ->where('type', AutofillMessageType::Progress)
            ->delete();

        $workflow->update([
            'state' => AutofillWorkflowState::Paused,
            'error_message' => $userMessage,
            'error_technical' => mb_substr($technicalError, 0, 1000),
        ]);

        $this->createMessage($workflow, AutofillMessageType::Error, AutofillMessageRole::System, [
            'text' => $userMessage,
        ]);

        Log::warning('Autofill workflow paused', [
            'workflow_id' => $workflow->id,
            'status' => $workflow->status->value,
            'error' => $technicalError,
        ]);
    }

    /**
     * Abandon a workflow (admin-initiated, definitive).
     */
    public function abandon(AutofillWorkflow $workflow): void
    {
        if ($workflow->isDismissed()) {
            return;
        }

        $workflow->update([
            'state' => AutofillWorkflowState::Abandoned,
            'completed_at' => now(),
        ]);

        $this->createMessage($workflow, AutofillMessageType::Text, AutofillMessageRole::System, [
            'text' => 'Workflow abandonné par l\'administrateur.',
        ]);
    }

    /**
     * Resume a paused workflow from where it stopped.
     */
    public function resume(AutofillWorkflow $workflow): void
    {
        if (! $workflow->isPaused()) {
            return;
        }

        $workflow->update([
            'state' => AutofillWorkflowState::Active,
            'error_message' => null,
            'error_technical' => null,
        ]);

        // Remove the error message — it will be replaced by a progress or
        // a new checkpoint once the re-dispatched job completes.
        $workflow->messages()
            ->where('type', AutofillMessageType::Error)
            ->delete();

        // Find items that failed and need reprocessing
        $failedItems = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->where('status', AutofillItemStatus::Failed)
            ->get();

        if ($failedItems->isEmpty()) {
            // Failure occurred at discovery stage (no items yet), re-dispatch
            $this->createMessage($workflow, AutofillMessageType::Progress, AutofillMessageRole::System, [
                'text' => 'Reprise du workflow. Relance de la recherche',
            ]);

            DiscoveryJob::dispatch($workflow->id)
                ->onQueue(config('autofill.queue', 'autofill'));

            return;
        }

        $this->createMessage($workflow, AutofillMessageType::Progress, AutofillMessageRole::System, [
            'text' => 'Reprise du workflow. '.$failedItems->count().' élément(s) à retraiter',
        ]);

        foreach ($failedItems as $item) {
            $this->resumeItem($workflow, $item);
        }
    }

    /**
     * Complete a workflow with a final message.
     */
    public function completeWithMessage(AutofillWorkflow $workflow, string $text): void
    {
        // Remove transient progress messages (they have a hardcoded spinner
        // that would be misleading on a completed workflow)
        $workflow->messages()
            ->where('type', AutofillMessageType::Progress)
            ->delete();

        $workflow->update([
            'status' => AutofillWorkflowStatus::Completed,
            'state' => AutofillWorkflowState::Completed,
            'completed_at' => now(),
        ]);

        $this->createMessage($workflow, AutofillMessageType::Recap, AutofillMessageRole::System, [
            'text' => $text,
        ]);
    }

    /** @param array<string, mixed> $payload */
    public function createMessage(
        AutofillWorkflow $workflow,
        AutofillMessageType $type,
        AutofillMessageRole $role,
        array $payload
    ): void {
        // Progress messages are transient — only the current step's progress
        // should be visible.  Remove previous ones whenever the system posts
        // any new message (a fresh Progress replaces the old one; any other
        // type means the step finished and the spinner should disappear).
        if ($role === AutofillMessageRole::System) {
            $workflow->messages()
                ->where('type', AutofillMessageType::Progress)
                ->delete();
        }

        $workflow->messages()->create([
            'type' => $type,
            'role' => $role,
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }

    private function resumeItem(AutofillWorkflow $workflow, AutofillItem $item): void
    {
        $failedStep = $item->step_failed_at;

        $item->update([
            'status' => AutofillItemStatus::Selected,
            'error_message' => null,
            'error_technical' => null,
            'step_failed_at' => null,
        ]);

        match ($failedStep) {
            'enrichment' => EnrichmentJob::dispatch($workflow->id, $item->id)
                ->onQueue(config('autofill.queue', 'autofill')),
            'images' => ImageSearchJob::dispatch($workflow->id, $item->id)
                ->onQueue(config('autofill.queue', 'autofill')),
            'translation' => TranslationJob::dispatch($workflow->id, $item->id)
                ->onQueue(config('autofill.queue', 'autofill')),
            'saving' => SavePlaceJob::dispatch($workflow->id, $item->id)
                ->onQueue(config('autofill.queue', 'autofill')),
            default => EnrichmentJob::dispatch($workflow->id, $item->id)
                ->onQueue(config('autofill.queue', 'autofill')),
        };
    }
}
