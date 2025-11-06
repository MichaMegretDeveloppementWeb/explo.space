<?php

namespace Tests\Livewire\Admin\Place\Store;

use App\Livewire\Admin\Place\Store\PlaceStoreForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceStoreFormTranslationsTest extends TestCase
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
    // Translation Management Tests
    // ========================================

    public function test_slug_is_auto_generated_when_title_changes(): void
    {
        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('translations.fr.title', 'Centre Spatial Kennedy');

        $translations = $component->get('translations');

        $this->assertEquals('centre-spatial-kennedy', $translations['fr']['slug']);
    }

    public function test_slug_is_regenerated_when_title_changes_after_manual_edit(): void
    {
        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('translations.fr.title', 'Premier Titre')
            ->set('translations.fr.slug', 'slug-personnalise')
            ->set('translations.fr.title', 'Deuxième Titre');

        $translations = $component->get('translations');

        // Le slug doit être régénéré depuis le nouveau titre
        $this->assertEquals('deuxieme-titre', $translations['fr']['slug']);
    }

    public function test_first_error_tab_property_exists(): void
    {
        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null]);

        // Vérifier que la propriété existe et est initialisée
        $this->assertNull($component->get('firstErrorTab'));
    }
}
