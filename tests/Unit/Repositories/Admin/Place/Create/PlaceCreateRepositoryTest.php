<?php

namespace Tests\Unit\Repositories\Admin\Place\Create;

use App\Models\Category;
use App\Models\Place;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\Admin\Place\Create\PlaceCreateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceCreateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlaceCreateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PlaceCreateRepository;
    }

    // ========================================
    // Create Place Tests
    // ========================================

    public function test_create_place_stores_basic_data(): void
    {
        $admin = User::factory()->create();

        $data = [
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'admin_id' => $admin->id,
            'is_featured' => false,
        ];

        $place = $this->repository->create($data);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($data['latitude'], $place->latitude);
        $this->assertEquals($data['longitude'], $place->longitude);
        $this->assertEquals($data['address'], $place->address);
        $this->assertEquals($data['admin_id'], $place->admin_id);
        $this->assertEquals($data['is_featured'], $place->is_featured);
    }

    public function test_create_place_can_set_is_featured_true(): void
    {
        $admin = User::factory()->create();

        $data = [
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'admin_id' => $admin->id,
            'is_featured' => true,
        ];

        $place = $this->repository->create($data);

        $this->assertTrue($place->is_featured);
    }

    public function test_create_place_defaults_is_featured_to_false(): void
    {
        $admin = User::factory()->create();

        $data = [
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'admin_id' => $admin->id,
        ];

        $place = $this->repository->create($data);

        $this->assertFalse($place->is_featured);
    }

    // ========================================
    // Create Translations Tests
    // ========================================

    public function test_create_translations_creates_single_translation(): void
    {
        $place = Place::factory()->create();

        $translations = [
            'fr' => [
                'title' => 'Centre Spatial Kennedy',
                'slug' => 'centre-spatial-kennedy',
                'description' => 'Le Centre spatial Kennedy est le principal site de lancement de la NASA.',
                'practical_info' => 'Ouvert tous les jours de 9h à 18h',
                'status' => 'published',
            ],
        ];

        $this->repository->createTranslations($place, $translations);

        $this->assertCount(1, $place->translations);
        $translation = $place->translations->first();
        $this->assertEquals('fr', $translation->locale);
        $this->assertEquals($translations['fr']['title'], $translation->title);
        $this->assertEquals($translations['fr']['slug'], $translation->slug);
        $this->assertEquals($translations['fr']['description'], $translation->description);
        $this->assertEquals($translations['fr']['practical_info'], $translation->practical_info);
        $this->assertEquals($translations['fr']['status'], $translation->status);
    }

    public function test_create_translations_creates_multiple_translations(): void
    {
        $place = Place::factory()->create();

        $translations = [
            'fr' => [
                'title' => 'Centre Spatial Kennedy',
                'slug' => 'centre-spatial-kennedy',
                'description' => 'Description FR',
                'practical_info' => 'Infos FR',
                'status' => 'published',
            ],
            'en' => [
                'title' => 'Kennedy Space Center',
                'slug' => 'kennedy-space-center',
                'description' => 'Description EN',
                'practical_info' => 'Infos EN',
                'status' => 'published',
            ],
        ];

        $this->repository->createTranslations($place, $translations);

        $this->assertCount(2, $place->translations);

        $frTranslation = $place->translations->where('locale', 'fr')->first();
        $this->assertEquals($translations['fr']['title'], $frTranslation->title);

        $enTranslation = $place->translations->where('locale', 'en')->first();
        $this->assertEquals($translations['en']['title'], $enTranslation->title);
    }

    public function test_create_translations_handles_draft_status(): void
    {
        $place = Place::factory()->create();

        $translations = [
            'fr' => [
                'title' => 'Centre Spatial Kennedy',
                'slug' => 'centre-spatial-kennedy',
                'description' => 'Description',
                'practical_info' => 'Infos',
                'status' => 'draft',
            ],
        ];

        $this->repository->createTranslations($place, $translations);

        $translation = $place->translations->first();
        $this->assertEquals('draft', $translation->status);
    }

    // ========================================
    // Attach Categories Tests
    // ========================================

    public function test_attach_categories_attaches_single_category(): void
    {
        $place = Place::factory()->create();
        $category = Category::factory()->create();

        $this->repository->attachCategories($place, [$category->id]);

        $this->assertCount(1, $place->categories);
        $this->assertEquals($category->id, $place->categories->first()->id);
    }

    public function test_attach_categories_attaches_multiple_categories(): void
    {
        $place = Place::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $this->repository->attachCategories($place, [$category1->id, $category2->id]);

        $this->assertCount(2, $place->categories);
        $this->assertTrue($place->categories->contains($category1));
        $this->assertTrue($place->categories->contains($category2));
    }

    public function test_attach_categories_handles_empty_array(): void
    {
        $place = Place::factory()->create();

        $this->repository->attachCategories($place, []);

        $this->assertCount(0, $place->categories);
    }

    // ========================================
    // Attach Tags Tests
    // ========================================

    public function test_attach_tags_attaches_single_tag(): void
    {
        $place = Place::factory()->create();
        $tag = Tag::factory()->create();

        $this->repository->attachTags($place, [$tag->id]);

        $this->assertCount(1, $place->tags);
        $this->assertEquals($tag->id, $place->tags->first()->id);
    }

    public function test_attach_tags_attaches_multiple_tags(): void
    {
        $place = Place::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $this->repository->attachTags($place, [$tag1->id, $tag2->id]);

        $this->assertCount(2, $place->tags);
        $this->assertTrue($place->tags->contains($tag1));
        $this->assertTrue($place->tags->contains($tag2));
    }

    public function test_attach_tags_handles_empty_array(): void
    {
        $place = Place::factory()->create();

        $this->repository->attachTags($place, []);

        $this->assertCount(0, $place->tags);
    }

    // ========================================
    // Create Photos Tests
    // ========================================

    public function test_create_photos_creates_single_photo(): void
    {
        $place = Place::factory()->create();

        $photos = [
            [
                'filename' => 'place-1.jpg',
                'original_name' => 'Place 1.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024000,
                'alt_text' => 'Photo du lieu',
                'is_main' => true,
                'sort_order' => 1,
            ],
        ];

        $this->repository->createPhotos($place, $photos);

        $this->assertCount(1, $place->photos);
        $photo = $place->photos->first();
        $this->assertEquals('place-1.jpg', $photo->filename);
        $this->assertEquals('Place 1.jpg', $photo->original_name);
        $this->assertEquals('image/jpeg', $photo->mime_type);
        $this->assertEquals(1024000, $photo->size);
        $this->assertEquals('Photo du lieu', $photo->alt_text);
        $this->assertTrue($photo->is_main);
        $this->assertEquals(1, $photo->sort_order);
    }

    public function test_create_photos_creates_multiple_photos(): void
    {
        $place = Place::factory()->create();

        $photos = [
            [
                'filename' => 'place-1.jpg',
                'original_name' => 'Place 1.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024000,
                'alt_text' => 'Photo 1',
                'is_main' => true,
                'sort_order' => 1,
            ],
            [
                'filename' => 'place-2.jpg',
                'original_name' => 'Place 2.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 2048000,
                'alt_text' => 'Photo 2',
                'is_main' => false,
                'sort_order' => 2,
            ],
        ];

        $this->repository->createPhotos($place, $photos);

        $this->assertCount(2, $place->photos);

        $mainPhoto = $place->photos->where('is_main', true)->first();
        $this->assertEquals('place-1.jpg', $mainPhoto->filename);

        $secondPhoto = $place->photos->where('is_main', false)->first();
        $this->assertEquals('place-2.jpg', $secondPhoto->filename);
    }

    public function test_create_photos_handles_empty_array(): void
    {
        $place = Place::factory()->create();

        $this->repository->createPhotos($place, []);

        $this->assertCount(0, $place->photos);
    }

    public function test_create_photos_respects_sort_order(): void
    {
        $place = Place::factory()->create();

        $photos = [
            [
                'filename' => 'place-1.jpg',
                'original_name' => 'Place 1.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024000,
                'is_main' => false,
                'sort_order' => 2,
            ],
            [
                'filename' => 'place-2.jpg',
                'original_name' => 'Place 2.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024000,
                'is_main' => true,
                'sort_order' => 1,
            ],
        ];

        $this->repository->createPhotos($place, $photos);

        $orderedPhotos = $place->photos->sortBy('sort_order')->values();
        $this->assertEquals(1, $orderedPhotos[0]->sort_order);
        $this->assertEquals(2, $orderedPhotos[1]->sort_order);
    }
}
