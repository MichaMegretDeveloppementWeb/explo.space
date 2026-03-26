<?php

namespace Tests\Unit\Services\Admin\Autofill;

use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Services\Admin\Autofill\DeduplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeduplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    private DeduplicationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DeduplicationService;
    }

    // --- Name-based deduplication (primary) ---

    public function test_identical_name_is_duplicate_regardless_of_coordinates(): void
    {
        $place = Place::factory()->create(['latitude' => 48.947, 'longitude' => 2.437]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
        ]);

        $result = $this->service->filterDuplicates([
            ['name' => 'Kennedy Space Center', 'latitude' => 0, 'longitude' => 0],
        ]);

        $this->assertCount(0, $result['unique']);
        $this->assertSame('Kennedy Space Center', $result['duplicateNames'][0]);
    }

    public function test_name_matches_against_all_locale_translations(): void
    {
        $place = Place::factory()->create(['latitude' => 48.947, 'longitude' => 2.437]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Musée de l\'Air et de l\'Espace',
        ]);

        $result = $this->service->filterDuplicates([
            ['name' => 'Musée de l\'Air et de l\'Espace', 'latitude' => 10, 'longitude' => 20],
        ]);

        $this->assertCount(0, $result['unique']);
        $this->assertCount(1, $result['duplicateNames']);
    }

    public function test_name_containment_detects_duplicate(): void
    {
        $place = Place::factory()->create(['latitude' => 28.572, 'longitude' => -80.648]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
        ]);

        $result = $this->service->filterDuplicates([
            ['name' => 'Kennedy Space Center Visitor Complex', 'latitude' => 28.524, 'longitude' => -80.681],
        ]);

        $this->assertCount(0, $result['unique']);
        $this->assertCount(1, $result['duplicateNames']);
    }

    // --- Coordinate-based deduplication (secondary) ---

    public function test_nearby_coords_is_duplicate_even_with_different_name(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5721,
            'longitude' => -80.6480,
        ]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
        ]);

        // Same coordinates, different name → duplicate (same physical location)
        $result = $this->service->filterDuplicates([
            ['name' => 'KSC Launch Pad 39B', 'latitude' => 28.5725, 'longitude' => -80.6485],
        ]);

        $this->assertCount(0, $result['unique']);
    }

    public function test_far_coords_and_different_name_is_not_duplicate(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5721,
            'longitude' => -80.6480,
        ]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
        ]);

        // Far away, different name → unique
        $result = $this->service->filterDuplicates([
            ['name' => 'Baikonur Cosmodrome', 'latitude' => 45.9646, 'longitude' => 63.3052],
        ]);

        $this->assertCount(1, $result['unique']);
        $this->assertEmpty($result['duplicateNames']);
    }

    // --- Edge cases ---

    public function test_no_places_in_db_returns_all_unique(): void
    {
        $places = [
            ['name' => 'Baikonur Cosmodrome', 'latitude' => 45.9646, 'longitude' => 63.3052],
            ['name' => 'Guiana Space Centre', 'latitude' => 5.2322, 'longitude' => -52.7693],
        ];

        $result = $this->service->filterDuplicates($places);

        $this->assertCount(2, $result['unique']);
        $this->assertEmpty($result['duplicateNames']);
    }

    public function test_empty_name_is_not_duplicate(): void
    {
        $result = $this->service->filterDuplicates([
            ['name' => '', 'latitude' => 28.5721, 'longitude' => -80.6480],
        ]);

        $this->assertCount(1, $result['unique']);
    }

    public function test_zero_coords_fall_back_to_name_only(): void
    {
        // No place in DB → not duplicate even with zero coords
        $result = $this->service->filterDuplicates([
            ['name' => 'Some Place', 'latitude' => 0, 'longitude' => 0],
        ]);

        $this->assertCount(1, $result['unique']);
    }

    public function test_filter_separates_unique_and_duplicates(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5721,
            'longitude' => -80.6480,
        ]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
        ]);

        $places = [
            ['name' => 'Kennedy Space Center', 'latitude' => 28.5725, 'longitude' => -80.6485],
            ['name' => 'Baikonur Cosmodrome', 'latitude' => 45.9646, 'longitude' => 63.3052],
        ];

        $result = $this->service->filterDuplicates($places);

        $this->assertCount(1, $result['unique']);
        $this->assertSame('Baikonur Cosmodrome', $result['unique'][0]['name']);
        $this->assertCount(1, $result['duplicateNames']);
        $this->assertSame('Kennedy Space Center', $result['duplicateNames'][0]);
    }

    // --- Name normalization & matching ---

    public function test_normalize_name_removes_accents_and_punctuation(): void
    {
        $this->assertSame('centre spatial kennedy', $this->service->normalizeName('Centre Spatial Kennedy'));
        $this->assertSame('musee de lair et de lespace', $this->service->normalizeName('Musée de l\'Air et de l\'Espace'));
    }

    public function test_names_match_identical(): void
    {
        $this->assertTrue($this->service->namesMatch('Kennedy Space Center', 'Kennedy Space Center'));
    }

    public function test_names_match_case_insensitive(): void
    {
        $this->assertTrue($this->service->namesMatch('kennedy space center', 'KENNEDY SPACE CENTER'));
    }

    public function test_names_do_not_match_completely_different(): void
    {
        $this->assertFalse($this->service->namesMatch('Kennedy Space Center', 'Baikonur Cosmodrome'));
    }
}
