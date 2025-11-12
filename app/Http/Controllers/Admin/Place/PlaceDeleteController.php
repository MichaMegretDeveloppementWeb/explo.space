<?php

namespace App\Http\Controllers\Admin\Place;

use App\Http\Controllers\Controller;
use App\Services\Admin\Place\Management\PlaceDeleteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlaceDeleteController extends Controller
{
    public function __construct(
        private PlaceDeleteService $placeDeleteService
    ) {}

    /**
     * Supprimer un lieu
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $deleted = $this->placeDeleteService->deletePlace($id);

            if ($deleted) {
                return redirect()
                    ->route('admin.places.index')
                    ->with('success', 'Le lieu a été supprimé avec succès.');
            }

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du lieu.');
        } catch (\Exception $e) {
            $message = 'Une erreur est survenue lors de la suppression du lieu.';
            if (app()->environment() !== 'production') {
                $message .= ' '.$e->getMessage();
            }

            return redirect()
                ->back()
                ->with('error', $message);
        }
    }
}
