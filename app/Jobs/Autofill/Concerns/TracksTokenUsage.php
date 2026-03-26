<?php

namespace App\Jobs\Autofill\Concerns;

use App\Models\AiModelPricing;
use App\Models\AutofillItem;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use Laravel\Ai\Responses\AgentResponse;

trait TracksTokenUsage
{
    /**
     * Log a step and track token usage on the item and workflow.
     */
    protected function logStep(
        AutofillWorkflow $workflow,
        ?AutofillItem $item,
        string $step,
        mixed $inputData,
        AgentResponse $response,
        string $provider,
        string $model
    ): void {
        $tokensIn = $response->usage->promptTokens;
        $tokensOut = $response->usage->completionTokens;
        $cost = $this->calculateCost($provider, $model, $tokensIn, $tokensOut);

        // Ensure raw_output is never empty — fallback to string cast.
        $rawOutput = $response->text;

        if (empty($rawOutput)) {
            $rawOutput = (string) $response;
        }

        AutofillStepLog::create([
            'workflow_id' => $workflow->id,
            'item_id' => $item?->id,
            'step' => $step,
            'input_data' => is_array($inputData) ? $inputData : ['prompt' => $inputData],
            'raw_output' => $rawOutput,
            'tokens_in' => $tokensIn,
            'tokens_out' => $tokensOut,
            'cost' => $cost,
            'model' => $model,
            'created_at' => now(),
        ]);

        if ($item) {
            $item->increment('tokens_in', $tokensIn);
            $item->increment('tokens_out', $tokensOut);
            $item->increment('cost', $cost);
        }

        $workflow->increment('total_tokens_in', $tokensIn);
        $workflow->increment('total_tokens_out', $tokensOut);
        $workflow->increment('total_cost', $cost);
    }

    protected function calculateCost(string $provider, string $model, int $tokensIn, int $tokensOut): float
    {
        $pricing = AiModelPricing::query()
            ->forModel($provider, $model)
            ->first();

        if (! $pricing) {
            return 0.0;
        }

        return $pricing->calculateCost($tokensIn, $tokensOut);
    }

    /**
     * Get provider and model for a given tier from the workflow.
     *
     * @return array{provider: string, model: string}
     */
    protected function getProviderAndModel(AutofillWorkflow $workflow, string $tier = 'mid'): array
    {
        $provider = $workflow->provider;
        $model = config("autofill.providers.{$provider}.models.{$tier}", '');

        return ['provider' => $provider, 'model' => $model];
    }
}
