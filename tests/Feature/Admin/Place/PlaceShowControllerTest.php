<?php

namespace Tests\Feature\Admin\Place;

use App\Models\Category;
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

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_can_view_place_detail_page(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertStatus(200);
        $response->assertViewIs('admin.place.detail.show');
        $response->assertViewHas('place');
    }

    public function test_displays_place_general_information(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL, USA',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertSee('28.5728');
        $response->assertSee('-80.6490');
        $response->assertSee('Kennedy Space Center, FL, USA');
    }

    public function test_displays_all_translations(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
            'description' => 'Description française',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
            'description' => 'English description',
            'status' => 'draft',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        // Vérifier que le composant Livewire des traductions est présent
        $response->assertSeeLivewire('admin.place.place-translation-viewer');

        // Vérifier que les tabs de langues sont présents (uppercase dans le HTML)
        $response->assertSee('fr', false);
        $response->assertSee('en', false);

        // Vérifier que la première traduction (FR) est affichée par défaut
        $response->assertSee('Centre Spatial Kennedy');
        $response->assertSee('Description française');

        // Vérifier les statuts sont visibles dans les tabs
        $response->assertSee('Publié');
        $response->assertSee('Brouillon');
    }

    public function test_displays_tags_with_translations(): void
    {
        $place = Place::factory()->create();

        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
        ]);

        $place->tags()->attach($tag);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertSee('NASA');
    }

    public function test_displays_categories(): void
    {
        $place = Place::factory()->create();

        $category = Category::factory()->create([
            'is_active' => true,
            'name' => 'Centre de lancement',
        ]);

        $place->categories()->attach($category);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertSee('Centre de lancement');
    }

    public function test_displays_photos_with_main_indicator(): void
    {
        $place = Place::factory()->create();

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'filename' => 'main-photo.jpg',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'filename' => 'photo-2.jpg',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertSee('main-photo.jpg');
        $response->assertSee('photo-2.jpg');
        $response->assertSee('Principale');
    }

    public function test_displays_photos_section_even_without_photos(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        // Aucune photo créée

        $response = $this->get(route('admin.places.show', $place));

        // Vérifier que la section Photos est affichée
        $response->assertSee('Photos');

        // Vérifier le message d'état vide
        $response->assertSee('Aucune photo enregistrée');
        $response->assertSee('Ajoutez des photos pour illustrer ce lieu');

        // Vérifier la présence du bouton CTA
        $response->assertSee('Ajouter une première photo');
    }

    public function test_shows_featured_badge_when_place_is_featured(): void
    {
        $place = Place::factory()->create(['is_featured' => true]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertSee("À l'affiche", false);
    }

    public function test_does_not_show_featured_badge_when_place_is_not_featured(): void
    {
        $place = Place::factory()->create(['is_featured' => false]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertDontSee("À l'affiche", false);
    }

    public function test_displays_admin_creator_information(): void
    {
        $creator = User::factory()->create(['name' => 'John Doe']);

        $place = Place::factory()->create(['admin_id' => $creator->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertSee('John Doe');
    }

    public function test_returns_404_when_place_not_found(): void
    {
        $response = $this->get(route('admin.places.show', 99999));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Ce lieu n\'existe pas');
    }

    public function test_guest_cannot_access_place_detail(): void
    {
        auth()->logout();

        $place = Place::factory()->create();

        $response = $this->get(route('admin.places.show', $place));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_passes_correct_data_to_view(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $tag = Tag::factory()->create();
        $place->tags()->attach($tag);

        $category = Category::factory()->create();
        $place->categories()->attach($category);

        Photo::factory()->count(3)->create(['place_id' => $place->id]);

        $response = $this->get(route('admin.places.show', $place));

        $response->assertViewHas('place');
        $response->assertViewHas('has_translations', true);
        $response->assertViewHas('translation_count', 1);
        $response->assertViewHas('has_tags', true);
        $response->assertViewHas('tag_count', 1);
        $response->assertViewHas('has_categories', true);
        $response->assertViewHas('category_count', 1);
        $response->assertViewHas('has_photos', true);
        $response->assertViewHas('photo_count', 3);
    }
}
