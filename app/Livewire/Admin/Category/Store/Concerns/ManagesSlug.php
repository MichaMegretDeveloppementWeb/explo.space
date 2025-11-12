<?php

namespace App\Livewire\Admin\Category\Store\Concerns;

use Illuminate\Support\Str;

trait ManagesSlug
{
    /**
     * Auto-generate slug from name when name changes
     */
    public function updatedName(): void
    {
        $this->slug = Str::slug($this->name);
    }
}
