<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_default_values(): void
    {
        $category = Category::factory()->create();

        $this->assertTrue($category->is_active);
        $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $category->color); // Couleur hex aléatoire
        $this->assertTrue($category->isActive());
    }

    public function test_category_has_places_relation(): void
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $category->places());
    }

    public function test_category_slug_doesnt_change_on_update(): void
    {
        $category = Category::factory()->create([
            'name' => 'Initial Name',
            'slug' => 'initial-name',
        ]);

        $category->update(['name' => 'Updated Name']);

        // Le slug ne devrait pas changer si il existe déjà
        $this->assertEquals('initial-name', $category->slug);
    }

    public function test_category_is_active_helper(): void
    {
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $this->assertTrue($activeCategory->isActive());
        $this->assertFalse($inactiveCategory->isActive());
    }

    public function test_category_has_required_attributes(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#3B82F6',
            'is_active' => true,
        ]);

        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertEquals('Test description', $category->description);
        $this->assertEquals('#3B82F6', $category->color);
        $this->assertTrue($category->is_active);
    }
}
