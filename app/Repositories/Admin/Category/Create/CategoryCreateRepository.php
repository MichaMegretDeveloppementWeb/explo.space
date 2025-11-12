<?php

namespace App\Repositories\Admin\Category\Create;

use App\Contracts\Repositories\Admin\Category\Create\CategoryCreateRepositoryInterface;
use App\Models\Category;

class CategoryCreateRepository implements CategoryCreateRepositoryInterface
{
    /**
     * @param  array{name: string, slug: string, description: ?string, color: string, is_active: bool}  $categoryData
     */
    public function create(array $categoryData): Category
    {
        return Category::create([
            'name' => $categoryData['name'],
            'slug' => $categoryData['slug'],
            'description' => $categoryData['description'] ?? null,
            'color' => $categoryData['color'],
            'is_active' => $categoryData['is_active'] ?? true,
        ]);
    }
}
