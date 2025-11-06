<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Seed tags with their translations (FR + EN)
     */
    public function run(): void
    {
        // 1. NASA Tag
        $nasaTag = Tag::factory()->create([
            'color' => '#1E40AF',
            'is_active' => true,
        ]);

        // French translation
        TagTranslation::factory()->create([
            'tag_id' => $nasaTag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'description' => 'Agence spatiale américaine',
            'status' => 'published',
        ]);

        // English translation
        TagTranslation::factory()->create([
            'tag_id' => $nasaTag->id,
            'locale' => 'en',
            'name' => 'NASA',
            'slug' => 'nasa',
            'description' => 'National Aeronautics and Space Administration',
            'status' => 'published',
        ]);

        // 2. SpaceX Tag
        $spacexTag = Tag::factory()->create([
            'color' => '#10B981',
            'is_active' => true,
        ]);

        // French translation
        TagTranslation::factory()->create([
            'tag_id' => $spacexTag->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'description' => 'Entreprise privée d\'exploration spatiale',
            'status' => 'published',
        ]);

        // English translation
        TagTranslation::factory()->create([
            'tag_id' => $spacexTag->id,
            'locale' => 'en',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'description' => 'Private space exploration company',
            'status' => 'published',
        ]);
    }
}
