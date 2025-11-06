<?php

namespace Tests\Unit\Repositories\Admin\Category;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Repositories\Admin\Category\CategorySelectionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySelectionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CategorySelectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new CategorySelectionRepository;
    }

    // ========================================
    // Get All Tests
    // ========================================

    public function test_get_all_returns_active_categories_only(): void
    {
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        CategoryTranslation::factory()->create([
            'category_id' => $activeCategory->id,
            'locale' => 'fr',
            'name' => 'Catégorie Active',
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $inactiveCategory->id,
            'locale' => 'fr',
            'name' => 'Catégorie Inactive',
        ]);

        $result = $this->repository->getAll();

        $this->assertCount(1, $result);
        $this->assertEquals($activeCategory->id, $result->first()->id);
    }

    public function test_get_all_eager_loads_translations(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        CategoryTranslation::factory()->create([
            'category_id' => $category->id,
            'locale' => 'fr',
            'name' => 'Catégorie FR',
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $category->id,
            'locale' => 'en',
            'name' => 'Category EN',
        ]);

        $result = $this->repository->getAll();

        $this->assertTrue($result->first()->relationLoaded('translations'));
        $this->assertCount(2, $result->first()->translations);
    }

    public function test_get_all_sorts_by_first_translation_name(): void
    {
        // Create categories with FR translations in non-alphabetical order
        $category1 = Category::factory()->create(['is_active' => true]);
        CategoryTranslation::factory()->create([
            'category_id' => $category1->id,
            'locale' => 'fr',
            'name' => 'Zénith',
        ]);

        $category2 = Category::factory()->create(['is_active' => true]);
        CategoryTranslation::factory()->create([
            'category_id' => $category2->id,
            'locale' => 'fr',
            'name' => 'Alpha',
        ]);

        $category3 = Category::factory()->create(['is_active' => true]);
        CategoryTranslation::factory()->create([
            'category_id' => $category3->id,
            'locale' => 'fr',
            'name' => 'Bravo',
        ]);

        $result = $this->repository->getAll();

        $names = $result->map(fn ($cat) => $cat->translations->first()->name)->toArray();

        $this->assertEquals(['Alpha', 'Bravo', 'Zénith'], $names);
    }

    public function test_get_all_handles_categories_without_translations(): void
    {
        $categoryWithTranslation = Category::factory()->create(['is_active' => true]);
        CategoryTranslation::factory()->create([
            'category_id' => $categoryWithTranslation->id,
            'locale' => 'fr',
            'name' => 'Catégorie',
        ]);

        $categoryWithoutTranslation = Category::factory()->create(['is_active' => true]);

        $result = $this->repository->getAll();

        // Should return both, but the one without translation should be sorted last (empty name)
        $this->assertCount(2, $result);
    }

    public function test_get_all_returns_empty_collection_when_no_active_categories(): void
    {
        Category::factory()->create(['is_active' => false]);

        $result = $this->repository->getAll();

        $this->assertCount(0, $result);
    }

    public function test_get_all_sorts_translations_by_locale(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        // Create in reverse order
        CategoryTranslation::factory()->create([
            'category_id' => $category->id,
            'locale' => 'fr',
            'name' => 'Catégorie FR',
        ]);

        CategoryTranslation::factory()->create([
            'category_id' => $category->id,
            'locale' => 'en',
            'name' => 'Category EN',
        ]);

        $result = $this->repository->getAll();

        $locales = $result->first()->translations->pluck('locale')->toArray();

        // Translations should be ordered by locale
        $this->assertEquals(['en', 'fr'], $locales);
    }
}
