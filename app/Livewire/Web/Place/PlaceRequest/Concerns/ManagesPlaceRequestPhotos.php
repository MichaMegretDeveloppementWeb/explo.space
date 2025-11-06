<?php

namespace App\Livewire\Web\Place\PlaceRequest\Concerns;

use App\Http\Requests\Concerns\HasPhotoValidationRules;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait ManagesPlaceRequestPhotos
{
    use HasPhotoValidationRules;

    /**
     * Validation automatique lors de la sélection de photos
     */
    public function updatedPendingPhotos(): void
    {

        if (empty($this->pendingPhotos)) {
            return;
        }

        // Reset des erreurs précédentes de pendingPhotos
        $this->clearPendingPhotosErrors();

        // Vérifier que le total ne dépasse pas la limite
        $maxFiles = config('upload.images.max_files');
        $currentPhotosCount = count($this->photos);
        $newCount = count($this->pendingPhotos);
        $totalAfterAdd = $currentPhotosCount + $newCount;

        if ($totalAfterAdd > $maxFiles) {
            $this->addError('pendingPhotos', __('web/pages/place-request.messages.photos_limit_exceeded', [
                'max' => $maxFiles,
                'current' => $currentPhotosCount,
            ]));
            $this->pendingPhotos = [];

            return;
        }

        // Validation avec les règles adaptées pour pendingPhotos
        try {
            $this->validate(
                $this->getPhotoValidationRules('pendingPhotos'),
                $this->getPhotoValidationMessages('pendingPhotos')
            );

            // Succès : transférer vers photos, vider pendingPhotos
            $addedCount = count($this->pendingPhotos);
            $this->photos = array_merge($this->photos, $this->pendingPhotos);
            $this->pendingPhotos = [];

            session()->flash('photo_success', __('web/pages/place-request.messages.photos_validated', [
                'count' => $addedCount,
            ]));

        } catch (ValidationException $e) {
            $this->addError('pendingPhotos', $e->getMessage());
            $this->pendingPhotos = [];

            return;

        } catch (\Exception $e) {
            $this->handleUnexpectedError($e);
            $this->pendingPhotos = [];

            return;
        }
    }

    /**
     * Supprimer une photo de la liste des photos validées
     */
    public function removePhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            // Réindexer pour éviter les gaps
            $this->photos = array_values($this->photos);
        }
    }

    /**
     * Réinitialiser les erreurs pendingPhotos (appelé depuis Alpine.js)
     */
    public function clearPendingPhotosErrors(): void
    {
        $this->resetErrorBag('pendingPhotos');
        $this->resetErrorBag('pendingPhotos.*');
    }

    /**
     * Gérer erreurs inattendues (ne pas exposer détails)
     */
    private function handleUnexpectedError(\Exception $e): void
    {
        // Message générique pour l'utilisateur
        $this->addError('pendingPhotos', __('web/pages/place-request.messages.photos_unexpected_error'));

        // Log complet pour investigation
        Log::error('Unexpected photo upload error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
}
