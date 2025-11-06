<?php

namespace App\Http\Controllers\Admin\PlaceRequest;

use App\Http\Controllers\Controller;
use App\Models\PlaceRequest;
use App\Services\Admin\PlaceRequest\Detail\PlaceRequestViewService;
use Illuminate\View\View;

class PlaceRequestShowController extends Controller
{
    public function __construct(
        private readonly PlaceRequestViewService $viewService
    ) {}

    /**
     * Afficher le détail d'une proposition de lieu
     */
    public function show(int $id): View
    {
        $placeRequest = PlaceRequest::with([
            'viewedByAdmin:id,name',
            'processedByAdmin:id,name',
            'photos',
            'place', // Lieu créé si accepté
        ])->findOrFail($id);

        // Marquer comme vue si première consultation
        $this->viewService->markAsViewedIfNeeded($placeRequest, auth()->id());

        // Recharger les relations pour récupérer les données à jour
        $placeRequest->refresh();
        $placeRequest->load([
            'viewedByAdmin:id,name',
            'processedByAdmin:id,name',
        ]);

        // Compteur de photos
        $photoCount = $placeRequest->photos->count();

        return view('admin.place-request.detail.show', [
            'placeRequest' => $placeRequest,
            'photo_count' => $photoCount,
        ]);
    }
}
