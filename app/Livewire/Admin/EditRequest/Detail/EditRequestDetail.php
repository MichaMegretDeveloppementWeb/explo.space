<?php

namespace App\Livewire\Admin\EditRequest\Detail;

use App\Contracts\Services\Admin\EditRequest\Detail\EditRequestTranslationServiceInterface;
use App\Models\EditRequest;
use App\Services\Admin\EditRequest\Detail\EditRequestAcceptanceService;
use App\Services\Admin\EditRequest\Detail\EditRequestRefusalService;
use Livewire\Component;

class EditRequestDetail extends Component
{
    public EditRequest $editRequest;

    public bool $showRefusalModal = false;

    public ?string $refusalReason = null;

    // Pour les modifications : champs sélectionnés à appliquer
    /** @var array<int, string> */
    public array $selectedFields = [];

    // Pour les photo_suggestions : photos sélectionnées à appliquer
    /** @var array<int, int> */
    public array $selectedPhotos = [];

    // Pour la traduction : gérer l'affichage original vs traduction par champ
    /** @var array<string, bool> */
    public array $showTranslated = [];

    // Pour la traduction : affichage de la description (original vs traduction)
    public bool $showDescriptionTranslated = false;

    /**
     * Initialiser le composant avec la demande de modification
     */
    public function mount(EditRequest $editRequest): void
    {
        $this->editRequest = $editRequest;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.edit-request.detail.edit-request-detail');
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
     * Refuser la demande de modification/signalement
     */
    public function refuseEditRequest(EditRequestRefusalService $refusalService): void
    {
        // Vérifier que la demande peut être refusée
        /** @var \App\Enums\RequestStatus $status */
        $status = $this->editRequest->status;

        if (! $status->canBeRefused()) {
            $this->dispatch('flash-message', type: 'error', message: 'Cette demande ne peut pas être refusée.');
            $this->closeRefusalModal();

            return;
        }

        // Refuser via le service
        $success = $refusalService->refuse(
            $this->editRequest,
            auth()->id(),
            $this->refusalReason
        );

        if ($success) {
            // Recharger le modèle pour afficher les données à jour
            $this->editRequest->refresh();
            $this->editRequest->load([
                'viewedByAdmin:id,name',
                'processedByAdmin:id,name',
            ]);

            $this->dispatch('flash-message', type: 'success', message: 'La demande a été refusée avec succès.');
        } else {
            $this->dispatch('flash-message', type: 'error', message: 'Une erreur est survenue lors du refus.');
        }

        $this->closeRefusalModal();
    }

    /**
     * Accepter un signalement simple (admin confirme avoir fait les modifications manuellement)
     */
    public function acceptSignalement(EditRequestAcceptanceService $acceptanceService): void
    {
        // Vérifier que c'est bien un signalement
        if (! $this->editRequest->isSignalement()) {
            $this->dispatch('flash-message', type: 'error', message: 'Cette action n\'est disponible que pour les signalements simples.');

            return;
        }

        // Accepter via le service
        $success = $acceptanceService->acceptSignalement(
            $this->editRequest,
            auth()->id()
        );

        if ($success) {
            // Recharger le modèle
            $this->editRequest->refresh();
            $this->editRequest->load([
                'viewedByAdmin:id,name',
                'processedByAdmin:id,name',
            ]);

            $this->dispatch('flash-message', type: 'success', message: 'Le signalement a été marqué comme traité avec succès.');
        } else {
            $this->dispatch('flash-message', type: 'error', message: 'Une erreur est survenue lors de l\'acceptation.');
        }
    }

    /**
     * Rediriger vers le formulaire d'édition avec les modifications proposées
     */
    public function applyModification(): void
    {

        // Vérifier que c'est bien une modification
        if (! $this->editRequest->isModification()) {
            session()->flash('error', 'Cette action n\'est disponible que pour les propositions de modification.');

            return;
        }

        // Vérifier qu'au moins un champ est sélectionné
        if (empty($this->selectedFields)) {
            session()->flash('error', 'Veuillez sélectionner au moins un champ à appliquer.');

            return;
        }

        // Rediriger vers le formulaire d'édition avec l'ID de l'EditRequest
        $this->redirect(route('admin.places.edit', [
            'id' => $this->editRequest->place_id,
            'edit_request_id' => $this->editRequest->id,
            'selected_fields' => $this->selectedFields,
        ]));
    }

    /**
     * Rediriger vers le formulaire d'édition avec les photos proposées
     */
    public function applyPhotoSuggestion(): void
    {
        // Vérifier que c'est bien une photo_suggestion
        if (! $this->editRequest->isPhotoSuggestion()) {
            session()->flash('error', 'Cette action n\'est disponible que pour les propositions de photos.');

            return;
        }

        // Vérifier qu'au moins une photo est sélectionnée
        if (empty($this->selectedPhotos)) {
            session()->flash('error', 'Veuillez sélectionner au moins une photo à appliquer.');

            return;
        }

        // Rediriger vers le formulaire d'édition avec l'ID de l'EditRequest
        $this->redirect(route('admin.places.edit', [
            'id' => $this->editRequest->place_id,
            'edit_request_id' => $this->editRequest->id,
            'selected_photos' => $this->selectedPhotos,
        ]));
    }

    /**
     * Traduire un champ spécifique
     */
    public function translateField(string $fieldName, \App\Contracts\Services\Admin\EditRequest\Detail\EditRequestTranslationServiceInterface $service): void
    {
        $success = $service->translateField($this->editRequest, $fieldName);

        if ($success) {
            // Recharger le modèle pour avoir la traduction
            $this->editRequest->refresh();

            // Afficher automatiquement la traduction
            $this->showTranslated[$fieldName] = true;

            session()->flash('success', 'Le champ a été traduit avec succès.');
        } else {
            session()->flash('error', 'Impossible de traduire ce champ.');
        }
    }

    /**
     * Traduire la description
     */
    public function translateDescription(EditRequestTranslationServiceInterface $service): void
    {
        $success = $service->translateDescription($this->editRequest);

        if ($success) {
            // Recharger le modèle pour avoir la traduction
            $this->editRequest->refresh();

            // Afficher automatiquement la traduction
            $this->showDescriptionTranslated = true;

            session()->flash('success', 'La description a été traduite avec succès.');
        } else {
            session()->flash('error', 'Impossible de traduire la description.');
        }
    }

    /**
     * Basculer entre original et traduction pour un champ
     */
    public function toggleFieldTranslation(string $fieldName): void
    {
        $this->showTranslated[$fieldName] = ! ($this->showTranslated[$fieldName] ?? false);
    }

    /**
     * Basculer pour la description
     */
    public function toggleDescriptionTranslation(): void
    {
        $this->showDescriptionTranslated = ! $this->showDescriptionTranslated;
    }
}
