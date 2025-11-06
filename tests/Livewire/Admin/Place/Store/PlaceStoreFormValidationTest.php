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

class PlaceStoreFormValidationTest extends TestCase
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
    // Validation Tests
    // ========================================

    public function test_save_validates_required_fields(): void
    {
        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            // Laisser les coordonnées par défaut mais vider les traductions
            ->set('translations.fr.title', '')
            ->set('translations.fr.description', '')
            ->call('save')
            ->assertHasErrors([
                'translations.fr.title',
                'translations.fr.description',
            ]);
    }

    public function test_save_validates_latitude_range(): void
    {
        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 100) // Invalid: > 90
            ->set('longitude', 0)
            ->set('translations.fr.title', 'Test')
            ->set('translations.fr.description', 'Test')
            ->call('save')
            ->assertHasErrors(['latitude']);

        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', -100) // Invalid: < -90
            ->set('longitude', 0)
            ->set('translations.fr.title', 'Test')
            ->set('translations.fr.description', 'Test')
            ->call('save')
            ->assertHasErrors(['latitude']);
    }

    public function test_save_validates_longitude_range(): void
    {
        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 0)
            ->set('longitude', 200) // Invalid: > 180
            ->set('translations.fr.title', 'Test')
            ->set('translations.fr.description', 'Test')
            ->call('save')
            ->assertHasErrors(['longitude']);

        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 0)
            ->set('longitude', -200) // Invalid: < -180
            ->set('translations.fr.title', 'Test')
            ->set('translations.fr.description', 'Test')
            ->call('save')
            ->assertHasErrors(['longitude']);
    }

    public function test_save_validates_slug_format(): void
    {
        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 28.5728)
            ->set('longitude', -80.6490)
            ->set('translations.fr.title', 'Test')
            ->set('translations.fr.slug', 'Invalid Slug With Spaces!')  // Invalid slug
            ->set('translations.fr.description', 'Test description')
            ->call('save')
            ->assertHasErrors(['translations.fr.slug']);
    }

    public function test_save_validates_unique_slug_per_locale(): void
    {
        $place1 = Place::factory()->create();
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
        ]);

        Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('latitude', 28.5728)
            ->set('longitude', -80.6490)
            ->set('translations.fr.title', 'Test')
            ->set('translations.fr.slug', 'centre-spatial-kennedy')  // Duplicate slug
            ->set('translations.fr.description', 'Test description')
            ->call('save')
            ->assertHasErrors(['translations.fr.slug']);
    }
}
