<?php

namespace App\Repositories\Admin\Category\List;

use App\Contracts\Repositories\Admin\Category\List\CategoryListRepositoryInterface;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryListRepository implements CategoryListRepositoryInterface
{
    /**
     * Get paginated categories with filters and sorting
     *
     * @param  array{search?: string, is_active?: string}  $filters
     * @return LengthAwarePaginator<int, Category>
     */
    public function paginate(array $filters, string $sortBy, string $sortDirection, int $perPage): LengthAwarePaginator
    {
        $query = Category::query()
            ->withCount('places');

        // Apply search filter on category name, slug, or description
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        // Apply active/inactive filter
        if (isset($filters['activeFilter'])) {
            if ($filters['activeFilter'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['activeFilter'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply sorting
        if ($sortBy === 'places_count') {
            $query->orderBy('places_count', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get total count of all categories
     */
    public function getTotalCount(): int
    {
        return Category::count();
    }
}
