<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlaceTranslation>
 */
class PlaceTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3, false);

        return [
            'place_id' => Place::factory(),
            'locale' => 'fr',
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(5),
            'practical_info' => fake()->optional()->paragraph(),
            'status' => 'published',
            'source_hash' => null,
        ];
    }

    public function french(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'fr',
        ]);
    }

    public function english(): static
    {
        $title = fake('en_US')->sentence(3, false);

        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake('en_US')->paragraph(5),
            'practical_info' => fake('en_US')->optional()->paragraph(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
