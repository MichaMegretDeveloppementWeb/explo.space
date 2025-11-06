<?php

namespace App\Http\Controllers\Admin\Place;

use App\Http\Controllers\Controller;
use App\Services\Admin\Place\Detail\PlaceDetailService;
use Illuminate\Http\Request;

class PlaceShowController extends Controller
{
    public function __construct(
        private PlaceDetailService $placeDetailService
    ) {}

    /**
     * Afficher le détail d'un lieu
     */
    public function show(Request $request, int $placeId): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $data = $this->placeDetailService->getPlaceDetail($placeId);

        if (empty($data)) {
            return redirect()->back()->with('error', 'Ce lieu n\'existe pas');
        }

        return view('admin.place.detail.show', $data);
    }
}
