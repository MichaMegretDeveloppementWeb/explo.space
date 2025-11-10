<?php

namespace Tests\Unit\Services\Admin\Tag\Edit;

use App\Exceptions\Admin\Tag\TagNotFoundException;
use App\Models\Place;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use App\Services\Admin\Tag\Edit\TagUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pour TagUpdateService
 *
 * Ce service gère la mise à jour complète d'un tag avec :
 * - Données de base (color, is_active)
 * - Traductions (FR/EN)
 * - Suppression avec dissociation des places
 * - Transaction atomique
 * - Exceptions et gestion d'erreurs
 */
class TagUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    private TagUpdateService $service;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        // Use real repository for integration testing
        $this->service = app(TagUpdateService::class);
    }

    // ========================================
    // Tests chargement pour édition
    // ========================================

    public function test_load_for_edit_returns_tag_with_translations(): void
    {
        // Arrange
        $tag = Tag::factory()->create();
        TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'fr']);
        TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'en']);

        // Act
        $loaded = $this->service->loadForEdit($tag->id);

        // Assert
        $this->assertInstanceOf(Tag::class, $loaded);
        $this->assertTrue($loaded->relationLoaded('translations'));
        $this->assertCount(2, $loaded->translations);
    }

    public function test_load_for_edit_returns_null_if_not_found(): void
    {
        // Act
        $loaded = $this->service->loadForEdit(99999);

        // Assert
        $this->assertNull($loaded);
    }

    // ========================================
    // Tests mise à jour données de base
    // ========================================

    public function test_update_tag_color(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();
        $data['color'] = '#FF5733';

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $updated->refresh();
        $this->assertEquals('#FF5733', $updated->color);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'color' => '#FF5733',
        ]);
    }

    public function test_update_tag_is_active(): void
    {
        // Arrange
        $tag = $this->createBasicTag(['is_active' => true]);
        $data = $this->getBasicUpdateData();
        $data['is_active'] = false;

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $updated->refresh();
        $this->assertFalse($updated->is_active);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'is_active' => false,
        ]);
    }

    public function test_update_tag_color_and_is_active_together(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();
        $data['color'] = '#10B981';
        $data['is_active'] = false;

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $updated->refresh();
        $this->assertEquals('#10B981', $updated->color);
        $this->assertFalse($updated->is_active);
    }

    // ========================================
    // Tests mise à jour traductions
    // ========================================

    public function test_update_single_translation(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();
        $data['translations']['fr']['name'] = 'Nouveau nom';
        $data['translations']['fr']['slug'] = 'nouveau-nom';

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Nouveau nom',
            'slug' => 'nouveau-nom',
        ]);
    }

    public function test_update_multiple_translations(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Old English Name',
        ]);

        $data = $this->getBasicUpdateData();
        $data['translations']['fr']['name'] = 'Nouveau nom FR';
        $data['translations']['en'] = [
            'name' => 'New English Name',
            'slug' => 'new-english-name',
            'description' => 'New description',
        ];

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Nouveau nom FR',
        ]);

        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'New English Name',
            'slug' => 'new-english-name',
        ]);
    }

    public function test_update_translation_description(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();
        $data['translations']['fr']['description'] = 'Nouvelle description détaillée du tag';

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'description' => 'Nouvelle description détaillée du tag',
        ]);
    }

    public function test_update_preserves_unchanged_translations(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'English Name',
            'slug' => 'english-name',
        ]);

        $data = $this->getBasicUpdateData();
        // Only update FR, don't touch EN
        $data['translations']['fr']['name'] = 'Nom modifié';

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert - FR updated
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Nom modifié',
        ]);

        // Assert - EN preserved (not in data, so untouched)
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'English Name',
        ]);
    }

    // ========================================
    // Tests création nouvelles traductions
    // ========================================

    public function test_update_creates_new_translation_if_not_exists(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();
        $data['translations']['en'] = [
            'name' => 'New English Translation',
            'slug' => 'new-english-translation',
            'description' => null,
        ];

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'New English Translation',
        ]);
    }

    // ========================================
    // Tests exceptions
    // ========================================

    public function test_update_throws_exception_if_tag_not_found(): void
    {
        // Arrange
        $data = $this->getBasicUpdateData();

        // Act & Assert
        $this->expectException(TagNotFoundException::class);
        $this->expectExceptionMessage("Le tag que vous essayez de modifier n'existe pas.");

        $this->service->update(99999, $data);
    }

    // ========================================
    // Tests suppression
    // ========================================

    public function test_delete_removes_tag_and_translations(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'en']);

        $tagId = $tag->id;

        // Act
        $deleted = $this->service->delete($tagId);

        // Assert
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('tags', ['id' => $tagId]);
        $this->assertDatabaseMissing('tag_translations', ['tag_id' => $tagId]);
    }

    public function test_delete_detaches_places_before_deletion(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $place1 = Place::factory()->create();
        $place2 = Place::factory()->create();

        $tag->places()->attach([$place1->id, $place2->id]);

        // Verify association exists
        $this->assertDatabaseHas('place_tag', ['tag_id' => $tag->id, 'place_id' => $place1->id]);
        $this->assertDatabaseHas('place_tag', ['tag_id' => $tag->id, 'place_id' => $place2->id]);

        $tagId = $tag->id;

        // Act
        $deleted = $this->service->delete($tagId);

        // Assert
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('place_tag', ['tag_id' => $tagId]);
    }

    public function test_delete_throws_exception_if_tag_not_found(): void
    {
        // Act & Assert
        $this->expectException(TagNotFoundException::class);
        $this->expectExceptionMessage("Le tag que vous essayez de modifier n'existe pas.");

        $this->service->delete(99999);
    }

    public function test_delete_works_with_no_associated_places(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $tagId = $tag->id;

        // Act
        $deleted = $this->service->delete($tagId);

        // Assert
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('tags', ['id' => $tagId]);
    }

    // ========================================
    // Tests transaction atomicité
    // ========================================

    public function test_update_is_atomic(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();
        $data['color'] = '#EC4899';
        $data['translations']['fr']['name'] = 'Tag mis à jour';

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert - Both tag and translation updated together
        $updated->refresh();
        $this->assertEquals('#EC4899', $updated->color);
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'name' => 'Tag mis à jour',
        ]);
    }

    public function test_update_returns_tag_with_eager_loaded_translations(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        $data = $this->getBasicUpdateData();

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert
        $this->assertTrue($updated->relationLoaded('translations'));
    }

    // ========================================
    // Tests mise à jour complète
    // ========================================

    public function test_update_tag_with_all_fields(): void
    {
        // Arrange
        $tag = $this->createBasicTag();
        TagTranslation::factory()->create(['tag_id' => $tag->id, 'locale' => 'en']);

        $data = [
            'color' => '#F59E0B',
            'is_active' => false,
            'translations' => [
                'fr' => [
                    'name' => 'Tag Complet FR',
                    'slug' => 'tag-complet-fr',
                    'description' => 'Description complète en français',
                ],
                'en' => [
                    'name' => 'Complete Tag EN',
                    'slug' => 'complete-tag-en',
                    'description' => 'Full description in English',
                ],
            ],
        ];

        // Act
        $updated = $this->service->update($tag->id, $data);

        // Assert - Base data
        $updated->refresh();
        $this->assertEquals('#F59E0B', $updated->color);
        $this->assertFalse($updated->is_active);

        // Assert - FR translation
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Tag Complet FR',
            'description' => 'Description complète en français',
        ]);

        // Assert - EN translation
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Complete Tag EN',
            'description' => 'Full description in English',
        ]);
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

        return $tag->fresh('translations');
    }

    private function getBasicUpdateData(): array
    {
        return [
            'color' => '#3B82F6',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Tag de test',
                    'slug' => 'tag-de-test',
                    'description' => null,
                ],
            ],
        ];
    }
}
