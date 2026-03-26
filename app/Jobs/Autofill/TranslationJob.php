<?php

namespace App\Jobs\Autofill;

use App\Contracts\Translation\TranslationStrategyInterface;
use App\Enums\AutofillItemStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslationJob implements ShouldQueue
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

    public function handle(TranslationStrategyInterface $translator, AutofillPipelineService $pipeline): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);
        $item = AutofillItem::find($this->itemId);

        if (! $workflow || ! $item || ! $workflow->isActive()) {
            return;
        }

        $item->update(['status' => AutofillItemStatus::Translating]);

        try {
            $enrichmentData = $item->enrichment_data ?? [];

            // Translate main fields from EN to FR
            $textsToTranslate = array_filter([
                'title' => $enrichmentData['title'] ?? null,
                'description' => $enrichmentData['description'] ?? null,
                'practical_info' => $enrichmentData['practical_info'] ?? null,
            ]);

            $translations = [];
            if (! empty($textsToTranslate)) {
                $translations = $translator->translateBatch($textsToTranslate, 'en', 'fr');
            }

            // Translate alt texts
            /** @var array<int, array<string, mixed>> $imagesData */
            $imagesData = $item->images_data ?? [];
            foreach ($imagesData as $index => $image) {
                $altTextEn = $image['alt_text_en'] ?? null;

                if ($altTextEn) {
                    try {
                        $imagesData[$index]['alt_text_fr'] = $translator->translate($altTextEn, 'en', 'fr');
                    } catch (\Throwable $e) {
                        Log::warning('Alt text translation failed', [
                            'item_id' => $item->id,
                            'image_index' => $index,
                            'error' => $e->getMessage(),
                        ]);
                        $imagesData[$index]['alt_text_fr'] = $altTextEn;
                    }
                }
            }

            // Store translations in enrichment_data
            $enrichmentData['translations'] = [
                'fr' => [
                    'title' => $translations['title'] ?? $enrichmentData['title'] ?? '',
                    'description' => $translations['description'] ?? $enrichmentData['description'] ?? '',
                    'practical_info' => $translations['practical_info'] ?? $enrichmentData['practical_info'] ?? null,
                ],
            ];

            $item->update([
                'status' => AutofillItemStatus::Translated,
                'enrichment_data' => $enrichmentData,
                'images_data' => $imagesData,
            ]);

            // Dispatch save
            SavePlaceJob::dispatch($this->workflowId, $this->itemId)
                ->onQueue(config('autofill.queue', 'autofill'));
        } catch (\Throwable $e) {
            Log::error('Translation job failed', [
                'workflow_id' => $workflow->id,
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            $item->update([
                'status' => AutofillItemStatus::Failed,
                'error_message' => 'Erreur lors de la traduction.',
                'error_technical' => mb_substr($e->getMessage(), 0, 1000),
                'step_failed_at' => 'translation',
            ]);

            $pipeline->pause(
                $workflow,
                'Une erreur est survenue lors de la traduction de « '.$item->name.' ». Vous pouvez réessayer.',
                $e->getMessage()
            );
        }
    }
}
