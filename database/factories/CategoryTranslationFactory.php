<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryTranslation>
 */
class CategoryTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'category_id' => Category::factory(),
            'locale' => 'fr',
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
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
        $name = fake('en_US')->words(2, true);

        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake('en_US')->sentence(),
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
