<?php

namespace App\Repositories\Admin\Category\Edit;

use App\Contracts\Repositories\Admin\Category\Edit\CategoryUpdateRepositoryInterface;
use App\Models\Category;

class CategoryUpdateRepository implements CategoryUpdateRepositoryInterface
{
    public function findForEdit(int $id): ?Category
    {
        return Category::withCount('places')->find($id);
    }

    /**
     * @param  array{name?: string, slug?: string, description?: ?string, color?: string, is_active?: bool}  $categoryData
     */
    public function update(Category $category, array $categoryData): bool
    {
        return $category->update([
            'name' => $categoryData['name'] ?? $category->name,
            'slug' => $categoryData['slug'] ?? $category->slug,
            'description' => array_key_exists('description', $categoryData) ? $categoryData['description'] : $category->description,
            'color' => $categoryData['color'] ?? $category->color,
            'is_active' => $categoryData['is_active'] ?? $category->is_active,
        ]);
    }

    public function detachFromPlaces(Category $category): void
    {
        $category->places()->detach();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
