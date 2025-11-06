<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed categories with their translations (FR + EN)
     */
    public function run(): void
    {
        // 1. Space Agencies Category
        $spaceAgencyCategory = Category::factory()->create([
            'color' => '#3B82F6',
            'is_active' => true,
        ]);

        // French translation
        CategoryTranslation::factory()->create([
            'category_id' => $spaceAgencyCategory->id,
            'locale' => 'fr',
            'name' => 'Agences spatiales',
            'slug' => 'agences-spatiales',
            'description' => 'Centres de contrôle et agences spatiales mondiales',
            'status' => 'published',
        ]);

        // English translation
        CategoryTranslation::factory()->create([
            'category_id' => $spaceAgencyCategory->id,
            'locale' => 'en',
            'name' => 'Space Agencies',
            'slug' => 'space-agencies',
            'description' => 'Global space agencies and control centers',
            'status' => 'published',
        ]);

        // 2. Launch Sites Category
        $launchSiteCategory = Category::factory()->create([
            'color' => '#EF4444',
            'is_active' => true,
        ]);

        // French translation
        CategoryTranslation::factory()->create([
            'category_id' => $launchSiteCategory->id,
            'locale' => 'fr',
            'name' => 'Sites de lancement',
            'slug' => 'sites-de-lancement',
            'description' => 'Bases de lancement de fusées et missions spatiales',
            'status' => 'published',
        ]);

        // English translation
        CategoryTranslation::factory()->create([
            'category_id' => $launchSiteCategory->id,
            'locale' => 'en',
            'name' => 'Launch Sites',
            'slug' => 'launch-sites',
            'description' => 'Rocket launch bases and space mission facilities',
            'status' => 'published',
        ]);
    }
}
