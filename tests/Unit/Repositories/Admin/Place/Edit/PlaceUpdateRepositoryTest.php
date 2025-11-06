<?php

namespace Tests\Unit\Repositories\Admin\Place\Edit;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\Admin\Place\Edit\PlaceUpdateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceUpdateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlaceUpdateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PlaceUpdateRepository;
    }

    // ========================================
    // Find For Edit Tests (using findForEdit method)
    // ========================================

    public function test_find_for_edit_loads_all_relations(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $tag = Tag::factory()->create(['is_active' => true]);
        $place->tags()->attach($tag);

        $category = Category::factory()->create(['is_active' => true]);
        $place->categories()->attach($category);

        Photo::factory()->create([
            'place_id' => $place->id,
            'sort_order' => 1,
        ]);

        $result = $this->repository->findForEdit($place->id);

        $this->assertTrue($result->relationLoaded('translations'));
        $this->assertTrue($result->relationLoaded('tags'));
        $this->assertTrue($result->relationLoaded('categories'));
        $this->assertTrue($result->relationLoaded('photos'));
    }

    public function test_find_for_edit_loads_photos_ordered_by_sort_order(): void
    {
        $place = Place::factory()->create();

        Photo::factory()->create([
            'place_id' => $place->id,
            'sort_order' => 3,
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'sort_order' => 1,
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'sort_order' => 2,
        ]);

        $result = $this->repository->findForEdit($place->id);

        $photos = $result->photos;
        $this->assertEquals(1, $photos[0]->sort_order);
        $this->assertEquals(2, $photos[1]->sort_order);
        $this->assertEquals(3, $photos[2]->sort_order);
    }

    public function test_find_for_edit_returns_null_when_not_found(): void
    {
        $result = $this->repository->findForEdit(99999);

        $this->assertNull($result);
    }

    // ========================================
    // Update Place Tests
    // ========================================

    public function test_update_place_updates_basic_data(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Old Address',
            'admin_id' => $admin->id,
            'is_featured' => false,
        ]);

        $newData = [
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'address' => 'New Address',
            'is_featured' => true,
        ];

        $this->repository->update($place, $newData);

        $place->refresh();

        $this->assertEquals($newData['latitude'], $place->latitude);
        $this->assertEquals($newData['longitude'], $place->longitude);
        $this->assertEquals($newData['address'], $place->address);
        $this->assertEquals($newData['is_featured'], $place->is_featured);
    }

    public function test_update_place_can_toggle_is_featured(): void
    {
        $place = Place::factory()->create(['is_featured' => true]);

        $this->repository->update($place, ['is_featured' => false]);

        $place->refresh();
        $this->assertFalse($place->is_featured);
    }

    // ========================================
    // Update Translations Tests
    // ========================================

    public function test_update_translations_updates_existing_translation(): void
    {
        $place = Place::factory()->create();
        $translation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        $newTranslations = [
            'fr' => [
                'title' => 'New Title',
                'slug' => 'new-title',
                'description' => 'Updated description',
                'practical_info' => 'Updated info',
                'status' => 'published',
            ],
        ];

        $this->repository->updateTranslations($place, $newTranslations);

        $translation->refresh();
        $this->assertEquals('New Title', $translation->title);
        $this->assertEquals('new-title', $translation->slug);
    }

    public function test_update_translations_creates_new_translation(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $newTranslations = [
            'fr' => [
                'title' => 'Titre FR',
                'slug' => 'titre-fr',
                'description' => 'Description FR',
                'practical_info' => 'Info FR',
                'status' => 'published',
            ],
            'en' => [
                'title' => 'Title EN',
                'slug' => 'title-en',
                'description' => 'Description EN',
                'practical_info' => 'Info EN',
                'status' => 'published',
            ],
        ];

        $this->repository->updateTranslations($place, $newTranslations);

        $place->refresh();
        $this->assertCount(2, $place->translations);
        $this->assertNotNull($place->translations->where('locale', 'en')->first());
    }

    public function test_update_translations_preserves_untouched_translations(): void
    {
        // Note: updateTranslations uses updateOrCreate which doesn't delete missing translations
        // This is the expected behavior - translations are preserved unless explicitly deleted
        $place = Place::factory()->create();

        $frTranslation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre FR',
        ]);

        $enTranslation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Title EN',
        ]);

        // Only update FR translation (EN should be preserved)
        $newTranslations = [
            'fr' => [
                'title' => 'Titre FR Modifié',
                'slug' => 'titre-fr-modifie',
                'description' => 'Description FR',
                'practical_info' => 'Info FR',
                'status' => 'published',
            ],
        ];

        $this->repository->updateTranslations($place, $newTranslations);

        $place->refresh();
        $this->assertCount(2, $place->translations);
        $frTranslation->refresh();
        $enTranslation->refresh();
        $this->assertEquals('Titre FR Modifié', $frTranslation->title);
        $this->assertEquals('Title EN', $enTranslation->title); // Preserved
    }

    // ========================================
    // Sync Categories Tests
    // ========================================

    public function test_sync_categories_adds_new_categories(): void
    {
        $place = Place::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $this->repository->syncCategories($place, [$category1->id, $category2->id]);

        $this->assertCount(2, $place->categories);
    }

    public function test_sync_categories_removes_old_categories(): void
    {
        $place = Place::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $place->categories()->attach([$category1->id, $category2->id]);

        // Only keep category1
        $this->repository->syncCategories($place, [$category1->id]);

        $place->refresh();
        $this->assertCount(1, $place->categories);
        $this->assertEquals($category1->id, $place->categories->first()->id);
    }

    public function test_sync_categories_handles_empty_array(): void
    {
        $place = Place::factory()->create();
        $category = Category::factory()->create();

        $place->categories()->attach($category);

        $this->repository->syncCategories($place, []);

        $place->refresh();
        $this->assertCount(0, $place->categories);
    }

    // ========================================
    // Sync Tags Tests
    // ========================================

    public function test_sync_tags_adds_new_tags(): void
    {
        $place = Place::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $this->repository->syncTags($place, [$tag1->id, $tag2->id]);

        $this->assertCount(2, $place->tags);
    }

    public function test_sync_tags_removes_old_tags(): void
    {
        $place = Place::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $place->tags()->attach([$tag1->id, $tag2->id]);

        // Only keep tag1
        $this->repository->syncTags($place, [$tag1->id]);

        $place->refresh();
        $this->assertCount(1, $place->tags);
        $this->assertEquals($tag1->id, $place->tags->first()->id);
    }

    public function test_sync_tags_handles_empty_array(): void
    {
        $place = Place::factory()->create();
        $tag = Tag::factory()->create();

        $place->tags()->attach($tag);

        $this->repository->syncTags($place, []);

        $place->refresh();
        $this->assertCount(0, $place->tags);
    }

    // ========================================
    // Photo Management Tests
    // ========================================
    // Note: syncPhotos() method doesn't exist in the current architecture.
    // Photo management is done through separate methods:
    // - addPhotos() for adding new photos
    // - updatePhotoOrder() for reordering
    // - setMainPhoto() for changing main photo
    // - deletePhoto() for deletion
    // These individual methods are tested through the Service layer tests.
}
