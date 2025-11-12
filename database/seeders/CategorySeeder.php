<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed categories (no translations, internal admin use only)
     */
    public function run(): void
    {
        // 1. Space Agencies Category
        Category::factory()->create([
            'name' => 'Agences spatiales',
            'slug' => 'agences-spatiales',
            'description' => 'Centres de contrôle et agences spatiales mondiales',
            'color' => '#3B82F6',
            'is_active' => true,
        ]);

        // 2. Launch Sites Category
        Category::factory()->create([
            'name' => 'Sites de lancement',
            'slug' => 'sites-de-lancement',
            'description' => 'Bases de lancement de fusées et missions spatiales',
            'color' => '#EF4444',
            'is_active' => true,
        ]);

        // 3. Museums Category
        Category::factory()->create([
            'name' => 'Musées et expositions',
            'slug' => 'musees-et-expositions',
            'description' => 'Musées dédiés à l\'espace et expositions temporaires',
            'color' => '#10B981',
            'is_active' => true,
        ]);

        // 4. Observatories Category
        Category::factory()->create([
            'name' => 'Observatoires',
            'slug' => 'observatoires',
            'description' => 'Observatoires astronomiques et télescopes',
            'color' => '#8B5CF6',
            'is_active' => true,
        ]);
    }
}
