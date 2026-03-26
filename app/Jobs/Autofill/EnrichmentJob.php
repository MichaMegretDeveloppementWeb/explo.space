<?php

namespace App\Jobs\Autofill;

use App\Ai\Agents\EnrichmentAgent;
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

class EnrichmentJob implements ShouldQueue
{
    use Concerns\ParsesAgentJson;
    use Concerns\PausesOnFailure;
    use Concerns\TracksTokenUsage;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // Must exceed EnrichmentAgent's #[Timeout(120)] to avoid the queue killing the job mid-execution.
    public int $timeout = 240;

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

        $item->update(['status' => AutofillItemStatus::Enriching]);

        try {
            ['provider' => $provider, 'model' => $model] = $this->getProviderAndModel($workflow, 'mid');

            $location = $item->enrichment_data['approximate_location'] ?? '';
            $lat = $item->enrichment_data['latitude'] ?? null;
            $lng = $item->enrichment_data['longitude'] ?? null;
            $gpsHint = ($lat && $lng) ? " (GPS: {$lat}, {$lng})" : '';
            $prompt = "Enrich: {$item->name}, {$location}{$gpsHint}";

            $webSearchProviders = ['openai', 'anthropic', 'gemini'];
            $useWebSearch = in_array($provider, $webSearchProviders, true);

            $agent = EnrichmentAgent::make(useWebSearch: $useWebSearch);

            $response = $agent->prompt($prompt, provider: $provider, model: $model);

            $this->logStep($workflow, $item, 'enrichment', $prompt, $response, $provider, $model);

            $enrichmentData = method_exists($response, 'toArray')
                ? $response->toArray()
                : $this->parseJsonResponse($response->text);

            // Sanitize text fields to prevent XSS
            $enrichmentData['title'] = strip_tags($enrichmentData['title'] ?? '');
            $enrichmentData['description'] = strip_tags($enrichmentData['description'] ?? '');
            $enrichmentData['practical_info'] = strip_tags($enrichmentData['practical_info'] ?? '');
            $enrichmentData['address'] = strip_tags($enrichmentData['address'] ?? '');

            // Post-process text fields to remove URLs, markdown artifacts, and ensure paragraphs
            if (isset($enrichmentData['description'])) {
                $enrichmentData['description'] = $this->cleanTextField($enrichmentData['description']);
            }

            if (isset($enrichmentData['practical_info'])) {
                $enrichmentData['practical_info'] = $this->cleanTextField($enrichmentData['practical_info']);
            }

            // Strip tracking parameters from source URLs
            if (isset($enrichmentData['source_urls']) && is_array($enrichmentData['source_urls'])) {
                $enrichmentData['source_urls'] = array_map(
                    fn (string $url) => $this->cleanSourceUrl($url),
                    $enrichmentData['source_urls']
                );
            }

            // Convert suggested_tags from comma-separated string to array
            $suggestedTagsStr = $enrichmentData['suggested_tags'] ?? '';
            $suggestedTags = $suggestedTagsStr
                ? array_map('trim', explode(',', $suggestedTagsStr))
                : null;

            $item->update([
                'status' => AutofillItemStatus::Enriched,
                'enrichment_data' => array_merge($item->enrichment_data ?? [], $enrichmentData),
                'suggested_tags' => $suggestedTags,
            ]);

            // Dispatch image search for this item
            ImageSearchJob::dispatch($this->workflowId, $this->itemId)
                ->onQueue(config('autofill.queue', 'autofill'));
        } catch (\Throwable $e) {
            Log::error('Enrichment job failed', [
                'workflow_id' => $workflow->id,
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            $item->update([
                'status' => AutofillItemStatus::Failed,
                'error_message' => 'Erreur lors de l\'enrichissement.',
                'error_technical' => mb_substr($e->getMessage(), 0, 1000),
                'step_failed_at' => 'enrichment',
            ]);

            $pipeline->pause(
                $workflow,
                'Une erreur est survenue lors de l\'enrichissement de « '.$item->name.' ». Vous pouvez réessayer.',
                $e->getMessage()
            );
        }
    }

    /**
     * Clean a text field by removing markdown links, inline URLs, and normalizing whitespace.
     */
    private function cleanTextField(string $text): string
    {
        // Remove markdown links: [text](url) → text
        $text = (string) preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);

        // Remove parenthetical citations: (https://...) or (source: https://...)
        $text = (string) preg_replace('/\s*\(\s*(?:source:\s*)?https?:\/\/[^)]+\)/', '', $text);

        // Remove standalone URLs
        $text = (string) preg_replace('/https?:\/\/\S+/', '', $text);

        // Remove markdown bold/italic markers
        $text = str_replace(['**', '__', '*', '_'], '', $text);

        // Normalize whitespace: collapse multiple spaces, trim lines
        $text = (string) preg_replace('/[ \t]+/', ' ', $text);
        $text = (string) preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);

        // Ensure at least 2 paragraphs if text is long enough (>200 chars) and has no paragraph breaks
        if (mb_strlen($text) > 200 && ! str_contains($text, "\n\n")) {
            $sentences = preg_split('/(?<=\.)\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

            if ($sentences !== false && count($sentences) >= 4) {
                $midpoint = (int) ceil(count($sentences) / 2);
                $text = implode(' ', array_slice($sentences, 0, $midpoint))
                    ."\n\n"
                    .implode(' ', array_slice($sentences, $midpoint));
            }
        }

        return $text;
    }

    /**
     * Remove tracking parameters from a source URL.
     */
    private function cleanSourceUrl(string $url): string
    {
        $parsed = parse_url($url);

        if (! isset($parsed['query'])) {
            return $url;
        }

        parse_str($parsed['query'], $params);

        // Remove common tracking parameters
        $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];

        foreach ($trackingParams as $param) {
            unset($params[$param]);
        }

        $cleanUrl = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');

        if (isset($parsed['port'])) {
            $cleanUrl .= ':'.$parsed['port'];
        }

        $cleanUrl .= $parsed['path'] ?? '';

        if (! empty($params)) {
            $cleanUrl .= '?'.http_build_query($params);
        }

        if (isset($parsed['fragment'])) {
            $cleanUrl .= '#'.$parsed['fragment'];
        }

        return $cleanUrl;
    }
}
