<?php

namespace Tests\Livewire\Admin\Place\Store;

use App\Livewire\Admin\Place\Store\PlaceStoreForm;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceStoreFormInitializationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
        app()->setLocale('fr');

        Storage::fake('public');
    }

    // ========================================
    // Component Rendering & Initialization
    // ========================================

    public function test_component_renders_in_create_mode(): void
    {
        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->assertStatus(200)
            ->assertSet('mode', 'create');
    }

    public function test_component_renders_in_edit_mode(): void
    {
        $place = Place::factory()->create();

        Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->assertStatus(200)
            ->assertSet('mode', 'edit');
    }

    public function test_create_mode_initializes_with_empty_data(): void
    {
        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->assertSet('latitude', 0.0)
            ->assertSet('longitude', 0.0)
            ->assertSet('address', null)
            ->assertSet('is_featured', false)
            ->assertSet('categoryIds', [])
            ->assertSet('tagIds', [])
            ->assertSet('existingPhotos', []);
    }

    public function test_edit_mode_loads_existing_place_data(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.5728,
            'longitude' => -80.6490,
            'address' => 'Kennedy Space Center, FL 32899, USA',
            'is_featured' => true,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
        ]);

        Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->assertSet('latitude', 28.5728)
            ->assertSet('longitude', -80.6490)
            ->assertSet('address', 'Kennedy Space Center, FL 32899, USA')
            ->assertSet('is_featured', true);
    }

    public function test_edit_mode_loads_translations(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre FR',
            'slug' => 'titre-fr',
            'description' => 'Description FR',
            'practical_info' => 'Infos FR',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Title EN',
            'slug' => 'title-en',
            'description' => 'Description EN',
            'practical_info' => 'Infos EN',
            'status' => 'published',
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null]);

        $translations = $component->get('translations');

        $this->assertEquals('Titre FR', $translations['fr']['title']);
        $this->assertEquals('Title EN', $translations['en']['title']);
    }
}
