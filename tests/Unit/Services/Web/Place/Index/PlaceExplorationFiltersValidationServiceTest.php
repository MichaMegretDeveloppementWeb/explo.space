<?php

namespace Tests\Unit\Services\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Enums\ValidationStrategy;
use App\Exceptions\Admin\Place\InvalidPlaceFiltersException;
use App\Services\Web\Place\Index\PlaceExplorationFiltersValidationService;
use App\Services\Web\Tag\TagSelectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PlaceExplorationFiltersValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlaceExplorationFiltersValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock TagSelectionService pour éviter les requêtes DB
        $mockTagService = Mockery::mock(TagSelectionService::class);

        // Mock validateAndCleanSlugs to return valid slugs (no DB check)
        $mockTagService->shouldReceive('validateAndCleanSlugs')
            ->andReturnUsing(function ($tags, $locale) {
                // Simulate that all provided tags are valid (no DB check in tests)
                // Note: Service will limit to 10 after this
                return array_values(array_unique($tags));
            });

        $this->service = new PlaceExplorationFiltersValidationService($mockTagService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test validation with CORRECT_SILENTLY strategy - radius below minimum
     */
    public function test_corrects_radius_below_minimum(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['radius' => 1000]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(200000, $result->radius); // Corrected to default 200km
    }

    /**
     * Test validation with CORRECT_SILENTLY strategy - radius above maximum
     */
    public function test_corrects_radius_above_maximum(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['radius' => 3000000]); // 3000km > 2500km max

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(200000, $result->radius); // Corrected to default 200km
    }

    /**
     * Test validation with THROW strategy - radius below minimum
     */
    public function test_throws_on_invalid_radius_below_minimum(): void
    {
        $this->expectException(InvalidPlaceFiltersException::class);

        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['radius' => 1000]);

        $this->service->validate($dto, ValidationStrategy::THROW);
    }

    /**
     * Test validation with THROW strategy - radius above maximum
     */
    public function test_throws_on_invalid_radius_above_maximum(): void
    {
        $this->expectException(InvalidPlaceFiltersException::class);

        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['radius' => 3000000]); // 3000km > 2500km max

        $this->service->validate($dto, ValidationStrategy::THROW);
    }

    /**
     * Test validation with valid radius
     */
    public function test_accepts_valid_radius(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['radius' => 500000]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(500000, $result->radius);
    }

    /**
     * Test validation with CORRECT_SILENTLY strategy - invalid mode
     */
    public function test_corrects_invalid_mode(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['mode' => 'invalid_mode']);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals('proximity', $result->mode); // Corrected to default
    }

    /**
     * Test validation with THROW strategy - invalid mode
     */
    public function test_throws_on_invalid_mode(): void
    {
        $this->expectException(InvalidPlaceFiltersException::class);

        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['mode' => 'invalid_mode']);

        $this->service->validate($dto, ValidationStrategy::THROW);
    }

    /**
     * Test validation with valid modes
     */
    public function test_accepts_valid_modes(): void
    {
        $proximityDto = PlaceExplorationFiltersDTO::fromUrlParams(['mode' => 'proximity']);
        $proximityResult = $this->service->validate($proximityDto, ValidationStrategy::CORRECT_SILENTLY);

        $worldwideDto = PlaceExplorationFiltersDTO::fromUrlParams(['mode' => 'worldwide']);
        $worldwideResult = $this->service->validate($worldwideDto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals('proximity', $proximityResult->mode);
        $this->assertEquals('worldwide', $worldwideResult->mode);
    }

    /**
     * Test validation with CORRECT_SILENTLY strategy - invalid latitude
     */
    public function test_corrects_invalid_latitude(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['lat' => 95, 'lng' => 2]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertNull($result->latitude);
        $this->assertNull($result->longitude); // Both nullified for consistency
    }

    /**
     * Test validation with THROW strategy - invalid latitude
     */
    public function test_throws_on_invalid_latitude(): void
    {
        $this->expectException(InvalidPlaceFiltersException::class);

        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['lat' => 95, 'lng' => 2]);

        $this->service->validate($dto, ValidationStrategy::THROW);
    }

    /**
     * Test validation with CORRECT_SILENTLY strategy - invalid longitude
     */
    public function test_corrects_invalid_longitude(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['lat' => 45, 'lng' => 200]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertNull($result->latitude);
        $this->assertNull($result->longitude); // Both nullified for consistency
    }

    /**
     * Test validation with THROW strategy - invalid longitude
     */
    public function test_throws_on_invalid_longitude(): void
    {
        $this->expectException(InvalidPlaceFiltersException::class);

        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['lat' => 45, 'lng' => 200]);

        $this->service->validate($dto, ValidationStrategy::THROW);
    }

    /**
     * Test validation with valid coordinates
     */
    public function test_accepts_valid_coordinates(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['lat' => 48.8566, 'lng' => 2.3522]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(48.8566, $result->latitude);
        $this->assertEquals(2.3522, $result->longitude);
    }

    /**
     * Test validation with tags as array
     */
    public function test_accepts_tags_as_array(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['tags' => ['nasa', 'spacex', 'astronomy']]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(['nasa', 'spacex', 'astronomy'], $result->tags);
    }

    /**
     * Test validation with tags as string
     */
    public function test_parses_tags_from_string(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['tags' => 'nasa,spacex,astronomy']);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(['nasa', 'spacex', 'astronomy'], $result->tags);
    }

    /**
     * Test validation with tags with spaces
     */
    public function test_trims_tags_with_spaces(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['tags' => ' nasa , spacex , astronomy ']);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals(['nasa', 'spacex', 'astronomy'], $result->tags);
    }

    /**
     * Test validation with CORRECT_SILENTLY strategy - too many tags
     */
    public function test_limits_tags_to_maximum(): void
    {
        // Create 15 unique tags
        $manyTags = array_map(fn ($i) => "tag{$i}", range(1, 15));
        $dto = PlaceExplorationFiltersDTO::fromUrlParams(['tags' => $manyTags]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertCount(10, $result->tags); // Limited to 10
    }

    /**
     * Test validation with complete valid filter set
     */
    public function test_validates_complete_valid_filter_set(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams([
            'mode' => 'proximity',
            'lat' => 48.8566,
            'lng' => 2.3522,
            'radius' => 300000,
            'address' => 'Paris, France',
            'tags' => ['nasa', 'esa'],
        ]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals('proximity', $result->mode);
        $this->assertEquals(48.8566, $result->latitude);
        $this->assertEquals(2.3522, $result->longitude);
        $this->assertEquals(300000, $result->radius);
        $this->assertEquals('Paris, France', $result->address);
        $this->assertEquals(['nasa', 'esa'], $result->tags);
        // Note: page field is not validated/returned by service (passthrough in DTO)
    }

    /**
     * Test validation with empty filters returns defaults
     */
    public function test_returns_defaults_for_empty_filters(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams([]);

        $result = $this->service->validate($dto, ValidationStrategy::CORRECT_SILENTLY);

        $this->assertEquals('proximity', $result->mode);
        $this->assertNull($result->latitude);
        $this->assertNull($result->longitude);
        $this->assertEquals(200000, $result->radius);
        $this->assertNull($result->address);
        $this->assertEquals([], $result->tags);
        $this->assertEquals(1, $result->page);
    }
}
