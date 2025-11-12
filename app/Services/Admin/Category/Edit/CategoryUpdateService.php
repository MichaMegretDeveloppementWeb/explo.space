<?php

namespace App\Services\Admin\Category\Edit;

use App\Contracts\Repositories\Admin\Category\Edit\CategoryUpdateRepositoryInterface;
use App\Exceptions\Admin\Category\CategoryNotFoundException;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryUpdateService
{
    public function __construct(
        private CategoryUpdateRepositoryInterface $repository
    ) {}

    /**
     * Load a category for editing
     */
    public function loadForEdit(int $categoryId): ?Category
    {
        return $this->repository->findForEdit($categoryId);
    }

    /**
     * Update an existing category
     *
     * @param  array<string, mixed>  $data
     *
     * @throws CategoryNotFoundException|\Throwable
     */
    public function update(int $categoryId, array $data): Category
    {
        return DB::transaction(function () use ($categoryId, $data) {
            $category = $this->repository->findForEdit($categoryId);

            if (! $category) {
                throw new CategoryNotFoundException;
            }

            $this->repository->update($category, [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'color' => strtoupper($data['color']),
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Category updated successfully', [
                'category_id' => $category->id,
                'admin_id' => auth()->id(),
            ]);

            return $category->fresh();
        });
    }

    /**
     * Delete a category and detach it from all associated places
     *
     * @throws CategoryNotFoundException|\Throwable
     */
    public function delete(int $categoryId): bool
    {
        return DB::transaction(function () use ($categoryId) {
            $category = $this->repository->findForEdit($categoryId);

            if (! $category) {
                throw new CategoryNotFoundException;
            }

            // Count associated places for logging
            $associatedPlacesCount = $category->places()->count();

            // Detach from all places first
            $this->repository->detachFromPlaces($category);

            // Delete the category
            $deleted = $this->repository->delete($category);

            if ($deleted) {
                Log::info('Category deleted successfully', [
                    'category_id' => $categoryId,
                    'admin_id' => auth()->id(),
                    'detached_places_count' => $associatedPlacesCount,
                ]);
            }

            return $deleted;
        });
    }
}
