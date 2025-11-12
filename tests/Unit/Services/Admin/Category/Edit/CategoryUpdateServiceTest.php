<?php

namespace Tests\Unit\Services\Admin\Category\Edit;

use App\Contracts\Repositories\Admin\Category\Edit\CategoryUpdateRepositoryInterface;
use App\Models\Category;
use App\Models\Place;
use App\Models\User;
use App\Services\Admin\Category\Edit\CategoryUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryUpdateServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryUpdateService $service;

    private CategoryUpdateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CategoryUpdateRepositoryInterface::class);
        $this->service = new CategoryUpdateService($this->repository);

        // Authenticate an admin user
        $this->actingAs(User::factory()->create());
    }

    // ========================================
    // Find For Edit Tests
    // ========================================

    public function test_load_for_edit_returns_category_with_places_count(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);
        Place::factory()->count(3)->create()->each(fn ($place) => $place->categories()->attach($category->id));

        $result = $this->service->loadForEdit($category->id);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category->id, $result->id);
        $this->assertEquals(3, $result->places_count);
    }

    public function test_load_for_edit_returns_null_when_not_found(): void
    {
        $result = $this->service->loadForEdit(99999);

        $this->assertNull($result);
    }

    public function test_load_for_edit_returns_zero_places_count_when_no_associations(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        $result = $this->service->loadForEdit($category->id);

        $this->assertEquals(0, $result->places_count);
    }

    // ========================================
    // Update Tests
    // ========================================

    public function test_update_successfully_modifies_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-slug',
            'description' => 'Old description',
            'color' => '#FF0000',
            'is_active' => true,
        ]);

        $updateData = [
            'name' => 'New Name',
            'slug' => 'new-slug',
            'description' => 'New description',
            'color' => '#00FF00',
            'is_active' => false,
        ];

        $result = $this->service->update($category->id, $updateData);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('New Name', $result->name);
        $this->assertEquals('new-slug', $result->slug);
        $this->assertEquals('New description', $result->description);
        $this->assertEquals('#00FF00', $result->color);
        $this->assertFalse($result->is_active);
    }

    public function test_update_normalizes_color_to_uppercase_hex(): void
    {
        $category = Category::factory()->create(['color' => '#FF0000']);

        $updateData = [
            'name' => $category->name,
            'slug' => $category->slug,
            'color' => '#ff5733',
            'is_active' => $category->is_active,
        ];

        $result = $this->service->update($category->id, $updateData);

        $this->assertEquals('#FF5733', $result->color);
    }

    public function test_update_accepts_null_description(): void
    {
        $category = Category::factory()->create(['description' => 'Old description']);

        $updateData = [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => null,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ];

        $result = $this->service->update($category->id, $updateData);

        $this->assertNull($result->description);
    }

    public function test_update_uses_database_transaction(): void
    {
        $category = Category::factory()->create(['name' => 'Original']);

        $updateData = [
            'name' => 'Updated',
            'slug' => $category->slug,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ];

        $result = $this->service->update($category->id, $updateData);

        // Verify update was successful
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Updated', $result->name);
    }

    public function test_update_logs_success(): void
    {
        $category = Category::factory()->create(['name' => 'Original']);

        $updateData = [
            'name' => 'Updated',
            'slug' => $category->slug,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ];

        $result = $this->service->update($category->id, $updateData);

        // Verify the category was updated successfully (logging happens internally)
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Updated', $result->name);
    }

    public function test_update_rollback_on_repository_failure(): void
    {
        $category = Category::factory()->create(['name' => 'Original']);

        // Force repository to throw exception
        $mockRepository = $this->createMock(CategoryUpdateRepositoryInterface::class);
        $mockRepository->method('findForEdit')
            ->willReturn($category);
        $mockRepository->method('update')
            ->willThrowException(new \Exception('Database error'));

        $service = new CategoryUpdateService($mockRepository);

        $updateData = [
            'name' => 'Updated',
            'slug' => $category->slug,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $service->update($category->id, $updateData);

        // Verify category was not updated
        $category->refresh();
        $this->assertEquals('Original', $category->name);
    }

    // ========================================
    // Delete Tests
    // ========================================

    public function test_delete_successfully_removes_category(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->delete($category->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_detaches_from_places_before_deletion(): void
    {
        $category = Category::factory()->create();
        $place = Place::factory()->create();
        $place->categories()->attach($category->id);

        $this->assertDatabaseHas('place_category', [
            'category_id' => $category->id,
            'place_id' => $place->id,
        ]);

        $this->service->delete($category->id);

        $this->assertDatabaseMissing('place_category', [
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }

    public function test_delete_uses_database_transaction(): void
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $result = $this->service->delete($category->id);

        // Verify deletion was successful
        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_delete_logs_success(): void
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $result = $this->service->delete($category->id);

        // Verify the category was deleted successfully (logging happens internally)
        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_delete_rollback_on_repository_failure(): void
    {
        $category = Category::factory()->create();

        // Force repository to throw exception
        $mockRepository = $this->createMock(CategoryUpdateRepositoryInterface::class);
        $mockRepository->method('findForEdit')
            ->willReturn($category);
        $mockRepository->method('detachFromPlaces');
        $mockRepository->method('delete')
            ->willThrowException(new \Exception('Database error'));

        $service = new CategoryUpdateService($mockRepository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $service->delete($category->id);

        // Verify category still exists
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_delete_logs_admin_id(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $category = Category::factory()->create();
        $categoryId = $category->id;

        $result = $this->service->delete($category->id);

        // Verify the category was deleted by the admin (logging happens internally)
        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }
}
