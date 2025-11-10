<?php

namespace App\Http\Controllers\Admin\Place;

use App\Http\Controllers\Controller;
use App\Models\EditRequest;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceEditController extends Controller
{
    /**
     * Display the place edit form.
     *
     * The Place model is automatically loaded via route model binding.
     * All relations needed for the form are eager loaded by the
     * PlaceUpdateService::loadForEdit() method in the Livewire component.
     */
    public function __invoke(int $id, Request $request): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            return redirect('/')->with('error', 'Accès non autorisé.');
        }

        $place = Place::query()->find($id);

        if (! $place) {
            return redirect(route('admin.places.index'))->with('error', 'Le lieu selectionné est introuvable.');
        }

        // Récupérer l'ID de PlaceRequest depuis la query string
        $editRequestId = $request->query('edit_request_id');

        $editRequest = $editRequestId ? EditRequest::query()->find($editRequestId) : null;

        if ($editRequestId && ! $editRequest) {
            return redirect(route('admin.places.show', ['id' => $id]))->with('error', 'Demande de modification introuvable.');
        }

        if ($editRequest instanceof EditRequest && ! $editRequest->status->canBeModerated()) {
            return redirect(route('admin.places.show', ['id' => $id]))->with('error', 'Demande de modification déjà traitée.');
        }

        return view('admin.place.edit', [
            'place' => $place,
            'editRequestId' => $editRequestId,
        ]);
    }
}
