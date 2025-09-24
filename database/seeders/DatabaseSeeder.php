<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => 'super_admin',
        ]);

        // Create categories with multilingual content
        $spaceAgencyCategory = Category::factory()->create([
            'color' => '#3B82F6',
            'is_active' => true,
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $spaceAgencyCategory->id,
            'locale' => 'fr',
            'name' => 'Agences spatiales',
            'slug' => 'agences-spatiales',
            'description' => 'Centres de contrôle et agences spatiales mondiales',
            'status' => 'published',
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $spaceAgencyCategory->id,
            'locale' => 'en',
            'name' => 'Space Agencies',
            'slug' => 'space-agencies',
            'description' => 'Global space agencies and control centers',
            'status' => 'published',
        ]);

        $launchSiteCategory = Category::factory()->create([
            'color' => '#EF4444',
            'is_active' => true,
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $launchSiteCategory->id,
            'locale' => 'fr',
            'name' => 'Sites de lancement',
            'slug' => 'sites-de-lancement',
            'description' => 'Bases de lancement de fusées et missions spatiales',
            'status' => 'published',
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $launchSiteCategory->id,
            'locale' => 'en',
            'name' => 'Launch Sites',
            'slug' => 'launch-sites',
            'description' => 'Rocket launch bases and space mission facilities',
            'status' => 'published',
        ]);

        // Create tags with multilingual content
        $nasaTag = Tag::factory()->create([
            'color' => '#1E40AF',
            'is_active' => true,
        ]);

        TagTranslation::factory()->create([
            'tag_id' => $nasaTag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'description' => 'Agence spatiale américaine',
            'status' => 'published',
        ]);

        TagTranslation::factory()->create([
            'tag_id' => $nasaTag->id,
            'locale' => 'en',
            'name' => 'NASA',
            'slug' => 'nasa',
            'description' => 'National Aeronautics and Space Administration',
            'status' => 'published',
        ]);

        $spacexTag = Tag::factory()->create([
            'color' => '#10B981',
            'is_active' => true,
        ]);

        TagTranslation::factory()->create([
            'tag_id' => $spacexTag->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'description' => 'Entreprise privée d\'exploration spatiale',
            'status' => 'published',
        ]);

        TagTranslation::factory()->create([
            'tag_id' => $spacexTag->id,
            'locale' => 'en',
            'name' => 'SpaceX',
            'slug' => 'spacex',
            'description' => 'Private space exploration company',
            'status' => 'published',
        ]);

        // Create places with multilingual content
        $kennedySpace = Place::factory()->create([
            'latitude' => 28.573255,
            'longitude' => -80.651070,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'is_featured' => true,
            'admin_id' => $admin->id,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $kennedySpace->id,
            'locale' => 'fr',
            'title' => 'Centre spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
            'description' => 'Le Centre spatial Kennedy est le principal site de lancement de la NASA, situé en Floride. C\'est de là que partent les missions vers la Station spatiale internationale et les futures missions vers la Lune et Mars.',
            'practical_info' => 'Ouvert tous les jours. Visites guidées disponibles. Réservation recommandée.',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $kennedySpace->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
            'slug' => 'kennedy-space-center',
            'description' => 'Kennedy Space Center is NASA\'s primary launch site, located in Florida. It serves as the departure point for missions to the International Space Station and future missions to the Moon and Mars.',
            'practical_info' => 'Open daily. Guided tours available. Reservation recommended.',
            'status' => 'published',
        ]);

        // Attach tags to the place
        $kennedySpace->tags()->attach([$nasaTag->id]);

        // Create more sample places
        for ($i = 0; $i < 10; $i++) {
            $place = Place::factory()->create([
                'admin_id' => $admin->id,
                'is_featured' => fake()->boolean(20), // 20% chance to be featured
            ]);

            // Create French translation
            PlaceTranslation::factory()->french()->create([
                'place_id' => $place->id,
            ]);

            // Create English translation for some places
            if (fake()->boolean(70)) { // 70% chance to have English translation
                PlaceTranslation::factory()->english()->create([
                    'place_id' => $place->id,
                ]);
            }

            // Attach random tags
            if (fake()->boolean(80)) { // 80% chance to have tags
                $place->tags()->attach([$nasaTag->id, $spacexTag->id][fake()->numberBetween(0, 1)]);
            }
        }
    }
}
