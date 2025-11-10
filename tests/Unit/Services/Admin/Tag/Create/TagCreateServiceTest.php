<?php

namespace Tests\Unit\Services\Admin\Tag\Create;

use App\Models\Tag;
use App\Models\User;
use App\Services\Admin\Tag\Create\TagCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pour TagCreateService
 *
 * Ce service gère la création complète d'un tag avec :
 * - Tag de base (color, is_active)
 * - Traductions (FR/EN)
 * - Transaction atomique
 */
class TagCreateServiceTest extends TestCase
{
    use RefreshDatabase;

    private TagCreateService $service;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        // Use real repository for integration testing
        $this->service = app(TagCreateService::class);
    }

    // ========================================
    // Tests création de base
    // ========================================

    public function test_create_basic_tag_with_required_fields_only(): void
    {
        // Arrange
        $data = [
            'color' => '#3B82F6',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Fusées',
                    'slug' => 'fusees',
                    'description' => null,
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals('#3B82F6', $tag->color);
        $this->assertTrue($tag->is_active);
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    public function test_create_tag_with_multiple_translations(): void
    {
        // Arrange
        $data = [
            'color' => '#EF4444',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Stations spatiales',
                    'slug' => 'stations-spatiales',
                    'description' => 'ISS, stations orbitales',
                ],
                'en' => [
                    'name' => 'Space Stations',
                    'slug' => 'space-stations',
                    'description' => 'ISS, orbital stations',
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Stations spatiales',
            'slug' => 'stations-spatiales',
        ]);

        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Space Stations',
            'slug' => 'space-stations',
        ]);
    }

    public function test_create_tag_with_description(): void
    {
        // Arrange
        $data = [
            'color' => '#10B981',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Musées spatiaux',
                    'slug' => 'musees-spatiaux',
                    'description' => 'Musées dédiés à l\'exploration spatiale et à l\'astronomie',
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'description' => 'Musées dédiés à l\'exploration spatiale et à l\'astronomie',
        ]);
    }

    public function test_create_inactive_tag(): void
    {
        // Arrange
        $data = [
            'color' => '#9CA3AF',
            'is_active' => false,
            'translations' => [
                'fr' => [
                    'name' => 'Tag inactif',
                    'slug' => 'tag-inactif',
                    'description' => null,
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertFalse($tag->is_active);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'is_active' => false,
        ]);
    }

    // ========================================
    // Tests slug auto-generation
    // ========================================

    public function test_create_tag_with_auto_generated_slug_if_missing(): void
    {
        // Arrange
        $data = [
            'color' => '#F59E0B',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Observatoires',
                    'slug' => '', // Empty slug should trigger auto-generation
                    'description' => null,
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'observatoires',
        ]);
    }

    public function test_create_tag_preserves_custom_slug(): void
    {
        // Arrange
        $data = [
            'color' => '#8B5CF6',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Centres de lancement',
                    'slug' => 'custom-launch-centers', // Custom slug should be preserved
                    'description' => null,
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'slug' => 'custom-launch-centers',
        ]);
    }

    // ========================================
    // Tests couleurs
    // ========================================

    public function test_create_tag_with_hex_color(): void
    {
        // Arrange
        $data = [
            'color' => '#FF5733',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Tag coloré',
                    'slug' => 'tag-colore',
                    'description' => null,
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertEquals('#FF5733', $tag->color);
    }

    // ========================================
    // Tests relations eager loading
    // ========================================

    public function test_create_tag_returns_tag_with_eager_loaded_translations(): void
    {
        // Arrange
        $data = [
            'color' => '#06B6D4',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Satellites',
                    'slug' => 'satellites',
                    'description' => 'Satellites artificiels',
                ],
                'en' => [
                    'name' => 'Satellites',
                    'slug' => 'satellites',
                    'description' => 'Artificial satellites',
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        $this->assertTrue($tag->relationLoaded('translations'));
        $this->assertCount(2, $tag->translations);
    }

    // ========================================
    // Tests validation implicite via repository
    // ========================================

    public function test_create_tag_with_all_supported_locales(): void
    {
        // Arrange
        $supportedLocales = config('locales.supported', ['fr', 'en']);

        $translations = [];
        foreach ($supportedLocales as $locale) {
            $translations[$locale] = [
                'name' => "Tag $locale",
                'slug' => "tag-$locale",
                'description' => "Description $locale",
            ];
        }

        $data = [
            'color' => '#14B8A6',
            'is_active' => true,
            'translations' => $translations,
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert
        foreach ($supportedLocales as $locale) {
            $this->assertDatabaseHas('tag_translations', [
                'tag_id' => $tag->id,
                'locale' => $locale,
                'name' => "Tag $locale",
            ]);
        }
    }

    // ========================================
    // Tests données complètes
    // ========================================

    public function test_create_tag_with_all_fields_populated(): void
    {
        // Arrange
        $data = [
            'color' => '#DC2626',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Agences spatiales',
                    'slug' => 'agences-spatiales',
                    'description' => 'NASA, ESA, Roscosmos, CNSA, JAXA et autres agences spatiales nationales',
                ],
                'en' => [
                    'name' => 'Space Agencies',
                    'slug' => 'space-agencies',
                    'description' => 'NASA, ESA, Roscosmos, CNSA, JAXA and other national space agencies',
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert - Base data
        $this->assertEquals('#DC2626', $tag->color);
        $this->assertTrue($tag->is_active);

        // Assert - Translations
        $this->assertCount(2, $tag->translations);

        // Assert - French translation
        $frTranslation = $tag->translations->where('locale', 'fr')->first();
        $this->assertEquals('Agences spatiales', $frTranslation->name);
        $this->assertEquals('agences-spatiales', $frTranslation->slug);
        $this->assertStringContainsString('NASA', $frTranslation->description);

        // Assert - English translation
        $enTranslation = $tag->translations->where('locale', 'en')->first();
        $this->assertEquals('Space Agencies', $enTranslation->name);
        $this->assertEquals('space-agencies', $enTranslation->slug);
        $this->assertStringContainsString('NASA', $enTranslation->description);
    }

    // ========================================
    // Tests transaction atomicité
    // ========================================

    public function test_create_tag_is_atomic(): void
    {
        // Arrange
        $initialTagCount = Tag::count();

        $data = [
            'color' => '#6366F1',
            'is_active' => true,
            'translations' => [
                'fr' => [
                    'name' => 'Test transaction',
                    'slug' => 'test-transaction',
                    'description' => null,
                ],
            ],
        ];

        // Act
        $tag = $this->service->create($data);

        // Assert - Tag and translation created together
        $this->assertEquals($initialTagCount + 1, Tag::count());
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'fr',
        ]);
    }

    // ========================================
    // Helper method
    // ========================================

    private function getBasicTagData(): array
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
