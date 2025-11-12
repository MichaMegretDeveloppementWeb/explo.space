<?php

namespace App\Contracts\Repositories\Admin\Category\Create;

use App\Models\Category;

interface CategoryCreateRepositoryInterface
{
    /**
     * Create a new category
     *
     * @param  array{name: string, slug: string, description: ?string, color: string, is_active: bool}  $categoryData
     */
    public function create(array $categoryData): Category;
}
