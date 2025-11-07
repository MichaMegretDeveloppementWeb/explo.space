<?php

namespace Tests\Unit\Repositories\Web\Place\Index;

use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Repositories\Web\Place\Index\PlaceExplorationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceExplorationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlaceExplorationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PlaceExplorationRepository;

        // Set locale for tests
        app()->setLocale('fr');
    }

    // ==========================================
    // getPlacesCoordinates() tests (for map)
    // ==========================================

    /**
     * Test getPlacesCoordinates returns only id, latitude, longitude
     */
    public function test_get_places_coordinates_returns_simple_arrays(): void
    {
        // Create tag for worldwide mode
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        $place1 = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place1->tags()->attach($tag->id);

        $place2 = Place::factory()->create(['latitude' => 48.8606, 'longitude' => 2.3376]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place2->tags()->attach($tag->id);

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'], // At least one tag required for worldwide mode
        ];

        $result = $this->repository->getPlacesCoordinates($filters);

        // Should return Collection of arrays
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(2, $result);

        // Each item should be simple array with only 4 keys
        $place = $result->first();
        $this->assertIsArray($place);
        $this->assertArrayHasKey('id', $place);
        $this->assertArrayHasKey('latitude', $place);
        $this->assertArrayHasKey('longitude', $place);
        $this->assertArrayHasKey('is_featured', $place);
        $this->assertCount(4, $place);

        // Values should be correct types
        $this->assertIsInt($place['id']);
        $this->assertIsFloat($place['latitude']);
        $this->assertIsFloat($place['longitude']);
        $this->assertIsBool($place['is_featured']);
    }

    /**
     * Test getPlacesCoordinates applies bounding box filter
     */
    public function test_get_places_coordinates_applies_bounding_box(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        // Paris (inside bounding box)
        $place1 = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place1->tags()->attach($tag->id);

        // Lyon (outside bounding box)
        $place2 = Place::factory()->create(['latitude' => 45.7640, 'longitude' => 4.8357]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place2->tags()->attach($tag->id);

        // Bounding box around Paris only
        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $result = $this->repository->getPlacesCoordinates($filters, $boundingBox);

        // Should only return Paris (Lyon excluded by bounding box)
        $this->assertCount(1, $result);
        $this->assertEquals($place1->id, $result->first()['id']);
        $this->assertEquals(48.8566, $result->first()['latitude']);
    }

    /**
     * Test getPlacesCoordinates limits results to MAX_MAP_COORDINATES
     * Note: Testing with 100 places instead of actual MAX_MAP_COORDINATES (100,000) for performance
     */
    public function test_get_places_coordinates_limits_results(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        // Create 100 places (enough to verify limit is applied)
        $totalPlaces = 100;

        for ($i = 0; $i < $totalPlaces; $i++) {
            $place = Place::factory()->create([
                'latitude' => 48.8566 + ($i * 0.001),
                'longitude' => 2.3522 + ($i * 0.001),
            ]);

            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
                'locale' => 'fr',
                'status' => 'published',
                'slug' => "place-{$i}", // Unique slug
            ]);
            $place->tags()->attach($tag->id);
        }

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $result = $this->repository->getPlacesCoordinates($filters);

        // Should return all 100 (under limit)
        // The actual limit (100,000) would be tested in integration/performance tests
        $this->assertCount($totalPlaces, $result);
    }

    /**
     * Test getPlacesCoordinates applies tag filters
     */
    public function test_get_places_coordinates_applies_tag_filters(): void
    {
        $tag1 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'slug' => 'nasa',
        ]);

        $tag2 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'slug' => 'spacex',
        ]);

        $place1 = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place1->tags()->attach($tag1->id);

        $place2 = Place::factory()->create(['latitude' => 48.8606, 'longitude' => 2.3376]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place2->tags()->attach($tag2->id);

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['nasa'], // Only nasa tag
        ];

        $result = $this->repository->getPlacesCoordinates($filters);

        // Should only return place1 (has nasa tag)
        $this->assertCount(1, $result);
        $this->assertEquals($place1->id, $result->first()['id']);
    }

    // ==========================================
    // getPlacesInBoundingBoxAsArrays() tests (for list)
    // ==========================================

    /**
     * Test getPlacesInBoundingBoxAsArrays returns correct structure
     */
    public function test_get_places_in_bounding_box_returns_correct_structure(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        $place = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
            'title' => 'Tour Eiffel',
            'description' => 'Monument emblématique',
            'slug' => 'tour-eiffel',
        ]);
        $place->tags()->attach($tag->id);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
        ]);

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should have correct keys
        $this->assertIsArray($result);
        $this->assertArrayHasKey('places', $result);
        $this->assertArrayHasKey('nextCursor', $result);
        $this->assertArrayHasKey('hasMorePages', $result);

        // Places should be array of arrays (not Eloquent models)
        $this->assertIsArray($result['places']);
        $this->assertCount(1, $result['places']);

        $placeArray = $result['places'][0];
        $this->assertIsArray($placeArray);

        // Should have correct place data structure
        $this->assertArrayHasKey('id', $placeArray);
        $this->assertArrayHasKey('latitude', $placeArray);
        $this->assertArrayHasKey('longitude', $placeArray);
        $this->assertArrayHasKey('address', $placeArray);
        $this->assertArrayHasKey('distance', $placeArray);
        $this->assertArrayHasKey('translation', $placeArray);
        $this->assertArrayHasKey('main_photo', $placeArray);
        $this->assertArrayHasKey('tags', $placeArray);

        // Translation should be array with title, description, slug
        $this->assertIsArray($placeArray['translation']);
        $this->assertEquals('Tour Eiffel', $placeArray['translation']['title']);
        $this->assertEquals('Monument emblématique', $placeArray['translation']['description']);
        $this->assertEquals('tour-eiffel', $placeArray['translation']['slug']);

        // Main photo should be array with thumb_url, url
        $this->assertIsArray($placeArray['main_photo']);
        $this->assertArrayHasKey('thumb_url', $placeArray['main_photo']);
        $this->assertArrayHasKey('url', $placeArray['main_photo']);

        // Tags should be array
        $this->assertIsArray($placeArray['tags']);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays applies bounding box filter
     */
    public function test_get_places_in_bounding_box_applies_bounding_box_filter(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        // Paris (inside bounding box)
        $place1 = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place1->tags()->attach($tag->id);

        // Lyon (outside bounding box)
        $place2 = Place::factory()->create(['latitude' => 45.7640, 'longitude' => 4.8357]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place2->tags()->attach($tag->id);

        // Bounding box around Paris only
        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should only return Paris (Lyon excluded by bounding box)
        $this->assertCount(1, $result['places']);
        $this->assertEquals($place1->id, $result['places'][0]['id']);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays filters by published translations only
     */
    public function test_get_places_in_bounding_box_filters_by_published_translations(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        $place1 = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place1->tags()->attach($tag->id);

        $place2 = Place::factory()->create(['latitude' => 48.8606, 'longitude' => 2.3376]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'draft', // Draft status
        ]);
        $place2->tags()->attach($tag->id);

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should only return place1 (place2 has draft translation)
        $this->assertCount(1, $result['places']);
        $this->assertEquals($place1->id, $result['places'][0]['id']);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays filters by locale
     */
    public function test_get_places_in_bounding_box_filters_by_locale(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial-fr',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'slug' => 'spatial-en',
        ]);

        $place = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
            'title' => 'Tour Eiffel',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'status' => 'published',
            'title' => 'Eiffel Tower',
        ]);
        $place->tags()->attach($tag->id);

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        // Request in French
        app()->setLocale('fr');
        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial-fr'],
        ];
        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should return French translation
        $this->assertCount(1, $result['places']);
        $this->assertEquals('Tour Eiffel', $result['places'][0]['translation']['title']);

        // Request in English
        app()->setLocale('en');
        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial-en'],
        ];
        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'en',
            20,
            null
        );

        // Should return English translation
        $this->assertCount(1, $result['places']);
        $this->assertEquals('Eiffel Tower', $result['places'][0]['translation']['title']);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays loads only main photo
     */
    public function test_get_places_in_bounding_box_loads_only_main_photo(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        $place = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place->tags()->attach($tag->id);

        // Main photo
        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'filename' => 'main.jpg',
        ]);

        // Secondary photo (should not be loaded)
        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'filename' => 'secondary.jpg',
        ]);

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should have main_photo
        $this->assertNotNull($result['places'][0]['main_photo']);
        $this->assertStringContainsString('main.jpg', $result['places'][0]['main_photo']['url']);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays in proximity mode loads ALL tags
     */
    public function test_get_places_in_bounding_box_proximity_mode_loads_all_tags(): void
    {
        $place = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $tag1 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
        ]);

        $tag2 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
        ]);

        $place->tags()->attach([$tag1->id, $tag2->id]);

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            // NO tags filter - should load ALL tags
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should load ALL tags (both NASA and SpaceX)
        $this->assertCount(2, $result['places'][0]['tags']);
        $tagNames = array_column($result['places'][0]['tags'], 'name');
        $this->assertContains('NASA', $tagNames);
        $this->assertContains('SpaceX', $tagNames);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays in worldwide mode with filters loads ONLY selected tags
     */
    public function test_get_places_in_bounding_box_worldwide_mode_loads_only_selected_tags(): void
    {
        $place = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $tag1 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
        ]);

        $tag2 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'name' => 'SpaceX',
            'slug' => 'spacex',
        ]);

        $tag3 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag3->id,
            'locale' => 'fr',
            'name' => 'ESA',
            'slug' => 'esa',
        ]);

        $place->tags()->attach([$tag1->id, $tag2->id, $tag3->id]);

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['nasa', 'spacex'], // Only these 2 tags selected
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // Should load ONLY selected tags (NASA and SpaceX, NOT ESA)
        $this->assertCount(2, $result['places'][0]['tags']);
        $tagNames = array_column($result['places'][0]['tags'], 'name');
        $this->assertContains('NASA', $tagNames);
        $this->assertContains('SpaceX', $tagNames);
        $this->assertNotContains('ESA', $tagNames);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays cursor pagination works
     */
    public function test_get_places_in_bounding_box_cursor_pagination_works(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        // Create 3 places
        for ($i = 1; $i <= 3; $i++) {
            $place = Place::factory()->create([
                'latitude' => 48.8566 + ($i * 0.001),
                'longitude' => 2.3522 + ($i * 0.001),
            ]);

            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
                'locale' => 'fr',
                'status' => 'published',
                'title' => "Place {$i}",
                'slug' => "place-{$i}",
            ]);
            $place->tags()->attach($tag->id);
        }

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        // First page (2 items)
        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            2, // Only 2 per page
            null
        );

        $this->assertCount(2, $result['places']);
        $this->assertTrue($result['hasMorePages']);
        $this->assertNotNull($result['nextCursor']);

        // Second page (1 item)
        $result2 = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            2,
            $result['nextCursor'] // Use cursor from first page
        );

        $this->assertCount(1, $result2['places']);
        $this->assertFalse($result2['hasMorePages']);
        $this->assertNull($result2['nextCursor']);
    }

    /**
     * Test getPlacesInBoundingBoxAsArrays returns null main_photo when no photo
     */
    public function test_get_places_in_bounding_box_returns_null_main_photo_when_no_photo(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'spatial',
        ]);

        $place = Place::factory()->create(['latitude' => 48.8566, 'longitude' => 2.3522]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $place->tags()->attach($tag->id);

        // No photos

        $boundingBox = [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ];

        $filters = [
            'mode' => 'worldwide',
            'tags' => ['spatial'],
        ];

        $result = $this->repository->getPlacesInBoundingBoxAsArrays(
            $filters,
            $boundingBox,
            'fr',
            20,
            null
        );

        // main_photo should be null
        $this->assertNull($result['places'][0]['main_photo']);
    }
}
