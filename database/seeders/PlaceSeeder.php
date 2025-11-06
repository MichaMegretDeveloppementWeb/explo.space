<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    /**
     * Seed places with their translations (FR + EN)
     */
    public function run(): void
    {
        // Récupérer l'admin créé par UserSeeder
        $admin = User::where('email', 'admin@example.com')->first();

        if (! $admin) {
            $this->command->error('Admin user not found. Please run UserSeeder first.');

            return;
        }

        // Récupérer les tags créés par TagSeeder
        $nasaTag = Tag::whereHas('translations', function ($query) {
            $query->where('slug', 'nasa');
        })->first();

        $spacexTag = Tag::whereHas('translations', function ($query) {
            $query->where('slug', 'spacex');
        })->first();

        if (! $nasaTag || ! $spacexTag) {
            $this->command->error('Tags not found. Please run TagSeeder first.');

            return;
        }

        // 2. Create 20 sample places with factory
        for ($i = 0; $i < 20; $i++) {
            $place = Place::factory()->create([
                'admin_id' => $admin->id,
                'is_featured' => fake()->boolean(20), // 20% chance to be featured
            ]);

            // Create French translation (default)
            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
            ]);

            // Create English translation (always)
            PlaceTranslation::factory()->english()->create([
                'place_id' => $place->id,
            ]);

            // Attach random tag (always)
            $place->tags()->attach([$nasaTag->id, $spacexTag->id][fake()->numberBetween(0, 1)]);
        }
    }
}
