<?php

namespace Database\Factories;

use App\Models\AutofillWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutofillStepLog>
 */
class AutofillStepLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workflow_id' => AutofillWorkflow::factory(),
            'item_id' => null,
            'step' => fake()->randomElement(['discovery', 'enrichment', 'images_search', 'alt_text', 'translation', 'tag_suggestion']),
            'input_data' => ['query' => 'bases de lancement spatiales'],
            'raw_output' => fake()->paragraph(3),
            'tokens_in' => fake()->numberBetween(500, 3000),
            'tokens_out' => fake()->numberBetween(300, 2000),
            'cost' => fake()->randomFloat(6, 0.001, 0.05),
            'model' => fake()->randomElement(['gpt-4o-mini', 'claude-sonnet-4-5-20250514', 'sonar-pro']),
            'created_at' => now(),
        ];
    }
}
