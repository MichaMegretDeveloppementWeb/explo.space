<?php

namespace Tests\Unit\Repositories\Admin\Place\Management;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Repositories\Admin\Place\Management\PlaceDeleteRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PlaceDeleteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlaceDeleteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PlaceDeleteRepository;

        Log::spy();
    }

    public function test_delete_place_successfully(): void
    {
        $place = Place::factory()->create();

        $result = $this->repository->deletePlace($place->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('places', ['id' => $place->id]);
    }

    public function test_delete_place_cascades_to_translations(): void
    {
        $place = Place::factory()->create();

        $translation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
        ]);

        $this->repository->deletePlace($place->id);

        $this->assertDatabaseMissing('place_translations', ['id' => $translation->id]);
    }

    public function test_delete_place_cascades_to_photos(): void
    {
        $place = Place::factory()->create();

        $photo = Photo::factory()->create(['place_id' => $place->id]);

        $this->repository->deletePlace($place->id);

        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
    }

    public function test_delete_place_detaches_tags(): void
    {
        $place = Place::factory()->create();
        $tag = Tag::factory()->create();
        $place->tags()->attach($tag);

        $this->assertDatabaseHas('place_tag', [
            'place_id' => $place->id,
            'tag_id' => $tag->id,
        ]);

        $this->repository->deletePlace($place->id);

        $this->assertDatabaseMissing('place_tag', [
            'place_id' => $place->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_delete_place_detaches_categories(): void
    {
        $place = Place::factory()->create();
        $category = Category::factory()->create();
        $place->categories()->attach($category);

        $this->assertDatabaseHas('place_category', [
            'place_id' => $place->id,
            'category_id' => $category->id,
        ]);

        $this->repository->deletePlace($place->id);

        $this->assertDatabaseMissing('place_category', [
            'place_id' => $place->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_place_exists_returns_true_when_place_exists(): void
    {
        $place = Place::factory()->create();

        $result = $this->repository->placeExists($place->id);

        $this->assertTrue($result);
    }

    public function test_place_exists_returns_false_when_place_not_exists(): void
    {
        $result = $this->repository->placeExists(99999);

        $this->assertFalse($result);
    }

    public function test_find_place_returns_place_when_exists(): void
    {
        $place = Place::factory()->create();

        $result = $this->repository->findPlace($place->id);

        $this->assertNotNull($result);
        $this->assertEquals($place->id, $result->id);
    }

    public function test_find_place_returns_null_when_not_exists(): void
    {
        $result = $this->repository->findPlace(99999);

        $this->assertNull($result);
    }
}
