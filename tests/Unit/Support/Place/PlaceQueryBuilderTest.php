<?php

namespace Tests\Unit\Support\Place;

use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Support\Place\PlaceQueryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceQueryBuilderTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // TESTS applyFilters() - Main Method
    // ========================================

    public function test_apply_filters_defaults_to_proximity_mode(): void
    {
        $place = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
        ]);

        $results = $query->get();

        // Should apply proximity mode by default
        $this->assertCount(1, $results);
        $this->assertEquals($place->id, $results->first()->id);
    }

    public function test_apply_filters_with_proximity_mode(): void
    {
        $place = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
        ]);

        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals($place->id, $results->first()->id);
    }

    public function test_apply_filters_with_worldwide_mode(): void
    {
        $place = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $tag = Tag::factory()->create();
        $tagTranslation = TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $place->tags()->attach($tag->id);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
            'tags' => ['nasa'],
        ]);

        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals($place->id, $results->first()->id);
    }

    // ========================================
    // TESTS Proximity Mode
    // ========================================

    public function test_proximity_mode_filters_by_distance(): void
    {
        // Paris coordinates: 48.8566, 2.3522
        $parisPlace = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $parisPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Lyon coordinates: 45.7640, 4.8357 (approx 400km from Paris)
        $lyonPlace = Place::factory()->create([
            'latitude' => 45.7640,
            'longitude' => 4.8357,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $lyonPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Search from Paris with 200km radius
        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000, // 200km
        ]);

        $results = $query->get();

        // Should only find Paris (Lyon is ~400km away)
        $this->assertCount(1, $results);
        $this->assertEquals($parisPlace->id, $results->first()->id);
    }

    public function test_proximity_mode_returns_empty_without_coordinates(): void
    {
        Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'proximity',
            'radius' => 200000,
        ]);

        $results = $query->get();

        // Should return nothing without coordinates
        $this->assertCount(0, $results);
    }

    public function test_proximity_mode_orders_by_distance(): void
    {
        // Paris
        $parisPlace = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $parisPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Versailles (closer to Paris)
        $versaillesPlace = Place::factory()->create([
            'latitude' => 48.8049,
            'longitude' => 2.1204,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $versaillesPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Search from Paris
        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
        ]);

        $results = $query->get();

        // Paris should be first (distance = 0), Versailles second
        $this->assertCount(2, $results);
        $this->assertEquals($parisPlace->id, $results->first()->id);
        $this->assertEquals($versaillesPlace->id, $results->last()->id);
    }

    public function test_proximity_mode_with_tags_filter(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        // Paris with NASA tag
        $parisPlace = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $parisPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $parisPlace->tags()->attach($tag->id);

        // Versailles without tag
        $versaillesPlace = Place::factory()->create([
            'latitude' => 48.8049,
            'longitude' => 2.1204,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $versaillesPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Search with tag filter
        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => ['nasa'],
        ]);

        $results = $query->get();

        // Should only find Paris (has NASA tag)
        $this->assertCount(1, $results);
        $this->assertEquals($parisPlace->id, $results->first()->id);
    }

    // ========================================
    // TESTS Worldwide Mode
    // ========================================

    public function test_worldwide_mode_requires_tags(): void
    {
        Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
        ]);

        $results = $query->get();

        // Should return nothing without tags
        $this->assertCount(0, $results);
    }

    public function test_worldwide_mode_filters_by_tags(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        // Place with NASA tag
        $placeWithTag = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $placeWithTag->tags()->attach($tag->id);

        // Place without tag
        $placeWithoutTag = Place::factory()->create([
            'latitude' => 45.7640,
            'longitude' => 4.8357,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithoutTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
            'tags' => ['nasa'],
        ]);

        $results = $query->get();

        // Should only find place with NASA tag
        $this->assertCount(1, $results);
        $this->assertEquals($placeWithTag->id, $results->first()->id);
    }

    public function test_worldwide_mode_orders_by_created_at_desc(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        // Older place
        $olderPlace = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'created_at' => now()->subDays(10),
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $olderPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $olderPlace->tags()->attach($tag->id);

        // Newer place
        $newerPlace = Place::factory()->create([
            'latitude' => 45.7640,
            'longitude' => 4.8357,
            'created_at' => now()->subDays(1),
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $newerPlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $newerPlace->tags()->attach($tag->id);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
            'tags' => ['nasa'],
        ]);

        $results = $query->get();

        // Newer place should be first
        $this->assertCount(2, $results);
        $this->assertEquals($newerPlace->id, $results->first()->id);
        $this->assertEquals($olderPlace->id, $results->last()->id);
    }

    // ========================================
    // TESTS Bounding Box
    // ========================================

    public function test_bounding_box_filters_coordinates(): void
    {
        // Place inside bounding box
        $insidePlace = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $insidePlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Place outside bounding box
        $outsidePlace = Place::factory()->create([
            'latitude' => 40.7128, // New York
            'longitude' => -74.0060,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $outsidePlace->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Bounding box around Paris
        $query = Place::query();
        PlaceQueryBuilder::applyBoundingBox($query, [
            'north' => 49.0,
            'south' => 48.5,
            'east' => 2.5,
            'west' => 2.0,
        ]);

        $results = $query->get();

        // Should only find Paris
        $this->assertCount(1, $results);
        $this->assertEquals($insidePlace->id, $results->first()->id);
    }

    public function test_bounding_box_does_nothing_with_incomplete_bounds(): void
    {
        Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyBoundingBox($query, [
            'north' => 49.0,
            'south' => 48.5,
            // Missing east and west
        ]);

        $results = $query->get();

        // Should return all places (no filter applied)
        $this->assertCount(1, $results);
    }

    public function test_bounding_box_with_null_values(): void
    {
        Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyBoundingBox($query, [
            'north' => null,
            'south' => null,
            'east' => null,
            'west' => null,
        ]);

        $results = $query->get();

        // Should return all places (no filter applied)
        $this->assertCount(1, $results);
    }

    // ========================================
    // TESTS Tags Filter
    // ========================================

    public function test_tags_filter_with_tag_ids(): void
    {
        $tag = Tag::factory()->create();

        $placeWithTag = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $placeWithTag->tags()->attach($tag->id);

        $placeWithoutTag = Place::factory()->create([
            'latitude' => 45.7640,
            'longitude' => 4.8357,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithoutTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
            'tags' => [$tag->id],
        ]);

        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals($placeWithTag->id, $results->first()->id);
    }

    public function test_tags_filter_with_tag_slugs(): void
    {
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'nasa',
            'status' => 'published',
        ]);

        $placeWithTag = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $placeWithTag->tags()->attach($tag->id);

        $placeWithoutTag = Place::factory()->create([
            'latitude' => 45.7640,
            'longitude' => 4.8357,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithoutTag->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
            'tags' => ['nasa'],
        ]);

        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals($placeWithTag->id, $results->first()->id);
    }

    public function test_tags_filter_with_mixed_ids_and_slugs(): void
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        TagTranslation::factory()->create([
            'tag_id' => $tag2->id,
            'locale' => 'fr',
            'slug' => 'spacex',
            'status' => 'published',
        ]);

        $placeWithTag1 = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithTag1->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $placeWithTag1->tags()->attach($tag1->id);

        $placeWithTag2 = Place::factory()->create([
            'latitude' => 45.7640,
            'longitude' => 4.8357,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $placeWithTag2->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $placeWithTag2->tags()->attach($tag2->id);

        // Search with mix of ID and slug
        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'worldwide',
            'tags' => [$tag1->id, 'spacex'],
        ]);

        $results = $query->get();

        // Should find both places
        $this->assertCount(2, $results);
    }

    public function test_tags_filter_with_empty_array_does_nothing_in_proximity_mode(): void
    {
        $place = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $query = Place::query();
        PlaceQueryBuilder::applyFilters($query, [
            'mode' => 'proximity',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 200000,
            'tags' => [],
        ]);

        $results = $query->get();

        // Should find place (empty tags doesn't filter in proximity mode)
        $this->assertCount(1, $results);
    }
}
