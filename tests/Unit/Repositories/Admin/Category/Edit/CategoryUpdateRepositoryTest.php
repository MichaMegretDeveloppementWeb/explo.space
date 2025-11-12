<?php

namespace Tests\Unit\Repositories\Admin\Category\Edit;

use App\Models\Category;
use App\Models\Place;
use App\Repositories\Admin\Category\Edit\CategoryUpdateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryUpdateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CategoryUpdateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new CategoryUpdateRepository;
    }

    // ========================================
    // Find For Edit Tests
    // ========================================

    public function test_find_for_edit_returns_category_with_places_count(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);
        Place::factory()->count(5)->create()->each(fn ($place) => $place->categories()->attach($category->id));

        $result = $this->repository->findForEdit($category->id);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category->id, $result->id);
        $this->assertEquals(5, $result->places_count);
    }

    public function test_find_for_edit_returns_null_when_not_found(): void
    {
        $result = $this->repository->findForEdit(99999);

        $this->assertNull($result);
    }

    public function test_find_for_edit_loads_places_count_even_when_zero(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        $result = $this->repository->findForEdit($category->id);

        $this->assertEquals(0, $result->places_count);
    }

    // ========================================
    // Update Tests
    // ========================================

    public function test_update_modifies_all_fields(): void
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

        $result = $this->repository->update($category, $updateData);

        $this->assertTrue($result);
        $category->refresh();
        $this->assertEquals('New Name', $category->name);
        $this->assertEquals('new-slug', $category->slug);
        $this->assertEquals('New description', $category->description);
        $this->assertEquals('#00FF00', $category->color);
        $this->assertFalse($category->is_active);
    }

    public function test_update_accepts_null_description(): void
    {
        $category = Category::factory()->create(['description' => 'Old description']);

        $result = $this->repository->update($category, [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => null,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ]);

        $this->assertTrue($result);
        $category->refresh();
        $this->assertNull($category->description);
    }

    public function test_update_preserves_unchanged_fields(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test',
            'description' => 'Description',
            'color' => '#FF0000',
        ]);

        $result = $this->repository->update($category, [
            'name' => 'Updated',
            'slug' => $category->slug,
            'description' => $category->description,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ]);

        $this->assertTrue($result);
        $category->refresh();
        $this->assertEquals('Updated', $category->name);
        $this->assertEquals('Description', $category->description);
        $this->assertEquals('#FF0000', $category->color);
    }

    public function test_update_updates_timestamp(): void
    {
        $category = Category::factory()->create();
        $oldUpdatedAt = $category->updated_at;

        sleep(1);

        $this->repository->update($category, [
            'name' => 'Updated',
            'slug' => $category->slug,
            'color' => $category->color,
            'is_active' => $category->is_active,
        ]);

        $category->refresh();
        $this->assertTrue($category->updated_at->greaterThan($oldUpdatedAt));
    }

    // ========================================
    // Detach From Places Tests
    // ========================================

    public function test_detach_from_places_removes_all_associations(): void
    {
        $category = Category::factory()->create();
        $places = Place::factory()->count(3)->create();
        $places->each(fn ($place) => $place->categories()->attach($category->id));

        $this->assertEquals(3, $category->places()->count());

        $this->repository->detachFromPlaces($category);

        $this->assertEquals(0, $category->places()->count());
    }

    public function test_detach_from_places_does_nothing_when_no_associations(): void
    {
        $category = Category::factory()->create();

        $this->repository->detachFromPlaces($category);

        $this->assertEquals(0, $category->places()->count());
    }

    public function test_detach_from_places_does_not_delete_places(): void
    {
        $category = Category::factory()->create();
        $place = Place::factory()->create();
        $place->categories()->attach($category->id);

        $this->repository->detachFromPlaces($category);

        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }

    // ========================================
    // Delete Tests
    // ========================================

    public function test_delete_removes_category_from_database(): void
    {
        $category = Category::factory()->create();

        $result = $this->repository->delete($category);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_removes_place_associations(): void
    {
        $category = Category::factory()->create();
        $place = Place::factory()->create();
        $place->categories()->attach($category->id);

        $this->assertDatabaseHas('place_category', [
            'category_id' => $category->id,
            'place_id' => $place->id,
        ]);

        $this->repository->delete($category);

        $this->assertDatabaseMissing('place_category', [
            'category_id' => $category->id,
        ]);
    }

    public function test_delete_does_not_delete_associated_places(): void
    {
        $category = Category::factory()->create();
        $place = Place::factory()->create();
        $place->categories()->attach($category->id);

        $this->repository->delete($category);

        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }
}
