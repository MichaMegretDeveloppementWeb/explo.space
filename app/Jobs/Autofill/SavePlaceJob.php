<?php

namespace App\Jobs\Autofill;

use App\Enums\AutofillItemStatus;
use App\Models\AutofillItem;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use App\Services\Admin\Place\Create\PlaceCreateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SavePlaceJob implements ShouldQueue
{
    use Concerns\PausesOnFailure;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120;

    public int $tries = 1;

    public function __construct(
        public readonly int $workflowId,
        public readonly int $itemId,
    ) {}

    public function handle(PlaceCreateService $placeCreateService, AutofillPipelineService $pipeline): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);
        $item = AutofillItem::find($this->itemId);

        if (! $workflow || ! $item || ! $workflow->isActive()) {
            return;
        }

        $item->update(['status' => AutofillItemStatus::Saving]);

        try {
            $enrichmentData = $item->enrichment_data ?? [];
            $frTranslations = $enrichmentData['translations']['fr'] ?? [];

            $titleEn = $enrichmentData['title'] ?? $item->name;
            $titleFr = $frTranslations['title'] ?? $titleEn;

            // Prepare photos from temp storage
            $photos = $this->preparePhotos($item);
            $photoTranslations = $this->preparePhotoTranslations($item);

            $placeData = [
                'latitude' => $enrichmentData['latitude'] ?? 0,
                'longitude' => $enrichmentData['longitude'] ?? 0,
                'address' => $enrichmentData['address'] ?? '',
                'admin_id' => $workflow->admin_id,
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => $titleEn,
                        'slug' => Str::slug($titleEn),
                        'description' => $enrichmentData['description'] ?? '',
                        'practical_info' => $enrichmentData['practical_info'] ?? null,
                        'status' => 'published',
                    ],
                    'fr' => [
                        'title' => $titleFr,
                        'slug' => Str::slug($titleFr),
                        'description' => $frTranslations['description'] ?? $enrichmentData['description'] ?? '',
                        'practical_info' => $frTranslations['practical_info'] ?? null,
                        'status' => 'published',
                    ],
                ],
                'category_ids' => [],
                'tag_ids' => [],
                'photos' => $photos,
                'photo_translations' => $photoTranslations,
            ];

            $place = $placeCreateService->create($placeData);

            $item->update([
                'status' => AutofillItemStatus::Saved,
                'place_id' => $place->id,
            ]);

            $this->checkCompletion($workflow);
        } catch (\Throwable $e) {
            Log::error('Save place job failed', [
                'workflow_id' => $workflow->id,
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            $item->update([
                'status' => AutofillItemStatus::Failed,
                'error_message' => 'Erreur lors de l\'enregistrement du lieu.',
                'error_technical' => mb_substr($e->getMessage(), 0, 1000),
                'step_failed_at' => 'saving',
            ]);

            $pipeline->pause(
                $workflow,
                'Une erreur est survenue lors de l\'enregistrement de « '.$item->name.' ». Vous pouvez réessayer.',
                $e->getMessage()
            );
        }
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function preparePhotos(AutofillItem $item): array
    {
        /** @var array<int, array<string, mixed>> $imagesData */
        $imagesData = $item->images_data ?? [];
        $photos = [];
        $disk = config('autofill.images.temp_disk', 'autofill_temp');

        foreach ($imagesData as $image) {
            $path = $image['path'] ?? null;

            if (! $path || ! Storage::disk($disk)->exists($path)) {
                continue;
            }

            $fullPath = Storage::disk($disk)->path($path);
            $filename = basename($path);
            $mimeType = Storage::disk($disk)->mimeType($path) ?: 'image/jpeg';

            $photos[] = new UploadedFile($fullPath, $filename, $mimeType, null, true);
        }

        return $photos;
    }

    /**
     * @return array<string, array<string, array{alt_text: string|null}>>
     */
    private function preparePhotoTranslations(AutofillItem $item): array
    {
        /** @var array<int, array<string, mixed>> $imagesData */
        $imagesData = $item->images_data ?? [];
        $translations = [];

        foreach ($imagesData as $index => $image) {
            $key = "temp_{$index}";
            $altEn = $image['alt_text_en'] ?? null;
            $altFr = $image['alt_text_fr'] ?? $altEn;

            $translations[$key] = [
                'en' => ['alt_text' => $altEn ? mb_substr($altEn, 0, 255) : null],
                'fr' => ['alt_text' => $altFr ? mb_substr($altFr, 0, 255) : null],
            ];
        }

        return $translations;
    }

    private function checkCompletion(AutofillWorkflow $workflow): void
    {
        $pendingItems = AutofillItem::query()
            ->where('workflow_id', $workflow->id)
            ->whereNotIn('status', [
                AutofillItemStatus::Saved->value,
                AutofillItemStatus::Failed->value,
                AutofillItemStatus::Skipped->value,
            ])
            ->exists();

        if (! $pendingItems) {
            WorkflowCompletionJob::dispatch($this->workflowId)
                ->onQueue(config('autofill.queue', 'autofill'));
        }
    }
}
