<?php

namespace Tests\Feature\Web\Place;

use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceShowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_show_page_returns_200_for_valid_place(): void
    {
        $place = $this->createPlaceWithTranslation('fr', 'centre-spatial-kennedy', 'Centre spatial Kennedy');

        $response = $this->get(route('places.show.fr', ['slug' => 'centre-spatial-kennedy']));

        $response->assertStatus(200);
        $response->assertSee('Centre spatial Kennedy');
    }

    public function test_place_show_page_redirects_with_error_for_non_existent_place(): void
    {
        $response = $this->get(route('places.show.fr', ['slug' => 'non-existent-place']));

        $response->assertRedirect(route('explore.fr'));
        $response->assertSessionHas('error');
    }

    public function test_place_show_page_redirects_when_translation_missing(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        // Créer uniquement traduction anglaise
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'slug' => 'test-place',
            'status' => 'published',
        ]);

        // Essayer d'accéder en français
        $response = $this->get(route('places.show.fr', ['slug' => 'test-place']));

        $response->assertRedirect(route('explore.fr'));
        $response->assertSessionHas('error');
    }

    public function test_place_show_page_displays_all_place_information(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Test']);

        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'admin_id' => $admin->id,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
            'description' => 'Description complète du centre spatial',
            'practical_info' => 'Horaires: 9h-17h, Tarif: 50€',
            'status' => 'published',
        ]);

        $response = $this->get(route('places.show.fr', ['slug' => 'centre-spatial-kennedy']));

        $response->assertStatus(200);
        $response->assertSee('Centre spatial Kennedy');
        $response->assertSee('Description complète du centre spatial');
        $response->assertSee('Horaires: 9h-17h, Tarif: 50€');
        $response->assertSee('Kennedy Space Center, FL 32899, USA');
    }

    public function test_place_show_page_displays_tags(): void
    {
        $place = $this->createPlaceWithTranslation('fr', 'test-place', 'Test Place');

        $tag = Tag::factory()->create(['color' => '#FF0000']);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);
        $place->tags()->attach($tag->id);

        $response = $this->get(route('places.show.fr', ['slug' => 'test-place']));

        $response->assertStatus(200);
        $response->assertSee('NASA');
    }

    public function test_place_show_page_displays_photos(): void
    {
        $place = $this->createPlaceWithTranslation('fr', 'test-place', 'Test Place');

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'filename' => 'photo-principale.jpg',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'filename' => 'photo-secondaire.jpg',
        ]);

        $response = $this->get(route('places.show.fr', ['slug' => 'test-place']));

        $response->assertStatus(200);
        $response->assertSee('photo-principale.jpg');
        $response->assertSee('photo-secondaire.jpg');
    }

    public function test_place_show_page_includes_seo_metadata(): void
    {
        $place = $this->createPlaceWithTranslation('fr', 'centre-spatial-kennedy', 'Centre spatial Kennedy');

        $response = $this->get(route('places.show.fr', ['slug' => 'centre-spatial-kennedy']));

        $response->assertStatus(200);

        // Vérifier les métadonnées SEO de base
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta name="description"', false);
    }

    public function test_place_show_page_respects_locale_in_url(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        // Créer traductions FR et EN
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'lieu-francais',
            'title' => 'Titre en français',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'slug' => 'english-place',
            'title' => 'Title in English',
            'status' => 'published',
        ]);

        $responseFr = $this->get(route('places.show.fr', ['slug' => 'lieu-francais']));
        $responseEn = $this->get(route('places.show.en', ['slug' => 'english-place']));

        $responseFr->assertStatus(200);
        $responseFr->assertSee('Titre en français');

        $responseEn->assertStatus(200);
        $responseEn->assertSee('Title in English');
    }

    public function test_place_show_page_displays_coordinates_for_map(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'admin_id' => $admin->id,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'test-place',
            'status' => 'published',
        ]);

        $response = $this->get(route('places.show.fr', ['slug' => 'test-place']));

        $response->assertStatus(200);
        // Vérifier que les coordonnées sont dans la réponse (pour le JavaScript de la carte)
        $response->assertSee('28.5728');
        $response->assertSee('-80.6490');
    }

    public function test_place_show_page_redirects_to_explore_on_place_not_found(): void
    {
        $response = $this->get(route('places.show.fr', ['slug' => 'non-existent']));

        $response->assertRedirect(route('explore.fr'));
    }

    public function test_place_show_page_redirects_to_explore_on_translation_not_found(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en', // Only English
            'slug' => 'test-place',
            'status' => 'published',
        ]);

        $response = $this->get(route('places.show.fr', ['slug' => 'test-place']));

        $response->assertRedirect(route('explore.fr'));
    }

    /**
     * Helper: Créer un lieu avec traduction
     */
    private function createPlaceWithTranslation(string $locale, string $slug, string $title): Place
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => $locale,
            'slug' => $slug,
            'title' => $title,
            'status' => 'published',
        ]);

        return $place;
    }
}
