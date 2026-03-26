<?php

namespace App\Jobs\Autofill;

use App\Ai\Agents\AltTextAgent;
use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AltTextJob implements ShouldQueue
{
    use Concerns\PausesOnFailure;
    use Concerns\TracksTokenUsage;
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

    public function handle(AutofillPipelineService $pipeline): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);
        $item = AutofillItem::find($this->itemId);

        if (! $workflow || ! $item || ! $workflow->isActive()) {
            return;
        }

        /** @var array<int, array<string, mixed>> $imagesData */
        $imagesData = $item->images_data ?? [];

        if (empty($imagesData)) {
            $this->checkAllItemsReady($workflow, $pipeline);

            return;
        }

        try {
            ['provider' => $provider, 'model' => $model] = $this->getProviderAndModel($workflow, 'light');
            $agent = AltTextAgent::make();

            foreach ($imagesData as $index => $image) {
                try {
                    $prompt = sprintf(
                        'Place: %s, Location: %s. Image source: %s. Caption: %s',
                        $item->name,
                        $item->enrichment_data['address'] ?? 'Unknown',
                        $image['source'] ?? 'Unknown',
                        $image['caption'] ?? 'No caption available'
                    );

                    $response = $agent->prompt($prompt, provider: $provider, model: $model);

                    $this->logStep($workflow, $item, 'alt_text', $prompt, $response, $provider, $model);

                    $imagesData[$index]['alt_text_en'] = trim($response->text);
                } catch (\Throwable $e) {
                    Log::warning('Alt text generation failed for image', [
                        'workflow_id' => $workflow->id,
                        'item_id' => $item->id,
                        'image_index' => $index,
                        'error' => $e->getMessage(),
                    ]);

                    // Non-critical — keep the image without alt text
                    $imagesData[$index]['alt_text_en'] = $image['caption'] ?? '';
                }
            }

            $item->update(['images_data' => $imagesData]);
        } catch (\Throwable $e) {
            Log::error('AltText job failed', [
                'workflow_id' => $workflow->id,
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            // Alt text is non-critical — save what we have and continue
            $item->update(['images_data' => $imagesData]);
        }

        $this->checkAllItemsReady($workflow, $pipeline);
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

        if (! $pendingItems && $workflow->status !== AutofillWorkflowStatus::AwaitingImages) {
            $items = AutofillItem::query()
                ->where('workflow_id', $workflow->id)
                ->where('status', AutofillItemStatus::ImagesFound)
                ->get();

            if ($items->isNotEmpty()) {
                // Remove any previous Images checkpoint (e.g. from a resumed attempt)
                $workflow->messages()
                    ->where('type', AutofillMessageType::Images)
                    ->delete();

                $imagesPayload = $items->map(fn (AutofillItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'images' => $item->images_data ?? [],
                ])->toArray();

                $pipeline->createMessage(
                    $workflow,
                    AutofillMessageType::Images,
                    AutofillMessageRole::System,
                    [
                        'text' => 'Images trouvées. Sélectionnez les images à conserver pour chaque lieu :',
                        'items' => $imagesPayload,
                    ]
                );

                $workflow->update(['status' => AutofillWorkflowStatus::AwaitingImages]);
            }
        }
    }
}
