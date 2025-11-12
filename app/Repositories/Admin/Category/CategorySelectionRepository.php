<?php

namespace App\Repositories\Admin\Category;

use App\Contracts\Repositories\Admin\Category\CategorySelectionRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategorySelectionRepository implements CategorySelectionRepositoryInterface
{
    /**
     * Get all active categories ordered by name
     * Categories don't have translations (internal admin use only)
     */
    public function getAll(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
