<?php

namespace Tests\Livewire\Web\Place\Index;

use App\DTO\Web\Place\PlacePreviewDTO;
use App\Livewire\Web\Place\Index\PlacePreviewModal;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Services\Web\Place\PreviewModal\PlacePreviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class PlacePreviewModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('fr');
    }

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(PlacePreviewModal::class)
            ->assertStatus(200);
    }

    public function test_component_view_exists(): void
    {
        Livewire::test(PlacePreviewModal::class)
            ->assertViewIs('livewire.web.place.index.place-preview-modal');
    }

    public function test_component_initializes_with_closed_modal(): void
    {
        Livewire::test(PlacePreviewModal::class)
            ->assertSet('isOpen', false)
            ->assertSet('place', null)
            ->assertSet('errorMessage', null)
            ->assertSet('technicalError', null);
    }

    public function test_load_place_opens_modal_with_place_data(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'slug' => 'kennedy-space-center',
            'status' => 'published',
        ]);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place->id)
            ->assertSet('isOpen', true)
            ->assertSet('errorMessage', null)
            ->assertSet('place.id', $place->id)
            ->assertSet('place.title', 'Kennedy Space Center');
    }

    public function test_load_place_resets_state_before_loading(): void
    {
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'First Place',
            'slug' => 'first-place',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Second Place',
            'slug' => 'second-place',
            'status' => 'published',
        ]);

        $component = Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place1->id)
            ->assertSet('place.title', 'First Place');

        // Load second place
        $component->call('loadPlace', $place2->id)
            ->assertSet('place.title', 'Second Place')
            ->assertSet('errorMessage', null);
    }

    public function test_load_place_handles_invalid_id_exception(): void
    {
        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 0)
            ->assertSet('isOpen', true)
            ->assertSet('place', null)
            ->assertSet('errorMessage', __('web/pages/explore.place_preview.error_invalid_id'));
    }

    public function test_load_place_handles_place_not_found_exception(): void
    {
        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 999)
            ->assertSet('isOpen', true)
            ->assertSet('place', null)
            ->assertSet('errorMessage', __('web/pages/explore.place_preview.error_not_found'));
    }

    public function test_load_place_handles_translation_not_found_exception(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en', // Only English
            'status' => 'published',
        ]);

        app()->setLocale('fr'); // Request in French

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place->id)
            ->assertSet('isOpen', true)
            ->assertSet('place', null)
            ->assertSet('errorMessage', __('web/pages/explore.place_preview.error_translation_missing'));
    }

    public function test_load_place_handles_generic_exception(): void
    {
        $mockService = Mockery::mock(PlacePreviewService::class);
        $mockService->shouldReceive('getPlacePreviewById')
            ->once()
            ->andThrow(new \RuntimeException('Database error'));

        $this->app->instance(PlacePreviewService::class, $mockService);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 1)
            ->assertSet('isOpen', true)
            ->assertSet('place', null)
            ->assertSet('errorMessage', __('web/pages/explore.place_preview.error_loading'));
    }

    public function test_technical_error_shown_in_debug_mode(): void
    {
        config(['app.debug' => true]);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 999)
            ->assertSet('technicalError', 'App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException: Place with ID 999 not found in database for preview modal');
    }

    public function test_technical_error_hidden_in_production_mode(): void
    {
        config(['app.debug' => false]);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 999)
            ->assertSet('technicalError', null);
    }

    public function test_close_modal_resets_all_state(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place->id)
            ->assertSet('isOpen', true)
            ->assertNotNull('place')
            ->call('closeModal')
            ->assertSet('isOpen', false)
            ->assertSet('place', null)
            ->assertSet('errorMessage', null);
    }

    public function test_marker_clicked_event_triggers_load_place(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Test Place',
            'slug' => 'test-place',
            'status' => 'published',
        ]);

        Livewire::test(PlacePreviewModal::class)
            ->dispatch('marker-clicked', placeId: $place->id)
            ->assertSet('isOpen', true)
            ->assertSet('place.title', 'Test Place');
    }

    public function test_place_dto_is_wireable(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Test Wireable',
            'slug' => 'test-wireable',
            'status' => 'published',
        ]);

        $component = Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place->id);

        // Ensure DTO is properly serialized/deserialized
        $placeData = $component->get('place');
        $this->assertInstanceOf(PlacePreviewDTO::class, $placeData);
        $this->assertEquals('Test Wireable', $placeData->title);
    }

    public function test_multiple_errors_logged_correctly(): void
    {
        \Illuminate\Support\Facades\Log::spy();

        // Invalid ID
        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', -1);

        \Illuminate\Support\Facades\Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Error loading place preview modal'
                    && $context['place_id'] === -1
                    && $context['exception_type'] === 'InvalidArgumentException';
            });

        // Place not found
        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 999);

        \Illuminate\Support\Facades\Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Error loading place preview modal'
                    && $context['place_id'] === 999
                    && $context['exception_type'] === 'App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException';
            });
    }

    public function test_error_state_persists_until_next_load(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $component = Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', 999)
            ->assertSet('errorMessage', __('web/pages/explore.place_preview.error_not_found'));

        // Load valid place should clear error
        $component->call('loadPlace', $place->id)
            ->assertSet('errorMessage', null)
            ->assertSet('isOpen', true);
    }

    public function test_component_handles_rapid_successive_calls(): void
    {
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Place 1',
            'slug' => 'place-1',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Place 2',
            'slug' => 'place-2',
            'status' => 'published',
        ]);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place1->id)
            ->call('loadPlace', $place2->id)
            ->assertSet('place.title', 'Place 2');
    }

    public function test_view_receives_correct_data(): void
    {
        $place = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'View Test Place',
            'slug' => 'view-test-place',
            'description' => 'Test description',
            'status' => 'published',
        ]);

        Livewire::test(PlacePreviewModal::class)
            ->call('loadPlace', $place->id)
            ->assertViewHas('isOpen', true)
            ->assertViewHas('place')
            ->assertViewHas('errorMessage', null);
    }
}
