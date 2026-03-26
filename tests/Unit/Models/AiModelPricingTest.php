<?php

namespace Tests\Unit\Models;

use App\Models\AiModelPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiModelPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_can_be_created(): void
    {
        $pricing = AiModelPricing::create([
            'provider' => 'openai',
            'model' => 'gpt-test',
            'price_input_per_million' => 1.0000,
            'price_output_per_million' => 5.0000,
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('ai_model_pricing', [
            'provider' => 'openai',
            'model' => 'gpt-test',
        ]);
    }

    public function test_calculate_cost_returns_correct_value(): void
    {
        $pricing = AiModelPricing::create([
            'provider' => 'openai',
            'model' => 'gpt-test',
            'price_input_per_million' => 0.75,
            'price_output_per_million' => 4.50,
            'updated_at' => now(),
        ]);

        // 1000 input tokens at $0.75/1M = $0.00075
        // 2000 output tokens at $4.50/1M = $0.009
        // Total = $0.00975
        $cost = $pricing->calculateCost(1000, 2000);

        $this->assertEquals(0.00975, $cost);
    }

    public function test_calculate_cost_with_zero_tokens(): void
    {
        $pricing = AiModelPricing::create([
            'provider' => 'anthropic',
            'model' => 'claude-test',
            'price_input_per_million' => 3.00,
            'price_output_per_million' => 15.00,
            'updated_at' => now(),
        ]);

        $this->assertEquals(0.0, $pricing->calculateCost(0, 0));
    }

    public function test_calculate_cost_with_large_token_count(): void
    {
        $pricing = AiModelPricing::create([
            'provider' => 'perplexity',
            'model' => 'sonar-pro',
            'price_input_per_million' => 3.00,
            'price_output_per_million' => 15.00,
            'updated_at' => now(),
        ]);

        // 1M input = $3.00, 1M output = $15.00 => $18.00
        $cost = $pricing->calculateCost(1_000_000, 1_000_000);

        $this->assertEquals(18.0, $cost);
    }

    public function test_scope_for_model_finds_correct_record(): void
    {
        AiModelPricing::create([
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'price_input_per_million' => 0.15,
            'price_output_per_million' => 0.60,
            'updated_at' => now(),
        ]);

        AiModelPricing::create([
            'provider' => 'anthropic',
            'model' => 'claude-haiku-3-5-20241022',
            'price_input_per_million' => 0.80,
            'price_output_per_million' => 4.00,
            'updated_at' => now(),
        ]);

        $result = AiModelPricing::forModel('openai', 'gpt-4o-mini')->first();

        $this->assertNotNull($result);
        $this->assertEquals('0.1500', $result->price_input_per_million);
    }

    public function test_scope_for_model_returns_empty_for_unknown(): void
    {
        $result = AiModelPricing::forModel('unknown', 'model')->first();

        $this->assertNull($result);
    }

    public function test_unique_constraint_on_provider_and_model(): void
    {
        AiModelPricing::create([
            'provider' => 'openai',
            'model' => 'gpt-unique-test',
            'price_input_per_million' => 1.00,
            'price_output_per_million' => 5.00,
            'updated_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        AiModelPricing::create([
            'provider' => 'openai',
            'model' => 'gpt-unique-test',
            'price_input_per_million' => 2.00,
            'price_output_per_million' => 10.00,
            'updated_at' => now(),
        ]);
    }
}
