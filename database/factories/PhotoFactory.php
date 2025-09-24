<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->uuid().'.jpg';

        return [
            'place_id' => Place::factory(),
            'filename' => $filename,
            'original_name' => fake()->words(3, true).'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(100000, 5000000),
            'alt_text' => fake()->sentence(),
            'is_main' => false,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
            'sort_order' => 0,
        ]);
    }

    public function withPlace(Place $place): static
    {
        return $this->state(fn (array $attributes) => [
            'place_id' => $place->id,
        ]);
    }
}
