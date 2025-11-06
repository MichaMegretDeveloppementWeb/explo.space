<?php

namespace Database\Factories;

use App\Models\PlaceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlaceRequestPhoto>
 */
class PlaceRequestPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'place_request_id' => PlaceRequest::factory(),
            'filename' => fake()->uuid().'.jpg',
            'original_name' => fake()->lexify('photo-?????.jpg'),
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(100000, 5000000), // 100KB à 5MB
            'sort_order' => 0,
        ];
    }
}
