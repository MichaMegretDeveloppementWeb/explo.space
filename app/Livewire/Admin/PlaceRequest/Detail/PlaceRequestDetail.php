<?php

namespace App\Livewire\Admin\PlaceRequest\Detail;

use App\Models\PlaceRequest;
use App\Services\Admin\PlaceRequest\Detail\PlaceRequestRefusalService;
use Livewire\Component;

class PlaceRequestDetail extends Component
{
    public PlaceRequest $placeRequest;

    public int $photoCount = 0;

    public bool $showRefusalModal = false;

    public ?string $refusalReason = null;

    /**
     * Initialiser le composant avec la proposition de lieu
     */
    public function mount(PlaceRequest $placeRequest, int $photoCount): void
    {
        $this->placeRequest = $placeRequest;
        $this->photoCount = $photoCount;
    }

    /**
     * Ouvrir la modale de refus
     */
    public function openRefusalModal(): void
    {
        $this->showRefusalModal = true;
        $this->refusalReason = null; // Reset
    }

    /**
     * Fermer la modale de refus
     */
    public function closeRefusalModal(): void
    {
        $this->showRefusalModal = false;
        $this->refusalReason = null;
    }

    /**
     * Refuser la proposition de lieu
     */
    public function refusePlaceRequest(PlaceRequestRefusalService $refusalService): void
    {
        // Vérifier que la proposition peut être refusée
        /** @var \App\Enums\RequestStatus $status */
        $status = $this->placeRequest->status;

        if (! $status->canBeRefused()) {
            $this->dispatch('flash-message', type: 'error', message: 'Cette proposition ne peut pas être refusée.');
            $this->closeRefusalModal();

            return;
        }

        // Refuser via le service
        $success = $refusalService->refuse(
            $this->placeRequest,
            auth()->id(),
            $this->refusalReason
        );

        if ($success) {
            // Recharger le modèle pour afficher les données à jour
            $this->placeRequest->refresh();
            $this->placeRequest->load([
                'viewedByAdmin:id,name',
                'processedByAdmin:id,name',
            ]);

            $this->dispatch('flash-message', type: 'success', message: 'La proposition a été refusée avec succès.');
        } else {
            $this->dispatch('flash-message', type: 'error', message: 'Une erreur est survenue lors du refus.');
        }

        $this->closeRefusalModal();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.place-request.detail.place-request-detail');
    }
}
