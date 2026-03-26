<?php

namespace App\Jobs\Autofill;

use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use App\Services\ImageSource\ImageSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImageSearchJob implements ShouldQueue
{
    use Concerns\PausesOnFailure;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120;

    public int $tries = 2;

    public int $backoff = 15;

    public function __construct(
        public readonly int $workflowId,
        public readonly int $itemId,
    ) {}

    public function handle(ImageSearchService $imageSearch, AutofillPipelineService $pipeline): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);
        $item = AutofillItem::find($this->itemId);

        if (! $workflow || ! $item || ! $workflow->isActive()) {
            return;
        }

        $item->update(['status' => AutofillItemStatus::ImagesSearching]);

        try {
            $placeName = $item->enrichment_data['title'] ?? $item->name;
            $originalName = $item->name; // Original name (often in French) for multilingual image search
            $location = $item->enrichment_data['address'] ?? null;

            $downloaded = $imageSearch->searchAndDownload(
                $placeName,
                $this->workflowId,
                $this->itemId,
                $originalName,
                $location
            );

            $imagesData = $downloaded->map(fn (array $entry) => [
                'path' => $entry['path'],
                'url' => $entry['result']->url,
                'source' => $entry['result']->source,
                'license' => $entry['result']->license,
                'caption' => $entry['result']->caption,
                'width' => $entry['result']->width,
                'height' => $entry['result']->height,
                'alt_text_en' => null,
                'alt_text_fr' => null,
            ])->toArray();

            $item->update([
                'status' => AutofillItemStatus::ImagesFound,
                'images_data' => $imagesData,
                'images_count' => count($imagesData),
            ]);

            // Dispatch alt text generation if images were found
            if (! empty($imagesData)) {
                AltTextJob::dispatch($this->workflowId, $this->itemId)
                    ->onQueue(config('autofill.queue', 'autofill'));
            } else {
                $this->checkAllItemsReady($workflow, $pipeline);
            }
        } catch (\Throwable $e) {
            Log::error('[Autofill:Images] Job exception caught', [
                'workflow_id' => $this->workflowId,
                'item_id' => $this->itemId,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            // Image search failure is non-critical — proceed without images
            $item->update([
                'status' => AutofillItemStatus::ImagesFound,
                'images_data' => [],
                'images_count' => 0,
            ]);

            $this->checkAllItemsReady($workflow, $pipeline);
        }
    }

    private function checkAllItemsReady(AutofillWorkflow $workflow, AutofillPipelineService $pipeline): void
    {
        $pendingItems = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->whereIn('status', [
                AutofillItemStatus::Selected->value,
                AutofillItemStatus::Enriching->value,
                AutofillItemStatus::Enriched->value,
                AutofillItemStatus::ImagesSearching->value,
            ])
            ->exists();

        if (! $pendingItems) {
            $this->transitionToImageCheckpoint($workflow, $pipeline);
        }
    }

    private function transitionToImageCheckpoint(AutofillWorkflow $workflow, AutofillPipelineService $pipeline): void
    {
        $items = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->where('status', AutofillItemStatus::ImagesFound)
            ->get();

        if ($items->isEmpty()) {
            $pipeline->completeWithMessage($workflow, 'Aucun lieu n\'a pu être traité. Workflow terminé.');

            return;
        }

        // Remove any previous Images checkpoint (e.g. from a resumed attempt)
        $workflow->messages()
            ->where('type', AutofillMessageType::Images)
            ->delete();

        // Build images payload for checkpoint
        $imagesPayload = $items->map(fn (AutofillItem $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'images' => $item->images_data ?? [],
        ])->toArray();

        $pipeline->createMessage($workflow, AutofillMessageType::Images, AutofillMessageRole::System, [
            'text' => 'Images trouvées. Sélectionnez les images à conserver pour chaque lieu :',
            'items' => $imagesPayload,
        ]);

        $workflow->update(['status' => AutofillWorkflowStatus::AwaitingImages]);
    }
}
