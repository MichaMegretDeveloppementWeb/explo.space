<?php

namespace App\Livewire\Admin\Category\Store\Concerns;

use App\Services\Admin\Category\Edit\CategoryUpdateService;

trait ManagesLoadData
{
    /**
     * Load category data for editing
     */
    private function loadCategory(int $categoryId): void
    {
        $service = app(CategoryUpdateService::class);
        $category = $service->loadForEdit($categoryId);

        if (! $category) {
            session()->flash('error', 'Catégorie non trouvée.');
            $this->redirect(route('admin.categories.index'));

            return;
        }

        // Store category model for reuse (avoids duplicate queries in views)
        $this->category = $category;

        // Load data
        $this->name = $category->name;
        $this->original_name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->color = $category->color;
        $this->is_active = $category->is_active;
    }
}
