<?php

namespace Tests\Unit\Repositories\Admin\Place\Detail;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use App\Repositories\Admin\Place\Detail\PlaceDetailRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceDetailRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlaceDetailRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PlaceDetailRepository;
    }

    public function test_get_place_with_relations_loads_all_translations(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
        ]);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertTrue($result->relationLoaded('translations'));
        $this->assertCount(2, $result->translations);
    }

    public function test_get_place_with_relations_loads_admin(): void
    {
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertTrue($result->relationLoaded('admin'));
        $this->assertEquals($admin->id, $result->admin->id);
    }

    public function test_get_place_with_relations_loads_tags_with_translations(): void
    {
        $place = Place::factory()->create();

        $tag = Tag::factory()->create(['is_active' => true]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
        ]);

        $place->tags()->attach($tag);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertTrue($result->relationLoaded('tags'));
        $this->assertCount(1, $result->tags);
        $this->assertTrue($result->tags->first()->relationLoaded('translations'));
    }

    public function test_get_place_with_relations_loads_categories(): void
    {
        $place = Place::factory()->create();

        $category = Category::factory()->create([
            'is_active' => true,
            'name' => 'Test Category',
        ]);

        $place->categories()->attach($category);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertTrue($result->relationLoaded('categories'));
        $this->assertCount(1, $result->categories);
        $this->assertEquals('Test Category', $result->categories->first()->name);
    }

    public function test_get_place_with_relations_loads_photos_ordered_correctly(): void
    {
        $place = Place::factory()->create();

        // Create main photo first (earlier created_at)
        $mainPhoto = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
        ]);

        // Wait a moment to ensure different created_at
        sleep(1);

        // Create secondary photo later
        $secondPhoto = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
        ]);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertTrue($result->relationLoaded('photos'));
        $this->assertCount(2, $result->photos);
        // Main photo should be first (due to is_main DESC)
        $this->assertEquals($mainPhoto->id, $result->photos->first()->id);
    }

    public function test_get_place_with_relations_only_loads_active_tags(): void
    {
        $place = Place::factory()->create();

        $activeTag = Tag::factory()->create(['is_active' => true]);
        $inactiveTag = Tag::factory()->create(['is_active' => false]);

        $place->tags()->attach([$activeTag->id, $inactiveTag->id]);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertCount(1, $result->tags);
        $this->assertEquals($activeTag->id, $result->tags->first()->id);
    }

    public function test_get_place_with_relations_only_loads_active_categories(): void
    {
        $place = Place::factory()->create();

        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $place->categories()->attach([$activeCategory->id, $inactiveCategory->id]);

        $result = $this->repository->getPlaceWithRelations($place->id);

        $this->assertCount(1, $result->categories);
        $this->assertEquals($activeCategory->id, $result->categories->first()->id);
    }

    public function test_get_place_with_relations_returns_null_when_not_found(): void
    {
        $result = $this->repository->getPlaceWithRelations(99999);

        $this->assertNull($result);
    }

    public function test_exists_returns_true_when_place_exists(): void
    {
        $place = Place::factory()->create();

        $result = $this->repository->exists($place->id);

        $this->assertTrue($result);
    }

    public function test_exists_returns_false_when_place_does_not_exist(): void
    {
        $result = $this->repository->exists(99999);

        $this->assertFalse($result);
    }
}
