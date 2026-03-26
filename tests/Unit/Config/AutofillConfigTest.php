<?php

namespace Tests\Unit\Config;

use Tests\TestCase;

class AutofillConfigTest extends TestCase
{
    public function test_all_providers_have_required_model_tiers(): void
    {
        $providers = config('autofill.providers');

        $this->assertNotEmpty($providers, 'No providers configured');

        foreach ($providers as $providerName => $providerConfig) {
            $this->assertArrayHasKey('models', $providerConfig, "Provider '{$providerName}' missing 'models' key");
            $this->assertArrayHasKey('mid', $providerConfig['models'], "Provider '{$providerName}' missing 'mid' model");
            $this->assertArrayHasKey('light', $providerConfig['models'], "Provider '{$providerName}' missing 'light' model");
            $this->assertNotEmpty($providerConfig['models']['mid'], "Provider '{$providerName}' has empty 'mid' model");
            $this->assertNotEmpty($providerConfig['models']['light'], "Provider '{$providerName}' has empty 'light' model");
        }
    }

    public function test_expected_providers_are_configured(): void
    {
        $providers = config('autofill.providers');

        $this->assertArrayHasKey('openai', $providers);
        $this->assertArrayHasKey('anthropic', $providers);
        $this->assertArrayHasKey('gemini', $providers);
    }

    public function test_gemini_model_ids_follow_correct_format(): void
    {
        $geminiModels = config('autofill.providers.gemini.models');

        foreach ($geminiModels as $tier => $modelId) {
            $this->assertMatchesRegularExpression(
                '/^gemini-/',
                $modelId,
                "Gemini '{$tier}' model '{$modelId}' should start with 'gemini-'"
            );
        }
    }

    public function test_anthropic_model_ids_follow_correct_format(): void
    {
        $anthropicModels = config('autofill.providers.anthropic.models');

        foreach ($anthropicModels as $tier => $modelId) {
            $this->assertMatchesRegularExpression(
                '/^claude-/',
                $modelId,
                "Anthropic '{$tier}' model '{$modelId}' should start with 'claude-'"
            );
        }
    }

    public function test_openai_model_ids_follow_correct_format(): void
    {
        $openaiModels = config('autofill.providers.openai.models');

        foreach ($openaiModels as $tier => $modelId) {
            // OpenAI model IDs start with 'gpt-' or 'o1' or similar
            $this->assertMatchesRegularExpression(
                '/^(gpt-|o[134]|chatgpt)/',
                $modelId,
                "OpenAI '{$tier}' model '{$modelId}' should start with a valid OpenAI model prefix"
            );
        }
    }

    public function test_default_provider_is_valid(): void
    {
        $defaultProvider = config('autofill.default_provider');
        $providers = config('autofill.providers');

        $this->assertArrayHasKey(
            $defaultProvider,
            $providers,
            "Default provider '{$defaultProvider}' is not in the configured providers list"
        );
    }

    public function test_quantity_limits_are_reasonable(): void
    {
        $defaultQty = config('autofill.default_quantity');
        $maxQty = config('autofill.max_quantity');

        $this->assertGreaterThan(0, $defaultQty);
        $this->assertGreaterThanOrEqual($defaultQty, $maxQty);
        $this->assertLessThanOrEqual(100, $maxQty);
    }
}
