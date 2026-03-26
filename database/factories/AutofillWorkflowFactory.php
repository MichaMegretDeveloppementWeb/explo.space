<?php

namespace Database\Factories;

use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutofillWorkflow>
 */
class AutofillWorkflowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => User::factory(),
            'query' => fake()->randomElement([
                'bases de lancement spatiales',
                'observatoires astronomiques en Europe',
                'musées spatiaux aux États-Unis',
                'sites historiques de la conquête spatiale',
                'centres de recherche aérospatiale',
            ]),
            'provider' => fake()->randomElement(['openai', 'anthropic', 'perplexity']),
            'requested_quantity' => fake()->numberBetween(5, 50),
            'status' => AutofillWorkflowStatus::Pending,
            'state' => AutofillWorkflowState::Active,
            'total_tokens_in' => 0,
            'total_tokens_out' => 0,
            'total_cost' => 0,
            'started_at' => now(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutofillWorkflowStatus::Completed,
            'state' => AutofillWorkflowState::Completed,
            'total_tokens_in' => fake()->numberBetween(10000, 50000),
            'total_tokens_out' => fake()->numberBetween(15000, 60000),
            'total_cost' => fake()->randomFloat(6, 0.05, 0.50),
            'completed_at' => now(),
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => AutofillWorkflowState::Paused,
            'error_message' => 'Erreur lors du traitement',
            'error_technical' => 'API timeout after 30s',
        ]);
    }

    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => AutofillWorkflowState::Abandoned,
            'completed_at' => now(),
        ]);
    }

    public function awaitingSelection(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AutofillWorkflowStatus::AwaitingSelection,
            'state' => AutofillWorkflowState::Active,
        ]);
    }
}
