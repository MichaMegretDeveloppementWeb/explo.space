<?php

namespace App\Http\Controllers\Admin\Place;

use App\Http\Controllers\Controller;
use App\Models\PlaceRequest;
use Illuminate\Http\Request;

class PlaceCreateController extends Controller
{
    /**
     * Display the place creation form.
     *
     * This controller handles both:
     * - Direct creation (admin-initiated): /admin/lieux/creer
     * - Creation from PlaceRequest (visitor proposal): /admin/lieux/creer?request_id=12
     */
    public function __invoke(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Récupérer l'ID de PlaceRequest depuis la query string
        $placeRequestId = $request->query('request_id');

        $placeRequest = $placeRequestId ? PlaceRequest::query()->with('photos')->find($placeRequestId) : null;

        if ($placeRequestId && ! $placeRequest) {
            return redirect()->back()->with('error', 'Demande de lieu introuvable.');
        }

        if ($placeRequest instanceof PlaceRequest && ! $placeRequest->status->canBeModerated()) {
            return redirect()->back()->with('error', 'Demande de lieu déjà traitée.');
        }

        return view('admin.place.create', [
            'placeRequestId' => $placeRequestId,
        ]);
    }
}
