<?php

namespace Tests\Feature\Admin\Place;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceDeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_delete_place(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $response = $this->delete(route('admin.places.destroy', $place->id));

        $response->assertRedirect(route('admin.places.index'));
        $response->assertSessionHas('success', 'Le lieu a été supprimé avec succès.');

        $this->assertDatabaseMissing('places', ['id' => $place->id]);
    }

    public function test_deleting_place_cascades_to_translations(): void
    {
        $place = Place::factory()->create();

        $translationFr = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $translationEn = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
        ]);

        $this->delete(route('admin.places.destroy', $place->id));

        $this->assertDatabaseMissing('place_translations', ['id' => $translationFr->id]);
        $this->assertDatabaseMissing('place_translations', ['id' => $translationEn->id]);
    }

    public function test_deleting_place_cascades_to_photos(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create(['place_id' => $place->id]);
        $photo2 = Photo::factory()->create(['place_id' => $place->id]);

        $this->delete(route('admin.places.destroy', $place->id));

        $this->assertDatabaseMissing('photos', ['id' => $photo1->id]);
        $this->assertDatabaseMissing('photos', ['id' => $photo2->id]);
    }

    public function test_deleting_place_detaches_tags(): void
    {
        $place = Place::factory()->create();
        $tag = Tag::factory()->create();
        $place->tags()->attach($tag);

        $this->assertDatabaseHas('place_tag', [
            'place_id' => $place->id,
            'tag_id' => $tag->id,
        ]);

        $this->delete(route('admin.places.destroy', $place->id));

        $this->assertDatabaseMissing('place_tag', [
            'place_id' => $place->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_deleting_place_detaches_categories(): void
    {
        $place = Place::factory()->create();
        $category = Category::factory()->create();
        $place->categories()->attach($category);

        $this->assertDatabaseHas('place_category', [
            'place_id' => $place->id,
            'category_id' => $category->id,
        ]);

        $this->delete(route('admin.places.destroy', $place->id));

        $this->assertDatabaseMissing('place_category', [
            'place_id' => $place->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_returns_error_when_place_not_found(): void
    {
        $response = $this->delete(route('admin.places.destroy', 99999));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_delete_place(): void
    {
        auth()->logout();

        $place = Place::factory()->create();

        $response = $this->delete(route('admin.places.destroy', $place->id));

        $response->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }

    public function test_delete_form_exists_in_place_detail_page(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $response = $this->get(route('admin.places.show', $place->id));

        $response->assertSee('delete-form-'.$place->id, false);
        $response->assertSee(route('admin.places.destroy', $place->id), false);
        $response->assertSee('Supprimer');
    }
}
