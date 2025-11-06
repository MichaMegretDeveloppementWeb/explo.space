<?php

namespace Tests\Livewire\Admin\Place;

use App\Livewire\Admin\Place\PlaceTranslationViewer;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceTranslationViewerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_component_mounts_with_french_translation_selected_by_default(): void
    {
        // Arrange
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        // Act
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place]);

        // Assert
        $component->assertSet('selectedLocale', 'fr');
    }

    public function test_component_mounts_with_first_translation_when_french_not_available(): void
    {
        // Arrange
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'es',
            'title' => 'Título Español',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        // Act
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place]);

        // Assert
        // Devrait sélectionner la première traduction disponible
        $firstTranslation = $place->translations->first();
        $component->assertSet('selectedLocale', $firstTranslation->locale);
    }

    public function test_component_handles_place_with_no_translations(): void
    {
        // Arrange
        $place = Place::factory()->create();
        // Aucune traduction créée

        // Act
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place]);

        // Assert
        // Devrait garder la valeur par défaut 'fr' même si aucune traduction n'existe
        $component->assertSet('selectedLocale', 'fr');
    }

    public function test_select_locale_changes_selected_locale(): void
    {
        // Arrange
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place])
            ->assertSet('selectedLocale', 'fr');

        // Act
        $component->call('selectLocale', 'en');

        // Assert
        $component->assertSet('selectedLocale', 'en');
    }

    public function test_render_returns_selected_translation(): void
    {
        // Arrange
        $place = Place::factory()->create();

        $frTranslation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        $enTranslation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        // Act
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place])
            ->assertSet('selectedLocale', 'fr');

        // Assert
        $component->assertViewHas('selectedTranslation', function ($translation) use ($frTranslation) {
            return $translation->id === $frTranslation->id
                && $translation->title === 'Titre Français';
        });
    }

    public function test_render_sorts_translations_with_french_first(): void
    {
        // Arrange
        $place = Place::factory()->create();

        // Créer dans un ordre différent pour tester le tri
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'es',
            'title' => 'Título Español',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'de',
            'title' => 'Deutscher Titel',
        ]);

        // Act
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place]);

        // Assert
        $component->assertViewHas('translations', function ($translations) {
            // FR doit être en premier
            if ($translations->first()->locale !== 'fr') {
                return false;
            }

            // Les autres doivent être triés alphabétiquement
            $otherLocales = $translations->skip(1)->pluck('locale')->toArray();
            $sortedOtherLocales = $otherLocales;
            sort($sortedOtherLocales);

            return $otherLocales === $sortedOtherLocales;
        });
    }

    public function test_render_with_invalid_selected_locale_returns_null_translation(): void
    {
        // Arrange
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        // Act - Forcer une locale invalide
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place])
            ->set('selectedLocale', 'invalid_locale');

        // Assert
        $component->assertViewHas('selectedTranslation', null);
    }

    public function test_component_updates_view_when_locale_changed(): void
    {
        // Arrange
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        // Act & Assert
        Livewire::test(PlaceTranslationViewer::class, ['place' => $place])
            ->assertSet('selectedLocale', 'fr')
            ->assertViewHas('selectedTranslation', function ($translation) {
                return $translation->title === 'Titre Français';
            })
            ->call('selectLocale', 'en')
            ->assertSet('selectedLocale', 'en')
            ->assertViewHas('selectedTranslation', function ($translation) {
                return $translation->title === 'English Title';
            });
    }

    public function test_component_preserves_place_data(): void
    {
        // Arrange
        $place = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre Français',
        ]);

        // Act
        $component = Livewire::test(PlaceTranslationViewer::class, ['place' => $place]);

        // Assert
        $component->assertSet('place.latitude', 48.8566);
        $component->assertSet('place.longitude', 2.3522);
    }
}
