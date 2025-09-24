<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'latitude' => fake()->latitude(40, 50),
            'longitude' => fake()->longitude(-5, 10),
            'address' => fake()->address(),
            'is_featured' => false,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function nearParis(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => fake()->latitude(48.5, 49.0),
            'longitude' => fake()->longitude(2.0, 2.7),
        ]);
    }
}
