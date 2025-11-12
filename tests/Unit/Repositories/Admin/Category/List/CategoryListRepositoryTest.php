<?php

namespace Tests\Unit\Repositories\Admin\Category\List;

use App\Models\Category;
use App\Models\Place;
use App\Repositories\Admin\Category\List\CategoryListRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CategoryListRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new CategoryListRepository;
    }

    // ========================================
    // Paginate Tests
    // ========================================

    public function test_paginate_returns_categories_with_places_count(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        Place::factory()->count(3)->create()->each(function ($place) use ($category) {
            $place->categories()->attach($category->id);
        });

        $result = $this->repository->paginate([], 'name', 'asc', 10);

        $this->assertEquals(1, $result->total());
        $this->assertEquals(3, $result->first()->places_count);
    }

    public function test_paginate_filters_by_search_query(): void
    {
        Category::factory()->create(['name' => 'Fusées']);
        Category::factory()->create(['name' => 'Satellites']);
        Category::factory()->create(['name' => 'Stations']);

        $result = $this->repository->paginate(['search' => 'Fusées'], 'name', 'asc', 10);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('Fusées', $result->first()->name);
    }

    public function test_paginate_filters_by_is_active_true(): void
    {
        Category::factory()->create(['name' => 'Active', 'is_active' => true]);
        Category::factory()->create(['name' => 'Inactive', 'is_active' => false]);

        $result = $this->repository->paginate(['activeFilter' => 'active'], 'name', 'asc', 10);

        $this->assertEquals(1, $result->total());
        $this->assertTrue($result->first()->is_active);
    }

    public function test_paginate_filters_by_is_active_false(): void
    {
        Category::factory()->create(['name' => 'Active', 'is_active' => true]);
        Category::factory()->create(['name' => 'Inactive', 'is_active' => false]);

        $result = $this->repository->paginate(['activeFilter' => 'inactive'], 'name', 'asc', 10);

        $this->assertEquals(1, $result->total());
        $this->assertFalse($result->first()->is_active);
    }

    public function test_paginate_sorts_by_name_ascending(): void
    {
        Category::factory()->create(['name' => 'Zulu']);
        Category::factory()->create(['name' => 'Alpha']);
        Category::factory()->create(['name' => 'Bravo']);

        $result = $this->repository->paginate([], 'name', 'asc', 10);

        $names = $result->pluck('name')->toArray();
        $this->assertEquals(['Alpha', 'Bravo', 'Zulu'], $names);
    }

    public function test_paginate_sorts_by_name_descending(): void
    {
        Category::factory()->create(['name' => 'Zulu']);
        Category::factory()->create(['name' => 'Alpha']);
        Category::factory()->create(['name' => 'Bravo']);

        $result = $this->repository->paginate([], 'name', 'desc', 10);

        $names = $result->pluck('name')->toArray();
        $this->assertEquals(['Zulu', 'Bravo', 'Alpha'], $names);
    }

    public function test_paginate_sorts_by_created_at(): void
    {
        $old = Category::factory()->create(['name' => 'Old', 'created_at' => now()->subDays(5)]);
        $new = Category::factory()->create(['name' => 'New', 'created_at' => now()]);

        $result = $this->repository->paginate([], 'created_at', 'desc', 10);

        $this->assertEquals('New', $result->first()->name);
    }

    public function test_paginate_sorts_by_updated_at(): void
    {
        $stale = Category::factory()->create(['name' => 'Stale', 'updated_at' => now()->subDays(5)]);
        $fresh = Category::factory()->create(['name' => 'Fresh', 'updated_at' => now()]);

        $result = $this->repository->paginate([], 'updated_at', 'desc', 10);

        $this->assertEquals('Fresh', $result->first()->name);
    }

    public function test_paginate_respects_per_page_parameter(): void
    {
        Category::factory()->count(25)->create();

        $result = $this->repository->paginate([], 'name', 'asc', 10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
    }

    public function test_paginate_combines_multiple_filters(): void
    {
        Category::factory()->create(['name' => 'Active Fusées', 'is_active' => true]);
        Category::factory()->create(['name' => 'Inactive Fusées', 'is_active' => false]);
        Category::factory()->create(['name' => 'Active Satellites', 'is_active' => true]);

        $result = $this->repository->paginate(
            ['search' => 'Fusées', 'activeFilter' => 'active'],
            'name',
            'asc',
            10
        );

        $this->assertEquals(1, $result->total());
        $this->assertEquals('Active Fusées', $result->first()->name);
        $this->assertTrue($result->first()->is_active);
    }

    // Get Total Count Tests
    // ========================================

    public function test_get_total_count_returns_correct_count(): void
    {
        Category::factory()->count(15)->create();

        $count = $this->repository->getTotalCount();

        $this->assertEquals(15, $count);
    }
}
