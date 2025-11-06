<?php

namespace Tests\Unit\Support\Config;

use App\Support\Config\PlaceSearchConfig;
use Tests\TestCase;

/**
 * Tests pour PlaceSearchConfig
 *
 * Cette classe centralise la configuration de recherche de lieux.
 * Les tests garantissent que les constantes et helpers fonctionnent correctement.
 */
class PlaceSearchConfigTest extends TestCase
{
    // ========================================
    // Tests des constantes de rayon
    // ========================================

    public function test_radius_constants_are_defined_correctly(): void
    {
        // Assert
        $this->assertEquals(200_000, PlaceSearchConfig::RADIUS_MIN);
        $this->assertEquals(2_500_000, PlaceSearchConfig::RADIUS_MAX);
        $this->assertEquals(200_000, PlaceSearchConfig::RADIUS_DEFAULT);
        $this->assertEquals(10_000, PlaceSearchConfig::RADIUS_STEP);
    }

    public function test_radius_min_is_less_than_radius_max(): void
    {
        // Assert
        $this->assertLessThan(
            PlaceSearchConfig::RADIUS_MAX,
            PlaceSearchConfig::RADIUS_MIN
        );
    }

    public function test_radius_default_is_within_valid_range(): void
    {
        // Assert
        $this->assertGreaterThanOrEqual(
            PlaceSearchConfig::RADIUS_MIN,
            PlaceSearchConfig::RADIUS_DEFAULT
        );
        $this->assertLessThanOrEqual(
            PlaceSearchConfig::RADIUS_MAX,
            PlaceSearchConfig::RADIUS_DEFAULT
        );
    }

    public function test_radius_step_is_positive(): void
    {
        // Assert
        $this->assertGreaterThan(0, PlaceSearchConfig::RADIUS_STEP);
    }

    // ========================================
    // Tests des constantes de modes
    // ========================================

    public function test_search_modes_contains_expected_values(): void
    {
        // Assert
        $this->assertIsArray(PlaceSearchConfig::SEARCH_MODES);
        $this->assertContains('proximity', PlaceSearchConfig::SEARCH_MODES);
        $this->assertContains('worldwide', PlaceSearchConfig::SEARCH_MODES);
        $this->assertCount(2, PlaceSearchConfig::SEARCH_MODES);
    }

    public function test_search_mode_default_is_valid(): void
    {
        // Assert
        $this->assertContains(
            PlaceSearchConfig::SEARCH_MODE_DEFAULT,
            PlaceSearchConfig::SEARCH_MODES
        );
    }

    // ========================================
    // Tests des constantes de tags
    // ========================================

    public function test_tags_max_is_positive_and_reasonable(): void
    {
        // Assert
        $this->assertEquals(10, PlaceSearchConfig::TAGS_MAX);
        $this->assertGreaterThan(0, PlaceSearchConfig::TAGS_MAX);
        $this->assertLessThanOrEqual(20, PlaceSearchConfig::TAGS_MAX); // Limite raisonnable
    }

    // ========================================
    // Tests des constantes de pagination
    // ========================================

    public function test_pagination_constants_are_defined_correctly(): void
    {
        // Assert
        $this->assertEquals(30, PlaceSearchConfig::ITEMS_PER_PAGE);
        $this->assertEquals(1, PlaceSearchConfig::PAGE_DEFAULT);
    }

    public function test_items_per_page_is_positive(): void
    {
        // Assert
        $this->assertGreaterThan(0, PlaceSearchConfig::ITEMS_PER_PAGE);
    }

    // ========================================
    // Tests des constantes de limites système
    // ========================================

    public function test_system_limits_constants_are_defined_correctly(): void
    {
        // Assert
        $this->assertEquals(100_000, PlaceSearchConfig::MAX_MAP_COORDINATES);
        $this->assertEquals(10, PlaceSearchConfig::TAG_SEARCH_LIMIT);
        $this->assertEquals(300, PlaceSearchConfig::CACHE_TTL);
    }

    public function test_max_map_coordinates_is_positive_and_high(): void
    {
        // Assert
        $this->assertGreaterThan(0, PlaceSearchConfig::MAX_MAP_COORDINATES);
        $this->assertGreaterThanOrEqual(10_000, PlaceSearchConfig::MAX_MAP_COORDINATES);
    }

    public function test_cache_ttl_is_positive(): void
    {
        // Assert
        $this->assertGreaterThan(0, PlaceSearchConfig::CACHE_TTL);
    }

    // ========================================
    // Tests des méthodes helper
    // ========================================

    public function test_meters_to_km_converts_correctly(): void
    {
        // Assert
        $this->assertEquals(200, PlaceSearchConfig::metersToKm(200_000));
        $this->assertEquals(1500, PlaceSearchConfig::metersToKm(1_500_000));
        $this->assertEquals(2500, PlaceSearchConfig::metersToKm(2_500_000));
        $this->assertEquals(10, PlaceSearchConfig::metersToKm(10_000));
        $this->assertEquals(0, PlaceSearchConfig::metersToKm(0));
    }

