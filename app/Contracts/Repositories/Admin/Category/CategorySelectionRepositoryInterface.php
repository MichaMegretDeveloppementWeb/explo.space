<?php

namespace App\Contracts\Repositories\Admin\Category;

use Illuminate\Database\Eloquent\Collection;

interface CategorySelectionRepositoryInterface
{
    /**
     * Get all categories with their translations for all locales
     * Used in admin forms to populate category selection dropdowns
     * Returns categories with eager-loaded translations
     *
     * @return Collection<int, \App\Models\Category>
     */
    public function getAll(): Collection;
}
