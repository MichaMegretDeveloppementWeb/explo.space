<?php

namespace Tests\Unit\Repositories\Admin\Place\PlaceList;

use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use App\Repositories\Admin\Place\PlaceListRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PlaceListRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PlaceListRepository;
    }

    public function test_get_paginated_places_returns_correct_structure(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Test Place',
            'status' => 'published',
        ]);

        $filters = ['search' => '', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->relationLoaded('translations'));
        $this->assertTrue($result->first()->relationLoaded('admin'));
        $this->assertTrue($result->first()->relationLoaded('tags'));
        $this->assertTrue($result->first()->relationLoaded('categories'));
        $this->assertTrue($result->first()->relationLoaded('photos'));
    }

    public function test_get_paginated_places_filters_by_search_in_title(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'SpaceX Boca Chica',
            'status' => 'published',
        ]);

        $filters = ['search' => 'Kennedy', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($place1->id, $result->first()->id);
    }

    public function test_get_paginated_places_filters_by_search_in_description(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Place 1',
            'description' => 'This is a NASA facility',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Place 2',
            'description' => 'This is a SpaceX facility',
            'status' => 'published',
        ]);

        $filters = ['search' => 'NASA', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($place1->id, $result->first()->id);
    }

    public function test_get_paginated_places_filters_by_tags(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $tag1 = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag1->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $place1->tags()->attach($tag1);

        $filters = ['search' => '', 'tags' => ['nasa'], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($place1->id, $result->first()->id);
    }

    public function test_get_paginated_places_sorts_by_title_asc(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        $place2 = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Zulu Base',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Alpha Station',
            'status' => 'published',
        ]);

        $filters = ['search' => '', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'title', 'direction' => 'asc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Alpha Station', $result->first()->translations->first()->title);
        $this->assertEquals('Zulu Base', $result->last()->translations->first()->title);
    }

    public function test_get_paginated_places_sorts_by_created_at_desc(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place1 = Place::factory()->create([
            'admin_id' => $admin->id,
            'created_at' => now()->subDays(2),
        ]);
        $place2 = Place::factory()->create([
            'admin_id' => $admin->id,
            'created_at' => now()->subDay(),
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $filters = ['search' => '', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals($place2->id, $result->first()->id);
        $this->assertEquals($place1->id, $result->last()->id);
    }

    public function test_get_paginated_places_sorts_by_is_featured(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place1 = Place::factory()->create(['admin_id' => $admin->id, 'is_featured' => false]);
        $place2 = Place::factory()->create(['admin_id' => $admin->id, 'is_featured' => true]);

        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $filters = ['search' => '', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'is_featured', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals($place2->id, $result->first()->id);
        $this->assertEquals($place1->id, $result->last()->id);
    }

    public function test_get_paginated_places_respects_per_page_limit(): void
    {
        // Arrange
        $admin = User::factory()->create();

        for ($i = 0; $i < 15; $i++) {
            $place = Place::factory()->create(['admin_id' => $admin->id]);
            PlaceTranslation::factory()->create([
                'place_id' => $place->id,
                'locale' => 'fr',
                'status' => 'published',
            ]);
        }

        $filters = ['search' => '', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 10);

        // Assert
        $this->assertCount(10, $result);
        $this->assertEquals(15, $result->total());
    }

    public function test_get_paginated_places_returns_only_locale_specific_translations(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre français',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English title',
            'status' => 'published',
        ]);

        $filters = ['search' => '', 'tags' => [], 'locale' => 'fr'];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedPlaces($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $firstPlace = $result->first();
        $this->assertCount(1, $firstPlace->translations);
        $this->assertEquals('fr', $firstPlace->translations->first()->locale);
        $this->assertEquals('Titre français', $firstPlace->translations->first()->title);
    }
}
