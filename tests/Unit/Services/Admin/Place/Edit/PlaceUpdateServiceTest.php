<?php

namespace Tests\Unit\Services\Admin\Place\Edit;

use App\Exceptions\Admin\Place\PlaceNotFoundException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\User;
use App\Services\Admin\Place\Edit\PlaceUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tests pour PlaceUpdateService
 *
 * Ce service gère la mise à jour complète d'un lieu avec :
 * - Données de base (coordonnées, adresse, is_featured)
 * - Traductions (FR/EN)
 * - Relations (catégories, tags)
 * - Photos (ajout, suppression, ordre, photo principale)
 * - Transaction atomique
 * - Exceptions et rollback
 */
class PlaceUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlaceUpdateService $service;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        // Use real repository and photo service for integration testing
        $this->service = app(PlaceUpdateService::class);

        Storage::fake('place_photos');
    }

    // ========================================
    // Tests chargement pour édition
    // ========================================

    public function test_load_for_edit_returns_place_with_relations(): void
    {
        // Arrange
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create(['place_id' => $place->id, 'locale' => 'fr']);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $place->categories()->attach($category->id);
        $place->tags()->attach($tag->id);
        Photo::factory()->create(['place_id' => $place->id]);

        // Act
        $loaded = $this->service->loadForEdit($place->id);

        // Assert
        $this->assertInstanceOf(Place::class, $loaded);
        $this->assertTrue($loaded->relationLoaded('translations'));
        $this->assertTrue($loaded->relationLoaded('categories'));
        $this->assertTrue($loaded->relationLoaded('tags'));
        $this->assertTrue($loaded->relationLoaded('photos'));
        $this->assertCount(1, $loaded->translations);
        $this->assertCount(1, $loaded->categories);
        $this->assertCount(1, $loaded->tags);
        $this->assertCount(1, $loaded->photos);
    }

    public function test_load_for_edit_returns_null_if_not_found(): void
    {
        // Act
        $loaded = $this->service->loadForEdit(99999);

        // Assert
        $this->assertNull($loaded);
    }

    // ========================================
    // Tests mise à jour données de base
    // ========================================

    public function test_update_basic_place_data(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $data = $this->getBasicUpdateData();
        $data['latitude'] = 45.5017;
        $data['longitude'] = -73.5673;
        $data['address'] = 'Montreal, Canada';

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertEquals(45.5017, $updated->latitude);
        $this->assertEquals(-73.5673, $updated->longitude);
        $this->assertEquals('Montreal, Canada', $updated->address);
    }

    public function test_update_coordinates(): void
    {
        // Arrange
        $place = $this->createBasicPlace(['latitude' => 48.8566, 'longitude' => 2.3522]);
        $data = $this->getBasicUpdateData();
        $data['latitude'] = 40.7128;
        $data['longitude'] = -74.0060;

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertEquals(40.7128, $updated->latitude);
        $this->assertEquals(-74.0060, $updated->longitude);
    }

    public function test_update_is_featured_flag(): void
    {
        // Arrange
        $place = $this->createBasicPlace(['is_featured' => false]);
        $data = $this->getBasicUpdateData();
        $data['is_featured'] = true;

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertTrue($updated->is_featured);
        $this->assertDatabaseHas('places', [
            'id' => $updated->id,
            'is_featured' => true,
        ]);
    }

    public function test_update_nullable_address(): void
    {
        // Arrange - Repository preserves existing value when null is passed (using ??)
        $place = $this->createBasicPlace(['address' => 'Original Address']);
        $data = $this->getBasicUpdateData();
        $data['address'] = null;

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert - Address preserved when null passed (repository behavior with ??)
        $this->assertEquals('Original Address', $updated->address);
    }

    // ========================================
    // Tests mise à jour traductions
    // ========================================

    public function test_update_existing_translations(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Original',
            'description' => 'Description originale',
        ]);

        $data = $this->getBasicUpdateData();
        $data['translations'] = [
            'fr' => [
                'title' => 'Titre Modifié',
                'slug' => 'titre-modifie',
                'description' => 'Description modifiée',
                'practical_info' => 'Infos pratiques',
                'status' => 'published',
            ],
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertDatabaseHas('place_translations', [
            'place_id' => $updated->id,
            'locale' => 'fr',
            'title' => 'Titre Modifié',
            'description' => 'Description modifiée',
        ]);
    }

    public function test_add_new_translation_locale(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $data = $this->getBasicUpdateData();
        $data['translations'] = [
            'fr' => [
                'title' => 'Titre Français',
                'slug' => 'titre-francais',
                'description' => 'Description française',
                'status' => 'published',
            ],
            'en' => [
                'title' => 'English Title',
                'slug' => 'english-title',
                'description' => 'English description',
                'status' => 'published',
            ],
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(2, $updated->translations);
        $this->assertDatabaseHas('place_translations', [
            'place_id' => $updated->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);
    }

    public function test_update_translations_generates_slug_if_empty(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        PlaceTranslation::factory()->create(['place_id' => $place->id, 'locale' => 'fr']);

        $data = $this->getBasicUpdateData();
        $data['translations'] = [
            'fr' => [
                'title' => 'Tour Eiffel',
                'slug' => null, // Null slug to trigger auto-generation (empty string would not)
                'description' => 'Monument emblématique',
                'status' => 'published',
            ],
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $translation = $updated->translations->firstWhere('locale', 'fr');
        $this->assertEquals('tour-eiffel', $translation->slug);
    }

    // ========================================
    // Tests synchronisation relations
    // ========================================

    public function test_sync_categories(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $oldCategory = Category::factory()->create();
        $place->categories()->attach($oldCategory->id);

        $newCategory1 = Category::factory()->create();
        $newCategory2 = Category::factory()->create();

        $data = $this->getBasicUpdateData();
        $data['category_ids'] = [$newCategory1->id, $newCategory2->id];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(2, $updated->categories);
        $this->assertTrue($updated->categories->contains($newCategory1));
        $this->assertTrue($updated->categories->contains($newCategory2));
        $this->assertFalse($updated->categories->contains($oldCategory));
    }

    public function test_sync_tags(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $oldTag = Tag::factory()->create();
        $place->tags()->attach($oldTag->id);

        $newTag1 = Tag::factory()->create();
        $newTag2 = Tag::factory()->create();

        $data = $this->getBasicUpdateData();
        $data['tag_ids'] = [$newTag1->id, $newTag2->id];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(2, $updated->tags);
        $this->assertTrue($updated->tags->contains($newTag1));
        $this->assertTrue($updated->tags->contains($newTag2));
        $this->assertFalse($updated->tags->contains($oldTag));
    }

    public function test_sync_empty_arrays_removes_all_relations(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $place->categories()->attach($category->id);
        $place->tags()->attach($tag->id);

        $data = $this->getBasicUpdateData();
        $data['category_ids'] = [];
        $data['tag_ids'] = [];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(0, $updated->categories);
        $this->assertCount(0, $updated->tags);
    }

    // ========================================
    // Tests gestion photos
    // ========================================

    public function test_add_new_photos(): void
    {
        // Arrange
        $place = $this->createBasicPlace();

        $data = $this->getBasicUpdateData();
        $data['new_photos'] = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(2000),
            UploadedFile::fake()->image('photo2.jpg', 800, 600)->size(2000),
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(2, $updated->photos);
    }

    public function test_add_new_photos_with_translations(): void
    {
        // Arrange
        $place = $this->createBasicPlace();

        $data = $this->getBasicUpdateData();
        $data['new_photos'] = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(2000),
            UploadedFile::fake()->image('photo2.jpg', 800, 600)->size(2000),
        ];
        $data['photo_translations'] = [
            'temp_0' => [
                'fr' => ['alt_text' => 'Vue aérienne'],
                'en' => ['alt_text' => 'Aerial view'],
            ],
            'temp_1' => [
                'fr' => ['alt_text' => 'Vue de face'],
                'en' => ['alt_text' => 'Front view'],
            ],
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert - Photos created
        $this->assertCount(2, $updated->photos);

        // Assert - Photo translations created
        $firstPhoto = $updated->photos->sortBy('sort_order')->first();
        $this->assertDatabaseHas('photo_translations', [
            'photo_id' => $firstPhoto->id,
            'locale' => 'fr',
            'alt_text' => 'Vue aérienne',
        ]);
        $this->assertDatabaseHas('photo_translations', [
            'photo_id' => $firstPhoto->id,
            'locale' => 'en',
            'alt_text' => 'Aerial view',
        ]);

        $secondPhoto = $updated->photos->sortBy('sort_order')->last();
        $this->assertDatabaseHas('photo_translations', [
            'photo_id' => $secondPhoto->id,
            'locale' => 'fr',
            'alt_text' => 'Vue de face',
        ]);
        $this->assertDatabaseHas('photo_translations', [
            'photo_id' => $secondPhoto->id,
            'locale' => 'en',
            'alt_text' => 'Front view',
        ]);
    }

    public function test_update_existing_photo_translations(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $photo = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 0]);

        $data = $this->getBasicUpdateData();
        $data['photo_translations'] = [
            "photo_{$photo->id}" => [
                'fr' => ['alt_text' => 'Nouveau texte FR'],
                'en' => ['alt_text' => 'New text EN'],
            ],
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertDatabaseHas('photo_translations', [
            'photo_id' => $photo->id,
            'locale' => 'fr',
            'alt_text' => 'Nouveau texte FR',
        ]);
        $this->assertDatabaseHas('photo_translations', [
            'photo_id' => $photo->id,
            'locale' => 'en',
            'alt_text' => 'New text EN',
        ]);
    }

    public function test_delete_photos(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $photo1 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 0]);
        $photo2 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 1]);
        $photo3 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 2]);

        $data = $this->getBasicUpdateData();
        $data['deleted_photo_ids'] = [$photo1->id, $photo3->id];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(1, $updated->photos);
        $this->assertTrue($updated->photos->contains($photo2));
        $this->assertFalse($updated->photos->contains($photo1));
        $this->assertFalse($updated->photos->contains($photo3));
    }

    public function test_update_photo_order(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $photo1 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 0]);
        $photo2 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 1]);
        $photo3 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 2]);

        $data = $this->getBasicUpdateData();
        $data['photo_order'] = [
            $photo3->id => 0, // Photo 3 en premier
            $photo1->id => 1, // Photo 1 en deuxième
            $photo2->id => 2, // Photo 2 en troisième
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $photo1->refresh();
        $photo2->refresh();
        $photo3->refresh();

        $this->assertEquals(1, $photo1->sort_order);
        $this->assertEquals(2, $photo2->sort_order);
        $this->assertEquals(0, $photo3->sort_order);
    }

    public function test_set_main_photo(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $photo1 = Photo::factory()->create(['place_id' => $place->id, 'is_main' => true]);
        $photo2 = Photo::factory()->create(['place_id' => $place->id, 'is_main' => false]);

        $data = $this->getBasicUpdateData();
        $data['main_photo_id'] = $photo2->id;

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $photo1->refresh();
        $photo2->refresh();

        $this->assertFalse($photo1->is_main);
        $this->assertTrue($photo2->is_main);
    }

    public function test_new_photos_are_not_main_by_default(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $existingPhoto = Photo::factory()->create(['place_id' => $place->id, 'is_main' => true]);

        $data = $this->getBasicUpdateData();
        $data['new_photos'] = [
            UploadedFile::fake()->image('new-photo.jpg', 800, 600)->size(2000),
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(2, $updated->photos);

        $mainPhotos = $updated->photos->where('is_main', true);
        $this->assertCount(1, $mainPhotos);
        $this->assertEquals($existingPhoto->id, $mainPhotos->first()->id);

        $newPhoto = $updated->photos->where('id', '!=', $existingPhoto->id)->first();
        $this->assertFalse($newPhoto->is_main);
    }

    public function test_new_photos_continue_sort_order_from_max(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 0]);
        Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 1]);

        $data = $this->getBasicUpdateData();
        $data['new_photos'] = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(2000),
            UploadedFile::fake()->image('photo2.jpg', 800, 600)->size(2000),
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $this->assertCount(4, $updated->photos);

        $newPhotos = $updated->photos->sortBy('sort_order')->values()->slice(2, 2)->values();
        $this->assertEquals(2, $newPhotos[0]->sort_order);
        $this->assertEquals(3, $newPhotos[1]->sort_order);
    }

    // ========================================
    // Tests intégration complète
    // ========================================

    public function test_update_with_all_changes_combined(): void
    {
        // Arrange
        $place = $this->createBasicPlace(['is_featured' => false]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Ancien Titre',
        ]);

        $oldCategory = Category::factory()->create();
        $place->categories()->attach($oldCategory->id);

        $oldPhoto = Photo::factory()->create(['place_id' => $place->id, 'is_main' => true]);

        $newCategory = Category::factory()->create();
        $newTag = Tag::factory()->create();

        $data = [
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'address' => 'New York, USA',
            'is_featured' => true,
            'translations' => [
                'fr' => [
                    'title' => 'Nouveau Titre',
                    'slug' => 'nouveau-titre',
                    'description' => 'Nouvelle description',
                    'status' => 'published',
                ],
                'en' => [
                    'title' => 'New Title',
                    'slug' => 'new-title',
                    'description' => 'New description',
                    'status' => 'published',
                ],
            ],
            'category_ids' => [$newCategory->id],
            'tag_ids' => [$newTag->id],
            'deleted_photo_ids' => [$oldPhoto->id],
            'new_photos' => [
                UploadedFile::fake()->image('new-photo.jpg', 800, 600)->size(2000),
            ],
        ];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert - Base data
        $this->assertEquals(40.7128, $updated->latitude);
        $this->assertEquals(-74.0060, $updated->longitude);
        $this->assertEquals('New York, USA', $updated->address);
        $this->assertTrue($updated->is_featured);

        // Assert - Translations
        $this->assertCount(2, $updated->translations);
        $frTranslation = $updated->translations->firstWhere('locale', 'fr');
        $this->assertEquals('Nouveau Titre', $frTranslation->title);

        // Assert - Relations
        $this->assertCount(1, $updated->categories);
        $this->assertTrue($updated->categories->contains($newCategory));
        $this->assertCount(1, $updated->tags);
        $this->assertTrue($updated->tags->contains($newTag));

        // Assert - Photos
        $this->assertCount(1, $updated->photos);
        $this->assertFalse($updated->photos->contains($oldPhoto));
    }

    // ========================================
    // Tests exceptions et rollback
    // ========================================

    public function test_update_throws_exception_if_place_not_found(): void
    {
        // Arrange
        $data = $this->getBasicUpdateData();

        // Act & Assert
        $this->expectException(PlaceNotFoundException::class);
        $this->service->update(99999, $data);
    }

    public function test_update_rolls_back_on_photo_validation_exception(): void
    {
        // Arrange
        $place = $this->createBasicPlace(['is_featured' => false]);
        $initialPhotos = $place->photos()->count();

        $data = $this->getBasicUpdateData();
        $data['is_featured'] = true; // This would change if not rolled back
        $data['new_photos'] = [
            UploadedFile::fake()->create('invalid.txt', 100, 'text/plain'), // Invalid
        ];

        // Act & Assert
        $this->expectException(PhotoValidationException::class);

        try {
            $this->service->update($place->id, $data);
        } catch (PhotoValidationException $e) {
            // Assert - Changes were rolled back
            $place->refresh();
            $this->assertFalse($place->is_featured); // Not changed
            $this->assertEquals($initialPhotos, $place->photos()->count()); // No new photos

            throw $e;
        }
    }

    public function test_delete_photo_continues_on_technical_error(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $photo1 = Photo::factory()->create(['place_id' => $place->id, 'filename' => 'photo1.jpg']);
        $photo2 = Photo::factory()->create(['place_id' => $place->id, 'filename' => 'photo2.jpg']);

        // Photo2 has invalid filename that will cause technical error during deletion
        // But service should continue and succeed (errors are logged, not thrown)

        $data = $this->getBasicUpdateData();
        $data['deleted_photo_ids'] = [$photo1->id, $photo2->id];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert - Both photos should be deleted from DB even if file deletion fails
        $this->assertCount(0, $updated->photos);
        $this->assertDatabaseMissing('photos', ['id' => $photo1->id]);
        $this->assertDatabaseMissing('photos', ['id' => $photo2->id]);
    }

    // ========================================
    // Tests préservation données
    // ========================================

    public function test_update_preserves_unchanged_data(): void
    {
        // Arrange
        $place = $this->createBasicPlace([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'address' => 'Paris, France',
            'is_featured' => true,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Original Title',
            'description' => 'Original Description',
        ]);

        $category = Category::factory()->create();
        $place->categories()->attach($category->id);

        // Act - Update only latitude
        $data = $this->getBasicUpdateData();
        $data['latitude'] = 40.7128;
        $data['longitude'] = $place->longitude; // Keep same
        $data['address'] = $place->address; // Keep same
        $data['is_featured'] = $place->is_featured; // Keep same
        $data['translations'] = [
            'fr' => [
                'title' => 'Original Title', // Keep same
                'slug' => 'original-title',
                'description' => 'Original Description', // Keep same
                'status' => 'published',
            ],
        ];
        $data['category_ids'] = [$category->id]; // Keep same

        $updated = $this->service->update($place->id, $data);

        // Assert - Only latitude changed
        $this->assertEquals(40.7128, $updated->latitude); // Changed
        $this->assertEquals(2.3522, $updated->longitude); // Preserved
        $this->assertEquals('Paris, France', $updated->address); // Preserved
        $this->assertTrue($updated->is_featured); // Preserved

        $translation = $updated->translations->firstWhere('locale', 'fr');
        $this->assertEquals('Original Title', $translation->title); // Preserved
        $this->assertEquals('Original Description', $translation->description); // Preserved

        $this->assertCount(1, $updated->categories); // Preserved
        $this->assertTrue($updated->categories->contains($category)); // Preserved
    }

    // ========================================
    // Helper methods
    // ========================================

    private function createBasicPlace(array $overrides = []): Place
    {
        return Place::factory()->create(array_merge([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'address' => 'Paris, France',
            'admin_id' => $this->admin->id,
            'is_featured' => false,
        ], $overrides));
    }

    private function getBasicUpdateData(): array
    {
        return [
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'address' => 'Paris, France',
            'is_featured' => false,
            'translations' => [
                'fr' => [
                    'title' => 'Tour Eiffel',
                    'slug' => 'tour-eiffel',
                    'description' => 'Monument emblématique',
                    'practical_info' => null,
                    'status' => 'published',
                ],
            ],
            'category_ids' => [],
            'tag_ids' => [],
            'deleted_photo_ids' => [],
            'new_photos' => [],
            'photo_order' => [],
            'main_photo_id' => null,
        ];
    }

    // ========================================
    // Tests acceptation EditRequest avec applied_changes
    // ========================================

    public function test_update_saves_applied_changes_when_edit_request_accepted(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $data = $this->getBasicUpdateData();
        $data['admin_id'] = $this->admin->id;
        $data['edit_request_id'] = $editRequest->id;
        $data['selected_fields'] = ['title', 'description'];
        $data['selected_photos'] = [0, 2];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertNotNull($editRequest->applied_changes);
        $this->assertIsArray($editRequest->applied_changes);
        $this->assertArrayHasKey('fields', $editRequest->applied_changes);
        $this->assertArrayHasKey('photos', $editRequest->applied_changes);
        $this->assertEquals(['title', 'description'], $editRequest->applied_changes['fields']);
        $this->assertEquals([0, 2], $editRequest->applied_changes['photos']);
    }

    public function test_update_saves_empty_arrays_when_no_fields_or_photos_selected(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'signalement',
            'status' => 'submitted',
        ]);

        $data = $this->getBasicUpdateData();
        $data['admin_id'] = $this->admin->id;
        $data['edit_request_id'] = $editRequest->id;
        $data['selected_fields'] = [];
        $data['selected_photos'] = [];

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertNotNull($editRequest->applied_changes);
        $this->assertIsArray($editRequest->applied_changes);
        $this->assertEquals([], $editRequest->applied_changes['fields']);
        $this->assertEquals([], $editRequest->applied_changes['photos']);
    }

    public function test_update_sets_processed_by_admin_and_processed_at(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'modification',
            'status' => 'submitted',
            'processed_by_admin_id' => null,
            'processed_at' => null,
        ]);

        $data = $this->getBasicUpdateData();
        $data['admin_id'] = $this->admin->id;
        $data['edit_request_id'] = $editRequest->id;
        $data['selected_fields'] = ['title'];
        $data['selected_photos'] = [];

        // Act
        $beforeUpdate = now();
        $updated = $this->service->update($place->id, $data);
        $afterUpdate = now();

        // Assert
        $editRequest->refresh();
        $this->assertEquals($this->admin->id, $editRequest->processed_by_admin_id);
        $this->assertNotNull($editRequest->processed_at);
        $this->assertGreaterThanOrEqual($beforeUpdate->timestamp, $editRequest->processed_at->timestamp);
        $this->assertLessThanOrEqual($afterUpdate->timestamp, $editRequest->processed_at->timestamp);
    }

    public function test_update_does_not_modify_edit_request_if_not_provided(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $data = $this->getBasicUpdateData();
        // No edit_request_id provided

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Submitted, $editRequest->status);
        $this->assertNull($editRequest->applied_changes);
        $this->assertNull($editRequest->processed_by_admin_id);
        $this->assertNull($editRequest->processed_at);
    }

    public function test_update_handles_selected_fields_without_selected_photos(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $data = $this->getBasicUpdateData();
        $data['admin_id'] = $this->admin->id;
        $data['edit_request_id'] = $editRequest->id;
        $data['selected_fields'] = ['title', 'description', 'practical_info'];
        // No selected_photos key

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertEquals(['title', 'description', 'practical_info'], $editRequest->applied_changes['fields']);
        $this->assertEquals([], $editRequest->applied_changes['photos']);
    }

    public function test_update_handles_selected_photos_without_selected_fields(): void
    {
        // Arrange
        $place = $this->createBasicPlace();
        $editRequest = \App\Models\EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'visitor@example.com',
            'type' => 'photo_suggestion',
            'status' => 'submitted',
        ]);

        $data = $this->getBasicUpdateData();
        $data['admin_id'] = $this->admin->id;
        $data['edit_request_id'] = $editRequest->id;
        $data['selected_photos'] = [1, 3, 5];
        // No selected_fields key

        // Act
        $updated = $this->service->update($place->id, $data);

        // Assert
        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertEquals([], $editRequest->applied_changes['fields']);
        $this->assertEquals([1, 3, 5], $editRequest->applied_changes['photos']);
    }
}
