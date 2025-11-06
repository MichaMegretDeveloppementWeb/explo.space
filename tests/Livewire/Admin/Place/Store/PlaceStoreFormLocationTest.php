<?php

namespace Tests\Livewire\Admin\Place\Store;

use App\Livewire\Admin\Place\Store\PlaceStoreForm;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceStoreFormLocationTest extends TestCase
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
    // Location & Coordinate Normalization
    // ========================================

    public function test_coordinates_are_normalized_to_six_decimals_on_load(): void
    {
        $place = Place::factory()->create([
            'latitude' => 28.572839456789,  // Plus de 6 décimales
            'longitude' => -80.649012345678,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null]);

        // Doit être arrondi à 6 décimales
        $this->assertEquals(28.572839, $component->get('latitude'));
        $this->assertEquals(-80.649012, $component->get('longitude'));
    }

    // TODO: Ajouter tests pour géocodage (recherche d'adresse)
    // - test_search_address_returns_suggestions
    // - test_select_address_updates_coordinates
    // - test_reverse_geocoding_from_coordinates
}
