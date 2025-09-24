<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_generates_slug_automatically(): void
    {
        $category = new Category([
            'name' => 'Stations Spatiales',
            'description' => 'Catégorie pour les stations spatiales',
        ]);
        $category->save();

        $this->assertEquals('stations-spatiales', $category->slug);
    }

    public function test_category_has_default_values(): void
    {
        $category = Category::factory()->create();

        $this->assertTrue($category->is_active);
        $this->assertEquals('#6B7280', $category->color); // Couleur par défaut différente des tags
        $this->assertTrue($category->isActive());
    }

    public function test_category_has_places_relation(): void
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $category->places());
    }

    public function test_category_slug_update_when_name_changes(): void
    {
        $category = Category::factory()->create([
            'name' => 'Initial Name',
            'slug' => '', // Force empty slug to trigger auto-generation
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
}
