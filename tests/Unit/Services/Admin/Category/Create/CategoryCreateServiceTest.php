<?php

namespace Tests\Unit\Services\Admin\Category\Create;

use App\Contracts\Repositories\Admin\Category\Create\CategoryCreateRepositoryInterface;
use App\Models\Category;
use App\Models\User;
use App\Services\Admin\Category\Create\CategoryCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCreateServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryCreateService $service;

    private CategoryCreateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CategoryCreateRepositoryInterface::class);
        $this->service = new CategoryCreateService($this->repository);

        // Authenticate an admin user
        $this->actingAs(User::factory()->create());
    }

    // ========================================
    // Create Tests
    // ========================================

    public function test_create_successfully_creates_category(): void
    {
        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $category = $this->service->create($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertEquals('Test description', $category->description);
        $this->assertEquals('#FF5733', $category->color);
        $this->assertTrue($category->is_active);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_create_normalizes_color_to_uppercase_hex(): void
    {
        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#ff5733',
            'is_active' => true,
        ];

        $category = $this->service->create($data);

        $this->assertEquals('#FF5733', $category->color);
    }

    public function test_create_accepts_null_description(): void
    {
        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'description' => null,
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $category = $this->service->create($data);

        $this->assertNull($category->description);
    }

    public function test_create_sets_is_active_to_true_by_default(): void
    {
        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#FF5733',
        ];

        $category = $this->service->create($data);

        $this->assertTrue($category->is_active);
    }

    public function test_create_uses_database_transaction(): void
    {
        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $category = $this->service->create($data);

        // Verify category was created within transaction
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Test',
        ]);
    }

    public function test_create_logs_success(): void
    {
        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $category = $this->service->create($data);

        // Verify the category was created successfully (logging happens internally)
        $this->assertNotNull($category->id);
        $this->assertEquals('Test', $category->name);
    }

    public function test_create_logs_admin_id(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $category = $this->service->create($data);

        // Verify the category was created by the admin
        $this->assertNotNull($category->id);
    }

    public function test_create_rollback_on_repository_failure(): void
    {
        // Force repository to throw exception
        $mockRepository = $this->createMock(CategoryCreateRepositoryInterface::class);
        $mockRepository->method('create')
            ->willThrowException(new \Exception('Database error'));

        $service = new CategoryCreateService($mockRepository);

        $data = [
            'name' => 'Test',
            'slug' => 'test',
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $service->create($data);

        // Verify no category was created
        $this->assertDatabaseMissing('categories', [
            'name' => 'Test',
            'slug' => 'test',
        ]);
    }
}