    public function test_km_to_meters_converts_correctly(): void
    {
        // Assert
        $this->assertEquals(200_000, PlaceSearchConfig::kmToMeters(200));
        $this->assertEquals(1_500_000, PlaceSearchConfig::kmToMeters(1500));
        $this->assertEquals(2_500_000, PlaceSearchConfig::kmToMeters(2500));
        $this->assertEquals(10_000, PlaceSearchConfig::kmToMeters(10));
        $this->assertEquals(0, PlaceSearchConfig::kmToMeters(0));
    }

    public function test_meters_to_km_and_km_to_meters_are_inverse_operations(): void
    {
        // Act & Assert - Test avec plusieurs valeurs
        $valuesInMeters = [200_000, 500_000, 1_000_000, 2_500_000];

        foreach ($valuesInMeters as $meters) {
            $km = PlaceSearchConfig::metersToKm($meters);
            $backToMeters = PlaceSearchConfig::kmToMeters($km);
            $this->assertEquals($meters, $backToMeters);
        }
    }

    // ========================================
    // Tests de getJsConfig()
    // ========================================

    public function test_get_js_config_returns_complete_structure(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();

        // Assert - Structure principale
        $this->assertIsArray($config);
        $this->assertArrayHasKey('radius', $config);
        $this->assertArrayHasKey('modes', $config);
        $this->assertArrayHasKey('tags', $config);
        $this->assertArrayHasKey('pagination', $config);
        $this->assertArrayHasKey('map', $config);
    }

    public function test_get_js_config_radius_contains_all_required_keys(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();
        $radius = $config['radius'];

        // Assert - Valeurs en mètres
        $this->assertArrayHasKey('min', $radius);
        $this->assertArrayHasKey('max', $radius);
        $this->assertArrayHasKey('default', $radius);
        $this->assertArrayHasKey('step', $radius);

        // Assert - Valeurs en kilomètres
        $this->assertArrayHasKey('minKm', $radius);
        $this->assertArrayHasKey('maxKm', $radius);
        $this->assertArrayHasKey('defaultKm', $radius);
        $this->assertArrayHasKey('stepKm', $radius);
    }

    public function test_get_js_config_radius_conversions_are_correct(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();
        $radius = $config['radius'];

        // Assert - Conversions en kilomètres
        $this->assertEquals(200, $radius['minKm']);
        $this->assertEquals(2500, $radius['maxKm']);
        $this->assertEquals(200, $radius['defaultKm']);
        $this->assertEquals(10, $radius['stepKm']);

        // Assert - Valeurs en mètres correspondent
        $this->assertEquals($radius['min'] / 1000, $radius['minKm']);
        $this->assertEquals($radius['max'] / 1000, $radius['maxKm']);
    }

    public function test_get_js_config_modes_contains_all_required_keys(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();
        $modes = $config['modes'];

        // Assert
        $this->assertArrayHasKey('allowed', $modes);
        $this->assertArrayHasKey('default', $modes);
        $this->assertIsArray($modes['allowed']);
        $this->assertContains('proximity', $modes['allowed']);
        $this->assertContains('worldwide', $modes['allowed']);
        $this->assertEquals('proximity', $modes['default']);
    }

    public function test_get_js_config_tags_contains_max(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();

        // Assert
        $this->assertArrayHasKey('max', $config['tags']);
        $this->assertEquals(10, $config['tags']['max']);
    }

    public function test_get_js_config_pagination_contains_items_per_page(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();

        // Assert
        $this->assertArrayHasKey('itemsPerPage', $config['pagination']);
        $this->assertEquals(30, $config['pagination']['itemsPerPage']);
    }

    public function test_get_js_config_map_contains_all_required_keys(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();
        $map = $config['map'];

        // Assert
        $this->assertArrayHasKey('coordinates', $map);
        $this->assertArrayHasKey('boundingBox', $map);
        $this->assertArrayHasKey('useBoundingBox', $map);
    }

    public function test_get_js_config_synchronizes_with_map_config(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();

        // Assert - Valeurs doivent correspondre à config/map.php
        $this->assertEquals(config('map.coordinates'), $config['map']['coordinates']);
        $this->assertEquals(config('map.default_bounding_box'), $config['map']['boundingBox']);
        $this->assertEquals(config('map.use_bounding_box'), $config['map']['useBoundingBox']);
    }

    public function test_get_js_config_is_json_serializable(): void
    {
        // Act
        $config = PlaceSearchConfig::getJsConfig();
        $json = json_encode($config);

        // Assert
        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertEquals($config, $decoded);
    }
}
