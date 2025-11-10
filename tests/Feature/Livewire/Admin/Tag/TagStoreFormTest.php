<?php

namespace Tests\Feature\Livewire\Admin\Tag;

use App\Livewire\Admin\Tag\Store\TagStoreForm;
use App\Models\Place;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests pour le composant Livewire TagStoreForm
 *
 * Ce composant gère :
 * - Création de tags
 * - Édition de tags
 * - Traduction automatique (DeepL)
 * - Suppression de tags
 * - Validation
 */
class TagStoreFormTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    // ========================================
    // Tests rendu composant
    // ========================================

    public function test_component_renders_in_create_mode(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->assertStatus(200)
            ->assertSet('mode', 'create')
            ->assertSet('color', '#3B82F6')
            ->assertSet('is_active', true);
    }

    public function test_component_renders_in_edit_mode(): void
    {
        // Arrange
        $tag = Tag::factory()->create(['color' => '#EF4444']);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Test Tag',
        ]);

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->assertStatus(200)
            ->assertSet('mode', 'edit')
            ->assertSet('color', '#EF4444')
            ->assertSet('translations.fr.name', 'Test Tag');
    }

    public function test_component_initializes_empty_translations(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->assertSet('translations.fr.name', '')
            ->assertSet('translations.fr.slug', '')
            ->assertSet('translations.fr.description', null)
            ->assertSet('translations.en.name', '')
            ->assertSet('translations.en.slug', '')
            ->assertSet('translations.en.description', null);
    }

    // ========================================
    // Tests création
    // ========================================

    public function test_can_create_tag_with_basic_data(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('color', '#10B981')
            ->set('is_active', true)
            ->set('translations.fr.name', 'Nouveau Tag')
            ->set('translations.fr.slug', 'nouveau-tag')
            ->set('translations.en.name', 'New Tag')
            ->set('translations.en.slug', 'new-tag')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('tags', [
            'color' => '#10B981',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('tag_translations', [
            'locale' => 'fr',
            'name' => 'Nouveau Tag',
            'slug' => 'nouveau-tag',
        ]);
    }

    public function test_can_create_tag_with_multiple_translations(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('color', '#F59E0B')
            ->set('is_active', true)
            ->set('translations.fr.name', 'Fusées')
            ->set('translations.fr.slug', 'fusees')
            ->set('translations.fr.description', 'Description FR')
            ->set('translations.en.name', 'Rockets')
            ->set('translations.en.slug', 'rockets')
            ->set('translations.en.description', 'Description EN')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tag_translations', [
            'locale' => 'fr',
            'name' => 'Fusées',
        ]);

        $this->assertDatabaseHas('tag_translations', [
            'locale' => 'en',
            'name' => 'Rockets',
        ]);
    }

    public function test_create_tag_redirects_to_edit_page(): void
    {
        $component = Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('color', '#8B5CF6')
            ->set('is_active', true)
            ->set('translations.fr.name', 'Test Redirect')
            ->set('translations.fr.slug', 'test-redirect')
            ->set('translations.en.name', 'Test Redirect EN')
            ->set('translations.en.slug', 'test-redirect-en')
            ->call('save');

        // Get the created tag
        $tag = Tag::where('color', '#8B5CF6')->first();
        $this->assertNotNull($tag);

        // Assert redirected to edit page
        $component->assertRedirect(route('admin.tags.edit', $tag->id));
    }

    // ========================================
    // Tests mise à jour
    // ========================================

    public function test_can_update_tag_color(): void
    {
        // Arrange
        $tag = $this->createBasicTag();

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->set('color', '#DC2626')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'color' => '#DC2626',
        ]);
    }

    public function test_can_update_tag_is_active(): void
    {
        // Arrange
        $tag = $this->createBasicTag(['is_active' => true]);

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->set('is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'is_active' => false,
        ]);
    }

    public function test_can_update_tag_translation(): void
    {
        // Arrange
        $tag = $this->createBasicTag();

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->set('translations.fr.name', 'Nom modifié')
            ->set('translations.fr.slug', 'nom-modifie')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Nom modifié',
        ]);
    }

    public function test_update_tag_stays_on_same_page(): void
    {
        // Arrange
        $tag = $this->createBasicTag();

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->set('translations.fr.name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('translations.fr.name', 'Updated Name');
    }

    // ========================================
    // Tests validation
    // ========================================

    public function test_validates_required_name_in_french(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', '')
            ->set('translations.fr.slug', 'test-slug')
            ->call('save')
            ->assertHasErrors(['translations.fr.name']);
    }

    public function test_validates_required_slug_in_french(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'Test Name')
            ->set('translations.fr.slug', '')
            ->call('save')
            ->assertHasErrors(['translations.fr.slug']);
    }

    public function test_validates_unique_slug_per_locale(): void
    {
        // Arrange - Create a tag with a specific slug
        $existingTag = Tag::factory()->create();
        TagTranslation::factory()->create([
            'tag_id' => $existingTag->id,
            'locale' => 'fr',
            'slug' => 'existing-slug',
        ]);
        TagTranslation::factory()->create([
            'tag_id' => $existingTag->id,
            'locale' => 'en',
            'slug' => 'existing-slug-en',
        ]);

        // Act & Assert - Try to create a new tag with the same slug
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'Autre Tag')
            ->set('translations.fr.slug', 'existing-slug')
            ->set('translations.en.name', 'Another Tag')
            ->set('translations.en.slug', 'another-tag')
            ->call('save')
            ->assertHasErrors(['translations.fr.slug']);
    }

    public function test_validates_color_format(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('color', 'invalid-color')
            ->set('translations.fr.name', 'Test')
            ->set('translations.fr.slug', 'test')
            ->call('save')
            ->assertHasErrors(['color']);
    }

    public function test_validates_description_max_length(): void
    {
        $longDescription = str_repeat('a', 2001);

        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'Test')
            ->set('translations.fr.slug', 'test')
            ->set('translations.fr.description', $longDescription)
            ->call('save')
            ->assertHasErrors(['translations.fr.description']);
    }

    public function test_validation_switches_to_first_error_tab(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('activeTranslationTab', 'fr')
            ->set('translations.fr.name', 'Test FR')
            ->set('translations.fr.slug', 'test-fr')
            ->set('translations.en.name', '') // Error in EN tab
            ->set('translations.en.slug', 'test')
            ->call('save')
            ->assertSet('activeTranslationTab', 'en');
    }

    // ========================================
    // Tests slug auto-generation
    // ========================================

    public function test_auto_generates_slug_from_name(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'Test Auto Slug')
            ->assertSet('translations.fr.slug', 'test-auto-slug');
    }

    public function test_slug_updates_when_name_changes(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'First Name')
            ->assertSet('translations.fr.slug', 'first-name')
            ->set('translations.fr.name', 'Second Name')
            ->assertSet('translations.fr.slug', 'second-name');
    }

    // ========================================
    // Tests suppression
    // ========================================

    public function test_can_delete_tag(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $tagId = $tag->id;

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tagId])
            ->call('confirmDeleteModal')
            ->assertSet('showDeleteModal', true)
            ->call('delete')
            ->assertRedirect();

        $this->assertDatabaseMissing('tags', ['id' => $tagId]);
    }

    public function test_delete_shows_associated_places_count(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $place1 = Place::factory()->create();
        $place2 = Place::factory()->create();
        $tag->places()->attach([$place1->id, $place2->id]);

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->call('confirmDeleteModal')
            ->assertSet('associatedPlacesCount', 2);
    }

    public function test_delete_detaches_places_before_deletion(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $place = Place::factory()->create();
        $tag->places()->attach($place->id);

        $tagId = $tag->id;

        // Act
        Livewire::test(TagStoreForm::class, ['tagId' => $tagId])
            ->call('confirmDeleteModal')
            ->call('delete');

        // Assert
        $this->assertDatabaseMissing('place_tag', ['tag_id' => $tagId]);
    }

    public function test_can_cancel_delete_modal(): void
    {
        // Arrange
        $tag = $this->createBasicTag();

        // Act & Assert
        Livewire::test(TagStoreForm::class, ['tagId' => $tag->id])
            ->call('confirmDeleteModal')
            ->assertSet('showDeleteModal', true)
            ->call('cancelDelete')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    // ========================================
    // Tests traduction automatique
    // ========================================

    public function test_initiate_translation_shows_confirmation_modal_if_fields_exist(): void
    {
        // Mock the translation service
        $mockTranslation = $this->mock(\App\Contracts\Translation\TranslationStrategyInterface::class);
        $mockTranslation->shouldReceive('checkUsage')->once();
        $mockTranslation->shouldReceive('translateBatch')
            ->once()
            ->with(['name' => 'Source Name', 'description' => 'Source Description'], 'fr', 'en')
            ->andReturn([
                'name' => 'Translated Name',
                'description' => 'Translated Description',
            ]);

        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'Source Name')
            ->set('translations.fr.description', 'Source Description')
            ->set('translations.en.name', 'Existing Name')
            ->call('initiateTranslation', 'fr')
            ->assertSet('showTranslationConfirmation', true);
    }

    public function test_can_cancel_translation(): void
    {
        // Mock the translation service
        $mockTranslation = $this->mock(\App\Contracts\Translation\TranslationStrategyInterface::class);
        $mockTranslation->shouldReceive('checkUsage')->once();
        $mockTranslation->shouldReceive('translateBatch')
            ->once()
            ->with(['name' => 'Test'], 'fr', 'en')
            ->andReturn(['name' => 'Test Translated']);

        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->set('translations.fr.name', 'Test')
            ->set('translations.en.name', 'Existing Name') // Pre-fill EN to trigger modal
            ->call('initiateTranslation', 'fr')
            ->assertSet('showTranslationConfirmation', true)
            ->call('cancelTranslation')
            ->assertSet('showTranslationConfirmation', false);
    }

    // ========================================
    // Tests gestion d'onglets
    // ========================================

    public function test_can_switch_translation_tabs(): void
    {
        Livewire::test(TagStoreForm::class, ['tagId' => null])
            ->assertSet('activeTranslationTab', 'fr')
            ->set('activeTranslationTab', 'en')
            ->assertSet('activeTranslationTab', 'en');
    }

    // ========================================
    // Helper methods
    // ========================================

    private function createBasicTag(array $attributes = []): Tag
    {
        $tag = Tag::factory()->create(array_merge([
            'color' => '#3B82F6',
            'is_active' => true,
        ], $attributes));

        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Tag de test',
            'slug' => 'tag-de-test',
        ]);

        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ]);

        return $tag->fresh('translations');
    }
}
