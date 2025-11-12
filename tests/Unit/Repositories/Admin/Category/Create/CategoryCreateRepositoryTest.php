<?php

namespace Tests\Unit\Repositories\Admin\Category\Create;

use App\Models\Category;
use App\Repositories\Admin\Category\Create\CategoryCreateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCreateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CategoryCreateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new CategoryCreateRepository;
    }

    public function test_create_inserts_category_with_all_fields(): void
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#3B82F6',
            'is_active' => true,
        ];

        $category = $this->repository->create($categoryData);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertEquals('Test description', $category->description);
        $this->assertEquals('#3B82F6', $category->color);
        $this->assertTrue($category->is_active);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_create_sets_is_active_to_true_by_default(): void
    {
        $categoryData = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#3B82F6',
        ];

        $category = $this->repository->create($categoryData);

        $this->assertTrue($category->is_active);
    }

    public function test_create_accepts_null_description(): void
    {
        $categoryData = [
            'name' => 'Test',
            'slug' => 'test',
            'description' => null,
            'color' => '#3B82F6',
            'is_active' => true,
        ];

        $category = $this->repository->create($categoryData);

        $this->assertNull($category->description);
    }

    public function test_create_returns_persisted_model_with_id(): void
    {
        $categoryData = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#3B82F6',
            'is_active' => true,
        ];

        $category = $this->repository->create($categoryData);

        $this->assertNotNull($category->id);
        $this->assertTrue($category->exists);
    }

    public function test_create_sets_timestamps(): void
    {
        $categoryData = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#3B82F6',
            'is_active' => true,
        ];

        $category = $this->repository->create($categoryData);

        $this->assertNotNull($category->created_at);
        $this->assertNotNull($category->updated_at);
    }
}
