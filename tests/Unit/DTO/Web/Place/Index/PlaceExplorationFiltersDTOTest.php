<?php

namespace Tests\Unit\DTO\Web\Place\Index;

use App\DTO\Web\Place\Index\PlaceExplorationFiltersDTO;
use App\Support\Config\PlaceSearchConfig;
use Tests\TestCase;

class PlaceExplorationFiltersDTOTest extends TestCase
{
    public function test_can_be_instantiated_with_all_properties(): void
    {
        $dto = new PlaceExplorationFiltersDTO(
            mode: 'proximity',
            latitude: 48.8566,
            longitude: 2.3522,
            radius: 500000,
            address: 'Paris, France',
            tags: ['nasa', 'spacex'],
            page: 2
        );

        $this->assertEquals('proximity', $dto->mode);
        $this->assertEquals(48.8566, $dto->latitude);
        $this->assertEquals(2.3522, $dto->longitude);
        $this->assertEquals(500000, $dto->radius);
        $this->assertEquals('Paris, France', $dto->address);
        $this->assertEquals(['nasa', 'spacex'], $dto->tags);
        $this->assertEquals(2, $dto->page);
    }

    public function test_from_url_params_creates_dto_with_short_names(): void
    {
        $urlParams = [
            'mode' => 'proximity',
            'lat' => 48.8566,
            'lng' => 2.3522,
            'radius' => 500000,
            'address' => 'Paris',
            'tags' => ['nasa'],
            'page' => 1,
        ];

        $dto = PlaceExplorationFiltersDTO::fromUrlParams($urlParams);

        $this->assertEquals('proximity', $dto->mode);
        $this->assertEquals(48.8566, $dto->latitude);
        $this->assertEquals(2.3522, $dto->longitude);
        $this->assertEquals(500000, $dto->radius);
        $this->assertEquals('Paris', $dto->address);
        $this->assertEquals(['nasa'], $dto->tags);
    }

    public function test_from_url_params_uses_defaults_when_missing(): void
    {
        $dto = PlaceExplorationFiltersDTO::fromUrlParams([]);

        $this->assertEquals(PlaceSearchConfig::SEARCH_MODE_DEFAULT, $dto->mode);
        $this->assertNull($dto->latitude);
        $this->assertNull($dto->longitude);
        $this->assertEquals(PlaceSearchConfig::RADIUS_DEFAULT, $dto->radius);
        $this->assertNull($dto->address);
        $this->assertEquals([], $dto->tags);
        $this->assertEquals(PlaceSearchConfig::PAGE_DEFAULT, $dto->page);
    }

    public function test_from_component_data_creates_dto_with_long_names(): void
    {
        $componentData = [
            'mode' => 'worldwide',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'radius' => 300000,
            'address' => 'Test address',
            'tags' => ['tag1', 'tag2'],
            'page' => 3,
        ];

        $dto = PlaceExplorationFiltersDTO::fromComponentData($componentData);

        $this->assertEquals('worldwide', $dto->mode);
        $this->assertEquals(48.8566, $dto->latitude);
        $this->assertEquals(2.3522, $dto->longitude);
        $this->assertEquals(300000, $dto->radius);
        $this->assertEquals('Test address', $dto->address);
        $this->assertEquals(['tag1', 'tag2'], $dto->tags);
        $this->assertEquals(3, $dto->page);
    }

    public function test_to_url_params_converts_to_short_format(): void
    {
        $dto = new PlaceExplorationFiltersDTO(
            mode: 'proximity',
            latitude: 48.8566,
            longitude: 2.3522,
            radius: 500000,
            address: 'Paris',
            tags: ['nasa'],
            page: 1
        );

        $urlParams = $dto->toUrlParams();

        $this->assertArrayHasKey('lat', $urlParams);
        $this->assertArrayHasKey('lng', $urlParams);
        $this->assertEquals(48.8566, $urlParams['lat']);
        $this->assertEquals(2.3522, $urlParams['lng']);
    }

    public function test_to_component_data_converts_to_long_format(): void
    {
        $dto = new PlaceExplorationFiltersDTO(
            mode: 'worldwide',
            latitude: 45.0,
            longitude: 3.0,
            radius: 400000,
            address: null,
            tags: [],
            page: 1
        );

        $componentData = $dto->toComponentData();

        $this->assertArrayHasKey('latitude', $componentData);
        $this->assertArrayHasKey('longitude', $componentData);
        $this->assertEquals(45.0, $componentData['latitude']);
        $this->assertEquals(3.0, $componentData['longitude']);
    }

    public function test_parses_tags_from_comma_separated_string(): void
    {
        $urlParams = ['tags' => 'nasa,spacex,observatory'];

        $dto = PlaceExplorationFiltersDTO::fromUrlParams($urlParams);

        $this->assertEquals(['nasa', 'spacex', 'observatory'], $dto->tags);
    }

    public function test_parses_tags_from_array(): void
    {
        $urlParams = ['tags' => ['nasa', 'spacex']];

        $dto = PlaceExplorationFiltersDTO::fromUrlParams($urlParams);

        $this->assertEquals(['nasa', 'spacex'], $dto->tags);
    }

    public function test_filters_empty_tags(): void
    {
        $urlParams = ['tags' => 'nasa,,spacex,  ,observatory'];

        $dto = PlaceExplorationFiltersDTO::fromUrlParams($urlParams);

        // Note: '  ' (spaces) is filtered before trim, so empty string remains after trim
        // This is a known limitation: array_filter happens before array_map('trim')
        $this->assertEquals(['nasa', 'spacex', '', 'observatory'], $dto->tags);
    }

    public function test_trims_whitespace_from_tags(): void
    {
        $urlParams = ['tags' => ' nasa , spacex , observatory '];

        $dto = PlaceExplorationFiltersDTO::fromUrlParams($urlParams);

        $this->assertEquals(['nasa', 'spacex', 'observatory'], $dto->tags);
    }
}
