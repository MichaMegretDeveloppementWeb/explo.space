<?php

namespace Database\Factories;

use App\Enums\AutofillItemStatus;
use App\Models\AutofillWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutofillItem>
 */
class AutofillItemFactory extends Factory
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
            'name' => fake()->randomElement([
                'Kennedy Space Center',
                'Baikonur Cosmodrome',
                'Centre spatial guyanais',
                'Vandenberg Space Force Base',
                'Tanegashima Space Center',
                'Jiuquan Satellite Launch Center',
                'Observatoire de Paris',
                'Cité de l\'Espace',
                'National Air and Space Museum',
                'Euro Space Center',
            ]),
            'status' => AutofillItemStatus::Discovered,
            'tokens_in' => 0,
            'tokens_out' => 0,
            'cost' => 0,
            'images_count' => 0,
        ];
    }

    public function saved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutofillItemStatus::Saved,
            'tokens_in' => fake()->numberBetween(1000, 5000),
            'tokens_out' => fake()->numberBetween(1500, 6000),
            'cost' => fake()->randomFloat(6, 0.005, 0.05),
            'images_count' => fake()->numberBetween(1, 5),
            'suggested_tags' => 'base de lancement, NASA, exploration spatiale',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutofillItemStatus::Failed,
            'step_failed_at' => 'enrichment',
            'error_message' => 'Impossible d\'enrichir ce lieu',
            'error_technical' => 'Structured output parsing failed',
        ]);
    }

    public function selected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutofillItemStatus::Selected,
        ]);
    }

    public function enriched(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutofillItemStatus::Enriched,
            'enrichment_data' => [
                'title' => $attributes['name'] ?? 'Kennedy Space Center',
                'description' => 'Un centre de lancement spatial majeur.',
                'latitude' => fake()->latitude(),
                'longitude' => fake()->longitude(),
                'address' => fake()->address(),
                'practical_info' => 'Ouvert tous les jours de 9h à 17h.',
            ],
        ]);
    }
}
