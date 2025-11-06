<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

trait ManagesRelations
{
    public function updatedCategoryIds(): void
    {
        // Instant validation for categories
        $this->validate([
            'categoryIds' => 'array',
            'categoryIds.*' => 'integer|exists:categories,id',
        ]);
    }

    public function updatedTagIds(): void
    {
        // Instant validation for tags
        $this->validate([
            'tagIds' => 'array',
            'tagIds.*' => 'integer|exists:tags,id',
        ]);
    }
}
