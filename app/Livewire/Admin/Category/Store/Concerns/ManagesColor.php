<?php

namespace App\Livewire\Admin\Category\Store\Concerns;

trait ManagesColor
{
    /**
     * Update color from color picker
     */
    public function updatedColor(string $value): void
    {
        // Normalize color format (ensure # prefix for hex)
        if (! str_starts_with($value, '#') && preg_match('/^[0-9A-Fa-f]{6}$/', $value)) {
            $this->color = '#'.$value;
        }
    }
}
