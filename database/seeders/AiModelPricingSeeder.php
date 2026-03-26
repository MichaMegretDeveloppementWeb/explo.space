<?php

namespace Database\Seeders;

use App\Models\AiModelPricing;
use Illuminate\Database\Seeder;

class AiModelPricingSeeder extends Seeder
{
    /**
     * Seed the ai_model_pricing table with current prices.
     * Idempotent: uses updateOrCreate.
     */
    public function run(): void
    {
        $models = [
            // OpenAI
            ['provider' => 'openai', 'model' => 'gpt-4o-mini', 'price_input_per_million' => 0.15, 'price_output_per_million' => 0.60],

            // Anthropic
            ['provider' => 'anthropic', 'model' => 'claude-sonnet-4-6', 'price_input_per_million' => 3.00, 'price_output_per_million' => 15.00],
            ['provider' => 'anthropic', 'model' => 'claude-haiku-4-5-20251001', 'price_input_per_million' => 0.80, 'price_output_per_million' => 4.00],

            // Google Gemini
            ['provider' => 'gemini', 'model' => 'gemini-3-flash-preview', 'price_input_per_million' => 0.15, 'price_output_per_million' => 0.60],
        ];

        foreach ($models as $model) {
            AiModelPricing::updateOrCreate(
                ['provider' => $model['provider'], 'model' => $model['model']],
                [
                    'price_input_per_million' => $model['price_input_per_million'],
                    'price_output_per_million' => $model['price_output_per_million'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
