<?php

namespace App\Repositories\Admin\Category;

use App\Contracts\Repositories\Admin\Category\CategoryListRepositoryInterface;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryListRepository implements CategoryListRepositoryInterface
{
    /**
     * Get paginated categories with filters and sorting
     *
     * @param  array{search: string, activeFilter: string}  $filters
     * @param  array{column: string, direction: string}  $sorting
     * @return LengthAwarePaginator<int, \App\Models\Category>
     */
    public function getPaginatedCategories(array $filters, array $sorting, int $perPage): LengthAwarePaginator
    {
        $query = Category::query()
            ->withCount('places'); // Count associated places

        // Apply search filter on category name, slug, or description
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%')
                    ->orWhere('description', 'like', '%'.$filters['search'].'%');
            });
        }

        // Apply active/inactive filter
        if ($filters['activeFilter'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['activeFilter'] === 'inactive') {
            $query->where('is_active', false);
        }
        // 'all' = no filter

        // Apply sorting
        if ($sorting['column'] === 'places_count') {
            // Sort by places count (already loaded via withCount)
            $query->orderBy('places_count', $sorting['direction']);
        } else {
            // Sort by column on categories table directly
            $query->orderBy($sorting['column'], $sorting['direction']);
        }

        return $query->paginate($perPage);
    }
}
