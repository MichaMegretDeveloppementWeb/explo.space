<?php

namespace App\Livewire\Admin\Tag\Store\Concerns;

use App\Services\Admin\Tag\Edit\TagUpdateService;

trait ManagesLoadData
{
    /**
     * Load tag data for editing
     */
    private function loadTag(int $tagId): void
    {
        $service = app(TagUpdateService::class);
        $tag = $service->loadForEdit($tagId);

        if (! $tag) {
            session()->flash('error', 'Tag non trouvé.');
            $this->redirect(route('admin.tags.index'));

            return;
        }

        // Store tag model for reuse (avoids duplicate queries in views)
        $this->tag = $tag;

        // Load base data
        $this->color = $tag->color;
        $this->is_active = $tag->is_active;

        // Load translations
        foreach ($tag->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name,
                'slug' => $translation->slug,
                'description' => $translation->description ?? null,
            ];
        }
    }
}
