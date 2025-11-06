<?php

namespace App\Repositories\Admin\Category;

use App\Contracts\Repositories\Admin\Category\CategorySelectionRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategorySelectionRepository implements CategorySelectionRepositoryInterface
{
    /**
     * Get all categories with their translations for all locales
     * Eager loads translations to avoid N+1 queries
     * Orders by the first translation's name for consistency
     */
    public function getAll(): Collection
    {
        return Category::query()
            ->with(['translations' => function ($query) {
                $query->orderBy('locale');
            }])
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($category) {
                // Sort by first translation name (usually 'fr')
                $firstTranslation = $category->translations->first();

                return $firstTranslation->name ?? '';
            })
            ->values();
    }
}
