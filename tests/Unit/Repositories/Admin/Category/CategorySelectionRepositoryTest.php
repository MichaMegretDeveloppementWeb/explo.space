<?php

namespace Tests\Unit\Repositories\Admin\Category;

use App\Models\Category;
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
        $activeCategory = Category::factory()->create([
            'is_active' => true,
            'name' => 'Catégorie Active',
        ]);

        $inactiveCategory = Category::factory()->create([
            'is_active' => false,
            'name' => 'Catégorie Inactive',
        ]);

        $result = $this->repository->getAll();

        $this->assertCount(1, $result);
        $this->assertEquals($activeCategory->id, $result->first()->id);
    }

    public function test_get_all_sorts_by_name(): void
    {
        // Create categories in non-alphabetical order
        $category1 = Category::factory()->create([
            'is_active' => true,
            'name' => 'Zénith',
        ]);

        $category2 = Category::factory()->create([
            'is_active' => true,
            'name' => 'Alpha',
        ]);

        $category3 = Category::factory()->create([
            'is_active' => true,
            'name' => 'Bravo',
        ]);

        $result = $this->repository->getAll();

        $names = $result->pluck('name')->toArray();

        $this->assertEquals(['Alpha', 'Bravo', 'Zénith'], $names);
    }

    public function test_get_all_returns_empty_collection_when_no_active_categories(): void
    {
        Category::factory()->create(['is_active' => false, 'name' => 'Inactive']);

        $result = $this->repository->getAll();

        $this->assertCount(0, $result);
    }

    public function test_get_all_returns_all_active_categories(): void
    {
        Category::factory()->count(5)->create(['is_active' => true]);
        Category::factory()->count(3)->create(['is_active' => false]);

        $result = $this->repository->getAll();

        $this->assertCount(5, $result);
        $this->assertTrue($result->every(fn ($cat) => $cat->is_active === true));
    }

    public function test_get_all_categories_have_required_attributes(): void
    {
        $category = Category::factory()->create([
            'is_active' => true,
            'name' => 'Test Category',
            'slug' => 'test-category',
            'color' => '#3B82F6',
        ]);

        $result = $this->repository->getAll();

        $retrieved = $result->first();

        $this->assertEquals('Test Category', $retrieved->name);
        $this->assertEquals('test-category', $retrieved->slug);
        $this->assertEquals('#3B82F6', $retrieved->color);
        $this->assertTrue($retrieved->is_active);
    }
}
