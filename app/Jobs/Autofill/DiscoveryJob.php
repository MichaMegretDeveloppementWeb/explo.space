<?php

namespace App\Jobs\Autofill;

use App\Ai\Agents\DiscoveryAgent;
use App\Enums\AutofillItemStatus;
use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use App\Services\Admin\Autofill\DeduplicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DiscoveryJob implements ShouldQueue
{
    use Concerns\ParsesAgentJson;
    use Concerns\PausesOnFailure;
    use Concerns\TracksTokenUsage;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // Must exceed DiscoveryAgent's #[Timeout(300)] to avoid the queue killing the job mid-execution.
    public int $timeout = 360;

    public int $tries = 1;

    public function __construct(
        public readonly int $workflowId,
    ) {}

    public function handle(AutofillPipelineService $pipeline): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);

        if (! $workflow || ! $workflow->isActive()) {
            return;
        }

        $workflow->update(['status' => AutofillWorkflowStatus::Discovering]);

        try {
            ['provider' => $provider, 'model' => $model] = $this->getProviderAndModel($workflow, 'light');

            // Enable WebSearch ProviderTool only for providers that support it.
            // Perplexity models inherently search the web — the ProviderTool is unnecessary and unsupported.
            $webSearchProviders = ['openai', 'anthropic', 'gemini'];
            $useWebSearch = in_array($provider, $webSearchProviders, true);

            $deduplication = app(DeduplicationService::class);
            $maxIterations = (int) config('autofill.deduplication_max_iterations', 3);

            $uniquePlaces = [];
            $excludedNames = [];
            $remaining = $workflow->requested_quantity;

            for ($iteration = 0; $iteration < $maxIterations && $remaining > 0; $iteration++) {
                $prompt = $this->buildPrompt($workflow, $remaining, $excludedNames);

                $agent = DiscoveryAgent::make(useWebSearch: $useWebSearch);
                $response = $agent->prompt($prompt, provider: $provider, model: $model);

                $this->logStep($workflow, null, 'discovery', $prompt, $response, $provider, $model);

                $places = $this->parseJsonResponse($response->text, 'places');

                if (empty($places)) {
                    break;
                }

                // Deduplication against existing DB places
                $result = $deduplication->filterDuplicates($places);

                // Also deduplicate against already-found unique places within this job
                $result['unique'] = array_filter($result['unique'], function (array $place) use ($uniquePlaces) {
                    foreach ($uniquePlaces as $existing) {
                        if (mb_strtolower($place['name']) === mb_strtolower($existing['name'])) {
                            return false;
                        }
                    }

                    return true;
                });
                $result['unique'] = array_values($result['unique']);

                $uniquePlaces = array_merge($uniquePlaces, $result['unique']);
                $excludedNames = array_merge(
                    $excludedNames,
                    $result['duplicateNames'],
                    array_column($result['unique'], 'name')
                );

                $remaining = $workflow->requested_quantity - count($uniquePlaces);

                // If no duplicates were found, no point in retrying
                if (empty($result['duplicateNames'])) {
                    break;
                }
            }

            if (empty($uniquePlaces)) {
                $pipeline->completeWithMessage(
                    $workflow,
                    'Aucun lieu pertinent trouvé pour cette requête. Essayez avec des termes plus précis.'
                );

                return;
            }

            // Create AutofillItems
            foreach ($uniquePlaces as $place) {
                $workflow->items()->create([
                    'name' => $place['name'],
                    'status' => AutofillItemStatus::Discovered,
                    'enrichment_data' => [
                        'approximate_location' => $place['approximate_location'] ?? null,
                        'justification' => $place['justification'] ?? null,
                    ],
                ]);
            }

            // Create selection message (remove any previous one from a resumed attempt)
            $workflow->messages()
                ->where('type', AutofillMessageType::Selection)
                ->delete();

            $candidates = $workflow->items()
                ->where('status', AutofillItemStatus::Discovered)
                ->get()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'location' => $item->enrichment_data['approximate_location'] ?? '',
                    'justification' => $item->enrichment_data['justification'] ?? '',
                ])
                ->toArray();

            $pipeline->createMessage($workflow, AutofillMessageType::Selection, AutofillMessageRole::System, [
                'text' => count($candidates).' lieu(x) trouvé(s). Sélectionnez ceux que vous souhaitez importer :',
                'items' => $candidates,
            ]);

            $workflow->update(['status' => AutofillWorkflowStatus::AwaitingSelection]);
        } catch (\Throwable $e) {
            Log::error('Discovery job failed', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
            ]);

            $userMessage = match (true) {
                str_contains($e->getMessage(), 'rate limit') => 'Le fournisseur IA a limité les requêtes. Réessayez dans quelques minutes.',
                str_contains($e->getMessage(), 'not supported') => 'Le fournisseur sélectionné n\'est pas disponible. Essayez avec un autre.',
                str_contains($e->getMessage(), 'invalid_request_error') => 'Erreur de configuration du modèle IA. Contactez un administrateur.',
                default => 'Une erreur est survenue lors de la recherche. Vous pouvez réessayer.',
            };

            $pipeline->pause($workflow, $userMessage, $e->getMessage());
        }
    }

    /**
     * Build the discovery prompt.
     *
     * @param  array<int, string>  $excludedNames  Names to exclude from results
     */
    private function buildPrompt(AutofillWorkflow $workflow, int $quantity, array $excludedNames): string
    {
        $prompt = "Find {$quantity} space-related places matching the following user query. "
            ."The query may be in any language — translate it to English first, then search in BOTH the original language AND English to maximize results.\n\n"
            ."User query: {$workflow->query}";

        if (! empty($excludedNames)) {
            $namesList = implode(', ', $excludedNames);
            $prompt .= "\n\n## Places to EXCLUDE (already found or already in database — find DIFFERENT ones):\n".$namesList;
        }

        return $prompt;
    }
}
