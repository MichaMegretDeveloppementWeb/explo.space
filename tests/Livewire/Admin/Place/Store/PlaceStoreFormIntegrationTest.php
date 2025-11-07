<?php

namespace Tests\Livewire\Admin\Place\Store;

use App\Livewire\Admin\Place\Store\PlaceStoreForm;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceStoreFormIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
        app()->setLocale('fr');

        Storage::fake('public');
    }

    // ========================================
    // Create Place Tests
    // ========================================

    public function test_save_creates_new_place_with_valid_data(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['is_active' => true]);

        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 28.5728)
            ->set('longitude', -80.6490)
            ->set('address', 'Kennedy Space Center, FL 32899, USA')
            ->set('is_featured', true)
            ->set('categoryIds', [$category->id])
            ->set('tagIds', [$tag->id])
            ->set('translations', [
                'fr' => [
                    'title' => 'Centre Spatial Kennedy',
                    'slug' => 'centre-spatial-kennedy',
                    'description' => 'Description complète du lieu',
                    'practical_info' => 'Informations pratiques',
                    'status' => 'published',
                ],
                'en' => [
                    'title' => 'Kennedy Space Center',
                    'slug' => 'kennedy-space-center',
                    'description' => 'Complete description of the place',
                    'practical_info' => 'Practical information',
                    'status' => 'published',
                ],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('places', [
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'is_featured' => true,
            'admin_id' => $this->admin->id,
        ]);

        $place = Place::where('latitude', 28.5728)->first();

        $this->assertDatabaseHas('place_translations', [
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
        ]);

        $this->assertCount(1, $place->categories);
        $this->assertCount(1, $place->tags);
    }

    // ========================================
    // Update Place Tests
    // ========================================

    public function test_save_updates_existing_place(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Old Address',
            'is_featured' => false,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Old EN Title',
            'slug' => 'old-en-title',
        ]);

        Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->set('latitude', 45.5017)
            ->set('longitude', -73.5673)
            ->set('address', 'New Address')
            ->set('is_featured', true)
            ->set('translations.fr.title', 'New Title')
            ->set('translations.fr.slug', 'new-title')
            ->set('translations.fr.description', 'New Description')
            ->set('translations.en.title', 'New EN Title')
            ->set('translations.en.slug', 'new-en-title')
            ->set('translations.en.description', 'New EN Description')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $place->refresh();

        $this->assertEquals(45.5017, $place->latitude);
        $this->assertEquals(-73.5673, $place->longitude);
        $this->assertEquals('New Address', $place->address);
        $this->assertTrue($place->is_featured);

        $translation = $place->translations->where('locale', 'fr')->first();
        $this->assertEquals('New Title', $translation->title);
        $this->assertEquals('new-title', $translation->slug);
    }

    // ========================================
    // Integration Tests
    // ========================================

    public function test_complete_create_workflow_with_photos_categories_and_tags(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['is_active' => true]);
        $photo = UploadedFile::fake()->image('test.jpg', 800, 600);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 28.5728)
            ->set('longitude', -80.6490)
            ->set('address', 'Kennedy Space Center')
            ->set('is_featured', true)
            ->set('categoryIds', [$category->id])
            ->set('tagIds', [$tag->id])
            ->set('translations', [
                'fr' => [
                    'title' => 'Centre Spatial Kennedy',
                    'slug' => 'centre-spatial-kennedy',
                    'description' => 'Description',
                    'practical_info' => 'Infos',
                    'status' => 'published',
                ],
                'en' => [
                    'title' => 'Kennedy Space Center',
                    'slug' => 'kennedy-space-center',
                    'description' => 'Description',
                    'practical_info' => 'Info',
                    'status' => 'published',
                ],
            ])
            ->set('pendingPhotos', [$photo]);

        // La validation automatique se déclenche via le lifecycle hook

        // Sauvegarder
        $component->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        // Vérifier la création
        $place = Place::where('latitude', 28.5728)->first();
        $this->assertNotNull($place);
        $this->assertTrue($place->is_featured);
        $this->assertCount(1, $place->categories);
        $this->assertCount(1, $place->tags);
        $this->assertCount(2, $place->translations);
        $this->assertCount(1, $place->photos);
    }

    public function test_complete_update_workflow_preserves_unchanged_data(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'is_featured' => false,
        ]);

        $category = Category::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['is_active' => true]);

        $place->categories()->attach($category->id);
        $place->tags()->attach($tag->id);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Original Title',
            'slug' => 'original-title',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Original EN Title',
            'slug' => 'original-en-title',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'sort_order' => 0,
        ]);

        Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->set('is_featured', true)  // Changement simple
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $place->refresh();

        // Vérifier que le changement est appliqué
        $this->assertTrue($place->is_featured);

        // Vérifier que les autres données sont préservées
        $this->assertEquals(28.5728, $place->latitude);
        $this->assertEquals(-80.6490, $place->longitude);
        $this->assertCount(1, $place->categories);
        $this->assertCount(1, $place->tags);
        $this->assertCount(2, $place->translations); // FR + EN
        $this->assertCount(1, $place->photos);
    }

    // ========================================
    // EditRequest Integration Tests
    // ========================================

    public function test_save_passes_selected_fields_to_service_when_edit_request_present(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['is_active' => true]);

        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'is_featured' => false,
        ]);

        $place->categories()->attach($category->id);
        $place->tags()->attach($tag->id);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Old Title',
            'slug' => 'old-title',
            'description' => 'Old Description',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Old EN Title',
            'slug' => 'old-en-title',
            'description' => 'Old EN Description',
        ]);

        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        // Simuler la modification d'un lieu avec EditRequest
        Livewire::test(PlaceStoreForm::class, [
            'placeId' => $place->id,
            'placeRequestId' => null,
            'editRequestId' => $editRequest->id,
        ])
            ->set('categoryIds', [$category->id])
            ->set('tagIds', [$tag->id])
            ->set('selectedFields', ['title', 'description'])
            ->set('selectedPhotos', [])
            ->set('translations.fr.title', 'New Title')
            ->set('translations.fr.description', 'New Description')
            ->set('translations.en.title', 'New EN Title')
            ->set('translations.en.description', 'New EN Description')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        // Vérifier que l'EditRequest a été accepté avec applied_changes
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertNotNull($editRequest->applied_changes);
        $this->assertIsArray($editRequest->applied_changes);
        $this->assertArrayHasKey('fields', $editRequest->applied_changes);
        $this->assertArrayHasKey('photos', $editRequest->applied_changes);
        $this->assertEquals(['title', 'description'], $editRequest->applied_changes['fields']);
        $this->assertEquals([], $editRequest->applied_changes['photos']);
    }

    public function test_save_passes_selected_photos_to_service_when_edit_request_present(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['is_active' => true]);

        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'is_featured' => false,
        ]);

        $place->categories()->attach($category->id);
        $place->tags()->attach($tag->id);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Title',
            'slug' => 'title',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'EN Title',
            'slug' => 'en-title',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'sort_order' => 0,
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'sort_order' => 1,
        ]);

        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'photo_suggestion',
            'status' => 'submitted',
        ]);

        // Simuler l'acceptation de photos suggérées
        Livewire::test(PlaceStoreForm::class, [
            'placeId' => $place->id,
            'placeRequestId' => null,
            'editRequestId' => $editRequest->id,
        ])
            ->set('categoryIds', [$category->id])
            ->set('tagIds', [$tag->id])
            ->set('selectedFields', [])
            ->set('selectedPhotos', [0, 1])  // Accepter les 2 photos
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        // Vérifier que l'EditRequest a été accepté avec applied_changes
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertNotNull($editRequest->applied_changes);
        $this->assertIsArray($editRequest->applied_changes);
        $this->assertEquals([], $editRequest->applied_changes['fields']);
        $this->assertEquals([0, 1], $editRequest->applied_changes['photos']);
    }

    public function test_save_passes_both_selected_fields_and_photos_to_service(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['is_active' => true]);

        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'is_featured' => false,
        ]);

        $place->categories()->attach($category->id);
        $place->tags()->attach($tag->id);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Old Title',
            'slug' => 'old-title',
            'description' => 'Old Description',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Old EN Title',
            'slug' => 'old-en-title',
            'description' => 'Old EN Description',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        // Simuler une modification mixte (champs + photos)
        Livewire::test(PlaceStoreForm::class, [
            'placeId' => $place->id,
            'placeRequestId' => null,
            'editRequestId' => $editRequest->id,
        ])
            ->set('categoryIds', [$category->id])
            ->set('tagIds', [$tag->id])
            ->set('selectedFields', ['title'])
            ->set('selectedPhotos', [0])
            ->set('translations.fr.title', 'New Title')
            ->set('translations.en.title', 'New EN Title')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        // Vérifier que l'EditRequest a été accepté avec les deux types de données
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertNotNull($editRequest->applied_changes);
        $this->assertEquals(['title'], $editRequest->applied_changes['fields']);
        $this->assertEquals([0], $editRequest->applied_changes['photos']);
    }
}
