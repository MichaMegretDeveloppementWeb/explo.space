<?php

namespace App\Http\Controllers\Admin\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;

class PlaceEditController extends Controller
{
    /**
     * Display the place edit form.
     *
     * The Place model is automatically loaded via route model binding.
     * All relations needed for the form are eager loaded by the
     * PlaceUpdateService::loadForEdit() method in the Livewire component.
     */
    public function __invoke(int $id): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            return redirect('/')->with('error', 'Accès non autorisé.');
        }

        $place = Place::query()->find($id);

        if (! $place) {
            return redirect()->back()->with('error', 'Le lieu selectionné est introuvable.');
        }

        return view('admin.place.edit', [
            'place' => $place,
        ]);
    }
}
