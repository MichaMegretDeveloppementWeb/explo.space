<?php

namespace App\Livewire\Web\Place\Index\Traits;

/**
 * Trait pour gérer les changements de rayon de recherche
 */
trait HandlesRadius
{
    public function updatedRadius(): void
    {
        // Debounce géré côté JavaScript Alpine (500ms)
        $this->emitFiltersChanged();

    }
}
