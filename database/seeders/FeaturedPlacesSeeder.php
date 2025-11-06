<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class FeaturedPlacesSeeder extends Seeder
{
    private string $disk = 'place_photos';

    /**
     * Données des lieux emblématiques basées sur featured-places.blade.php
     */
    private array $featuredPlaces = [
        [
            'title_fr' => 'Centre spatial Kennedy',
            'title_en' => 'Kennedy Space Center',
            'slug_fr' => 'centre-spatial-kennedy',
            'slug_en' => 'kennedy-space-center',
            'description_fr' => 'Centre de lancement historique de la NASA en Floride, berceau des missions Apollo et des navettes spatiales.',
            'description_en' => 'Historic NASA launch center in Florida, birthplace of Apollo missions and space shuttles.',
            'latitude' => 28.573469,
            'longitude' => -80.651070,
            'address' => 'Kennedy Space Center, FL 32899, États-Unis',
            'practical_info_fr' => 'Ouvert tous les jours de 9h à 17h. Visites guidées disponibles. Réservation recommandée.',
            'practical_info_en' => 'Open daily from 9am to 5pm. Guided tours available. Booking recommended.',
            'image_source' => 'kennedy_space_center.jpg',
            'tag_name_fr' => 'NASA',
            'tag_name_en' => 'NASA',
            'tag_color' => '#1E40AF',
        ],
        [
            'title_fr' => 'Cosmodrome de Baikonour',
            'title_en' => 'Baikonur Cosmodrome',
            'slug_fr' => 'cosmodrome-de-baikonour',
            'slug_en' => 'baikonur-cosmodrome',
            'description_fr' => 'Premier cosmodrome au monde, site historique du vol de Gagarine et base actuelle des missions Soyouz.',
            'description_en' => 'World\'s first cosmodrome, historic site of Gagarin\'s flight and current base for Soyuz missions.',
            'latitude' => 45.920278,
            'longitude' => 63.342222,
            'address' => 'Baikonur, Kazakhstan',
            'practical_info_fr' => 'Visites limitées sur autorisation spéciale. Contactez Roscosmos pour les modalités.',
            'practical_info_en' => 'Limited visits by special authorization. Contact Roscosmos for procedures.',
            'image_source' => 'cosmodrome_baikonour.jpg',
            'tag_name_fr' => 'Roscosmos',
            'tag_name_en' => 'Roscosmos',
            'tag_color' => '#DC2626',
        ],
        [
            'title_fr' => 'Observatoire ALMA',
            'title_en' => 'ALMA Observatory',
            'slug_fr' => 'observatoire-alma',
            'slug_en' => 'alma-observatory',
            'description_fr' => 'Plus grand projet astronomique au monde, 66 antennes dans le désert d\'Atacama pour sonder l\'univers lointain.',
            'description_en' => 'World\'s largest astronomical project, 66 antennas in the Atacama desert to probe the distant universe.',
            'latitude' => -24.013,
            'longitude' => -67.754,
            'address' => 'Désert d\'Atacama, Région d\'Antofagasta, Chili',
            'practical_info_fr' => 'Visites publiques le week-end sur réservation. Altitude élevée, prévoir vêtements chauds.',
            'practical_info_en' => 'Public visits on weekends by reservation. High altitude, bring warm clothing.',
            'image_source' => 'observatoire_alma.jpg',
            'tag_name_fr' => 'Observatoire',
            'tag_name_en' => 'Observatory',
            'tag_color' => '#7C3AED',
        ],
    ];

    public function run(): void
    {
        $this->command->info('🚀 Création des lieux emblématiques featured-places...');

        DB::transaction(function () {
            // Obtenir un admin pour les lieux
            $admin = $this->getOrCreateAdmin();

            foreach ($this->featuredPlaces as $placeData) {
                $this->command->info("📍 Vérification : {$placeData['title_fr']}");

                // Vérifier si le lieu existe déjà
                $existingPlace = $this->findExistingPlace($placeData);

                if ($existingPlace) {
                    $this->command->warn("⚠️ Le lieu '{$placeData['title_fr']}' existe déjà (ID: {$existingPlace->id})");

                    continue;
                }

                // 1. Créer ou récupérer le tag
                $tag = $this->createOrGetTag($placeData);

                // 2. Créer le lieu
                $place = $this->createPlace($placeData, $admin);

                // 3. Créer les traductions
                $this->createPlaceTranslations($place, $placeData);

                // 4. Associer le tag au lieu
                $place->tags()->attach($tag->id);

                // 5. Traiter et créer la photo
                $this->createPhoto($place, $placeData);

                $this->command->info("✅ {$placeData['title_fr']} créé avec succès");
            }
        });

        $this->command->info('🎉 Tous les lieux emblématiques ont été créés avec succès !');
        $this->command->info('📸 Photos copiées et miniatures générées');
        $this->command->info('🏷️ Tags créés avec traductions FR/EN');
        $this->command->info('🌟 Tous les lieux marqués comme featured');
    }

    private function getOrCreateAdmin(): User
    {
        // Chercher un admin existant
        $admin = User::where('role', 'admin')->orWhere('role', 'super-admin')->first();

        if (! $admin) {
            // Créer un admin temporaire pour les seeders
            $admin = User::create([
                'name' => 'Admin Seeder',
                'email' => 'admin.seeder@explo.space',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->command->warn('⚠️ Admin temporaire créé pour les seeders');
        }

        return $admin;
    }

    private function findExistingPlace(array $placeData): ?Place
    {
        // Chercher par slug FR dans les traductions
        return Place::whereHas('translations', function ($query) use ($placeData) {
            $query->where('locale', 'fr')
                ->where('slug', $placeData['slug_fr']);
        })->first();
    }

    private function createOrGetTag(array $placeData): Tag
    {
        // Vérifier si le tag existe déjà (par nom FR)
        $existingTag = Tag::whereHas('translations', function ($query) use ($placeData) {
            $query->where('locale', 'fr')
                ->where('name', $placeData['tag_name_fr']);
        })->first();

        if ($existingTag) {
            return $existingTag;
        }

        // Créer le nouveau tag
        $tag = Tag::create([
            'color' => $placeData['tag_color'],
            'is_active' => true,
        ]);

        // Créer les traductions du tag
        TagTranslation::create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => $placeData['tag_name_fr'],
            'slug' => str($placeData['tag_name_fr'])->slug(),
            'description' => '',
            'status' => 'published',
        ]);

        TagTranslation::create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => $placeData['tag_name_en'],
            'slug' => str($placeData['tag_name_en'])->slug(),
            'description' => '',
            'status' => 'published',
        ]);

        return $tag;
    }

    private function createPlace(array $placeData, User $admin): Place
    {
        return Place::create([
            'latitude' => $placeData['latitude'],
            'longitude' => $placeData['longitude'],
            'address' => $placeData['address'],
            'is_featured' => true, // Tous les lieux sont featured
            'admin_id' => $admin->id,
            'request_id' => null,
        ]);
    }

    private function createPlaceTranslations(Place $place, array $placeData): void
    {
        // Traduction française
        PlaceTranslation::create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => $placeData['title_fr'],
            'slug' => $placeData['slug_fr'],
            'description' => $placeData['description_fr'],
            'practical_info' => $placeData['practical_info_fr'],
            'status' => 'published',
        ]);

        // Traduction anglaise
        PlaceTranslation::create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => $placeData['title_en'],
            'slug' => $placeData['slug_en'],
            'description' => $placeData['description_en'],
            'practical_info' => $placeData['practical_info_en'],
            'status' => 'published',
        ]);
    }

    private function createPhoto(Place $place, array $placeData): void
    {
        $sourceImage = $placeData['image_source'];
        $sourcePath = storage_path("app/public/images/places/{$sourceImage}");

        if (! File::exists($sourcePath)) {
            $this->command->error("❌ Image source non trouvée : {$sourcePath}");

            return;
        }

        // Créer les répertoires nécessaires
        $this->ensurePhotosDirectories();

        // Générer un nom unique pour la photo
        $filename = uniqid().'.jpg';
        $destinationPath = Storage::disk($this->disk)->path($filename);

        try {
            // Copier et redimensionner l'image principale
            $manager = new ImageManager(new Driver);
            $image = $manager->read($sourcePath);

            // Redimensionner l'image principale (max 1200px de large)
            $image->scale(width: 1200);
            $image->save($destinationPath, quality: 85);

            // Générer les miniatures
            $this->generateThumbnails($sourcePath, $filename, $manager);

            // Créer l'enregistrement en base
            $originalName = pathinfo($sourceImage, PATHINFO_FILENAME);
            $fileSize = File::size($destinationPath);

            Photo::create([
                'place_id' => $place->id,
                'filename' => $filename,
                'original_name' => $originalName,
                'mime_type' => 'image/jpeg',
                'size' => $fileSize,
                'alt_text' => $placeData['title_fr'],
                'is_main' => true, // Photo principale
                'sort_order' => 1,
            ]);

            $this->command->info("📸 Photo créée : {$filename}");

        } catch (\Exception $e) {
            $this->command->error("❌ Erreur lors du traitement de l'image : ".$e->getMessage());
        }
    }

    private function ensurePhotosDirectories(): void
    {
        $disk = Storage::disk($this->disk);
        $directories = ['', 'thumbs', 'medium'];

        foreach ($directories as $dir) {
            if (! $disk->exists($dir)) {
                $disk->makeDirectory($dir);
            }
        }
    }

    private function generateThumbnails(string $sourcePath, string $filename, ImageManager $manager): void
    {
        $disk = Storage::disk($this->disk);
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Miniature (150x150)
        $thumbFilename = "{$baseName}_thumb.{$extension}";
        $thumbPath = $disk->path("thumbs/{$thumbFilename}");

        $thumbImage = $manager->read($sourcePath);
        $thumbImage->cover(150, 150);
        $thumbImage->save($thumbPath, quality: 80);

        // Taille moyenne (400px)
        $mediumFilename = "{$baseName}_medium.{$extension}";
        $mediumPath = $disk->path("medium/{$mediumFilename}");

        $mediumImage = $manager->read($sourcePath);
        $mediumImage->scale(width: 400);
        $mediumImage->save($mediumPath, quality: 85);
    }
}
