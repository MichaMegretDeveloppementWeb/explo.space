<?php

namespace App\Contracts\Repositories\Admin\Category;

use Illuminate\Database\Eloquent\Collection;

interface CategorySelectionRepositoryInterface
{
    /**
     * Get all active categories ordered by name
     * Used in admin forms to populate category selection dropdowns
     * Categories don't have translations (internal admin use only)
     *
     * @return Collection<int, \App\Models\Category>
     */
    public function getAll(): Collection;
}
