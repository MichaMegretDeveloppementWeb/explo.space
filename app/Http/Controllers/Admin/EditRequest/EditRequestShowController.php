<?php

namespace App\Http\Controllers\Admin\EditRequest;

use App\Contracts\Repositories\Admin\EditRequest\Detail\EditRequestDetailRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Admin\EditRequest\Detail\EditRequestViewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EditRequestShowController extends Controller
{
    public function __construct(
        private readonly EditRequestDetailRepositoryInterface $repository,
        private readonly EditRequestViewService $viewService
    ) {}

    /**
     * Afficher le détail d'une demande de modification/signalement
     */
    public function show(int $id): View|RedirectResponse
    {
        // Récupérer via repository (avec toutes les relations)
        $editRequest = $this->repository->findWithRelations($id);

        // Gérer le cas où la demande n'existe pas
        if ($editRequest === null) {
            return redirect()
                ->route('admin.edit-requests.index')
                ->with('error', 'Cette demande de modification n\'existe pas ou a été supprimée.');
        }

        // Marquer comme vue si première consultation
        $this->viewService->markAsViewedIfNeeded($editRequest, auth()->id());

        // Recharger les relations pour récupérer les données à jour
        $editRequest->refresh();
        $editRequest->load([
            'viewedByAdmin:id,name',
            'processedByAdmin:id,name',
        ]);

        return view('admin.edit-request.detail.show', [
            'editRequest' => $editRequest,
        ]);
    }
}
