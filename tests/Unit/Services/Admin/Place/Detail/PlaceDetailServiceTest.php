<?php

namespace Tests\Unit\Services\Admin\Place\Detail;

use App\Contracts\Repositories\Admin\Place\Detail\PlaceDetailRepositoryInterface;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Services\Admin\Place\Detail\PlaceDetailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceDetailServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlaceDetailService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = app(PlaceDetailRepositoryInterface::class);
        $this->service = new PlaceDetailService($repository);
    }

    public function test_get_place_detail_returns_complete_data(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create(['place_id' => $place->id]);
        Photo::factory()->create(['place_id' => $place->id, 'is_main' => true]);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('place', $result);
        $this->assertArrayHasKey('has_translations', $result);
        $this->assertArrayHasKey('translation_count', $result);
        $this->assertArrayHasKey('has_tags', $result);
        $this->assertArrayHasKey('tag_count', $result);
        $this->assertArrayHasKey('has_categories', $result);
        $this->assertArrayHasKey('category_count', $result);
        $this->assertArrayHasKey('has_photos', $result);
        $this->assertArrayHasKey('photo_count', $result);
        $this->assertArrayHasKey('main_photo', $result);
    }

    public function test_calculates_translation_stats_correctly(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create(['place_id' => $place->id, 'locale' => 'fr']);
        PlaceTranslation::factory()->create(['place_id' => $place->id, 'locale' => 'en']);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertTrue($result['has_translations']);
        $this->assertEquals(2, $result['translation_count']);
    }

    public function test_calculates_tag_stats_correctly(): void
    {
        $place = Place::factory()->create();
        $tags = Tag::factory()->count(3)->create(['is_active' => true]);
        $place->tags()->attach($tags);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertTrue($result['has_tags']);
        $this->assertEquals(3, $result['tag_count']);
    }

    public function test_calculates_category_stats_correctly(): void
    {
        $place = Place::factory()->create();
        $categories = Category::factory()->count(2)->create(['is_active' => true]);
        $place->categories()->attach($categories);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertTrue($result['has_categories']);
        $this->assertEquals(2, $result['category_count']);
    }

    public function test_calculates_photo_stats_correctly(): void
    {
        $place = Place::factory()->create();
        Photo::factory()->count(5)->create(['place_id' => $place->id]);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertTrue($result['has_photos']);
        $this->assertEquals(5, $result['photo_count']);
    }

    public function test_identifies_main_photo(): void
    {
        $place = Place::factory()->create();
        $mainPhoto = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
        ]);
        Photo::factory()->count(2)->create([
            'place_id' => $place->id,
            'is_main' => false,
        ]);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertNotNull($result['main_photo']);
        $this->assertEquals($mainPhoto->id, $result['main_photo']->id);
    }

    public function test_returns_null_main_photo_when_none_set(): void
    {
        $place = Place::factory()->create();
        Photo::factory()->count(2)->create([
            'place_id' => $place->id,
            'is_main' => false,
        ]);

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertNull($result['main_photo']);
    }

    public function test_handles_place_with_no_relations(): void
    {
        $place = Place::factory()->create();

        $result = $this->service->getPlaceDetail($place->id);

        $this->assertFalse($result['has_translations']);
        $this->assertEquals(0, $result['translation_count']);
        $this->assertFalse($result['has_tags']);
        $this->assertEquals(0, $result['tag_count']);
        $this->assertFalse($result['has_categories']);
        $this->assertEquals(0, $result['category_count']);
        $this->assertFalse($result['has_photos']);
        $this->assertEquals(0, $result['photo_count']);
        $this->assertNull($result['main_photo']);
    }

    public function test_place_exists_returns_true_when_exists(): void
    {
        $place = Place::factory()->create();

        $result = $this->service->placeExists($place->id);

        $this->assertTrue($result);
    }

    public function test_place_exists_returns_false_when_not_exists(): void
    {
        $result = $this->service->placeExists(99999);

        $this->assertFalse($result);
    }

    public function test_returns_empty_array_when_place_not_found(): void
    {
        $result = $this->service->getPlaceDetail(99999);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
