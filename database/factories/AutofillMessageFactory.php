<?php

namespace Database\Factories;

use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Models\AutofillWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutofillMessage>
 */
class AutofillMessageFactory extends Factory
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
            'type' => AutofillMessageType::Text,
            'role' => AutofillMessageRole::System,
            'payload' => ['text' => 'Recherche en cours...'],
            'created_at' => now(),
        ];
    }

    public function userMessage(string $text = 'bases de lancement'): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AutofillMessageType::Text,
            'role' => AutofillMessageRole::User,
            'payload' => ['text' => $text],
        ]);
    }

    public function selection(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AutofillMessageType::Selection,
            'role' => AutofillMessageRole::System,
            'payload' => ['items' => [], 'message' => '10 lieux trouvés'],
        ]);
    }
}
