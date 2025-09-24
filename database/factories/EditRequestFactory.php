<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EditRequest>
 */
class EditRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_email' => $this->faker->safeEmail(),
            'place_id' => \App\Models\Place::factory(),
            'type' => $this->faker->randomElement(['modification', 'signalement']),
            'description' => $this->faker->paragraph(),
            'suggested_changes' => [],
            'status' => 'submitted',
        ];
    }
}
