<?php

namespace Tests\Feature\Web\Place\Index;

use App\Contracts\Services\GeocodingServiceInterface;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use App\Services\Web\Place\Index\PlaceExplorationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExplorePlacesPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the explorer page loads successfully
     */
    public function test_explorer_page_loads_successfully(): void
    {
        $response = $this->get('/fr/explorer');

        $response->assertStatus(200);
        // Vérifier les vrais textes présents dans la page (visibles à tout moment)
        $response->assertSee('Autour de moi');
        $response->assertSee('Monde entier');
        // Note: "Rayon de recherche" is in filters section which may be collapsed initially
    }

    /**
     * Test that the English explorer page loads successfully
     */
    public function test_explorer_page_loads_in_english(): void
    {
        $response = $this->get('/en/explore');

        $response->assertStatus(200);
        $response->assertSee('Near me');
        $response->assertSee('Worldwide');
    }

    /**
     * Test that the geocoding service is properly bound
     */
    public function test_geocoding_service_binding(): void
    {
        $service = app(GeocodingServiceInterface::class);

        $this->assertInstanceOf(GeocodingServiceInterface::class, $service);
    }

    /**
     * Test that the exploration service returns correct structure for list
     */
    public function test_exploration_service_returns_list_structure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $explorationService = app(PlaceExplorationService::class);

        $filters = [
            'mode' => 'worldwide',
            'tags' => [],
        ];

        $boundingBox = [
            'north' => 85,
            'south' => -85,
            'east' => 180,
            'west' => -180,
        ];

        $result = $explorationService->getPlacesForList($filters, $boundingBox, 30);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('places', $result);
        $this->assertArrayHasKey('nextCursor', $result);
        $this->assertArrayHasKey('hasMorePages', $result);
    }

    /**
     * Test that the exploration service returns correct structure for map
     */
    public function test_exploration_service_returns_map_structure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $explorationService = app(PlaceExplorationService::class);

        $filters = [
            'mode' => 'worldwide',
            'tags' => [],
        ];

        $boundingBox = [
            'north' => 85,
            'south' => -85,
            'east' => 180,
            'west' => -180,
        ];

        $result = $explorationService->getPlacesForMap($filters, $boundingBox);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('coordinates', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertArrayHasKey('bounding_box', $result);
    }

    /**
     * Test that Livewire components are present on the page
     */
    public function test_livewire_components_present(): void
    {
        $response = $this->get('/fr/explorer');

        $response->assertStatus(200);
        // Vérifier que les composants Livewire sont chargés
        $response->assertSeeLivewire('web.place.index.place-explorer');
    }
}
