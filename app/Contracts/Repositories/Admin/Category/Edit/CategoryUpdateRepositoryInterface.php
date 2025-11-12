<?php

namespace App\Contracts\Repositories\Admin\Category\Edit;

use App\Models\Category;

interface CategoryUpdateRepositoryInterface
{
    /**
     * Find a category with relations for editing
     */
    public function findForEdit(int $id): ?Category;

    /**
     * Update category data
     *
     * @param  array{name?: string, slug?: string, description?: ?string, color?: string, is_active?: bool}  $categoryData
     */
    public function update(Category $category, array $categoryData): bool;

    /**
     * Detach category from all associated places
     * Used before deletion to avoid orphaned relations
     */
    public function detachFromPlaces(Category $category): void;

    /**
     * Delete a category
     */
    public function delete(Category $category): bool;
}
