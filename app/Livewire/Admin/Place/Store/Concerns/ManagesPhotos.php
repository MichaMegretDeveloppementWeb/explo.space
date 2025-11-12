<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

use App\Http\Requests\Concerns\HasPhotoValidationRules;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait ManagesPhotos
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
        $existingCount = count($this->existingPhotos);
        $placeRequestCount = count($this->placeRequestPhotos);
        $editRequestCount = count($this->editRequestPhotos);
        $currentPhotosCount = count($this->photos);
        $newCount = count($this->pendingPhotos);
        $totalAfterAdd = $existingCount + $placeRequestCount + $editRequestCount + $currentPhotosCount + $newCount;

        if ($totalAfterAdd > $maxFiles) {
            $currentTotal = $existingCount + $placeRequestCount + $editRequestCount + $currentPhotosCount;
            $this->addError('pendingPhotos', "Vous ne pouvez pas avoir plus de {$maxFiles} photos au total. Vous avez déjà {$currentTotal} photo(s).");
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

            $this->dispatch('flash-message', type: 'photo_success', message: $addedCount.' photo(s) validée(s)');

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
     * Supprimer une photo de PlaceRequest de la liste
     */
    public function removePlaceRequestPhoto(int $index): void
    {
        if (isset($this->placeRequestPhotos[$index])) {
            unset($this->placeRequestPhotos[$index]);
            // Réindexer pour éviter les gaps
            $this->placeRequestPhotos = array_values($this->placeRequestPhotos);
        }
    }

    /**
     * Supprimer une photo de EditRequest de la liste
     */
    public function removeEditRequestPhoto(int $index): void
    {
        if (isset($this->editRequestPhotos[$index])) {
            unset($this->editRequestPhotos[$index]);
            // Réindexer pour éviter les gaps
            $this->editRequestPhotos = array_values($this->editRequestPhotos);
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
        $this->addError('pendingPhotos',
            'Une erreur inattendue est survenue lors du traitement des photos. '.
            'Veuillez réessayer.'
        );

        // Log complet pour investigation
        Log::error('Unexpected photo upload error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * @param  array<int, int>  $orderMap
     */
    public function updatePhotoOrder(array $orderMap): void
    {
        $this->photoOrder = $orderMap;

        // Mettre à jour l'ordre dans existingPhotos pour refléter le changement dans l'UI
        foreach ($this->existingPhotos as &$photo) {
            if (isset($orderMap[$photo['id']])) {
                $photo['sort_order'] = $orderMap[$photo['id']];
            }
        }

        // Réordonner le tableau par sort_order
        usort($this->existingPhotos, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        // Si une photo passe en première position (sort_order = 0), elle devient l'image principale
        if (! empty($this->existingPhotos)) {
            $firstPhoto = $this->existingPhotos[0];
            if ($firstPhoto['sort_order'] === 0) {
                $this->mainPhotoId = $firstPhoto['id'];
            }
        }
    }

    public function setMainPhoto(int $photoId): void
    {
        // Vérifier que le tableau n'est pas vide
        if (empty($this->existingPhotos)) {
            return;
        }

        // Trouver l'index de la photo sélectionnée
        $selectedPhotoIndex = null;
        foreach ($this->existingPhotos as $index => $photo) {
            if ($photo['id'] === $photoId) {
                $selectedPhotoIndex = $index;
                break;
            }
        }

        // Si la photo n'a pas été trouvée, ne rien faire
        if ($selectedPhotoIndex === null) {
            return;
        }

        // Si la photo n'est pas en première position, échanger avec la première
        if ($selectedPhotoIndex !== 0) {
            // Échanger les sort_order
            $selectedSortOrder = $this->existingPhotos[$selectedPhotoIndex]['sort_order'];
            $firstSortOrder = $this->existingPhotos[0]['sort_order'];

            $this->existingPhotos[$selectedPhotoIndex]['sort_order'] = $firstSortOrder;
            $this->existingPhotos[0]['sort_order'] = $selectedSortOrder;

            // Mettre à jour photoOrder pour la persistance
            $this->photoOrder[$this->existingPhotos[$selectedPhotoIndex]['id']] = $firstSortOrder;
            $this->photoOrder[$this->existingPhotos[0]['id']] = $selectedSortOrder;

            // Réordonner le tableau
            usort($this->existingPhotos, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);
        }

        // Définir comme photo principale
        $this->mainPhotoId = $photoId;
    }

    public function deletePhoto(int $photoId): void
    {
        $this->deletedPhotoIds[] = $photoId;

        // Vérifier si la photo supprimée était la principale
        $wasMainPhoto = ($this->mainPhotoId === $photoId);

        // Remove from existing photos
        $this->existingPhotos = array_filter(
            $this->existingPhotos,
            fn ($photo) => $photo['id'] !== $photoId
        );

        // Réindexer pour éviter les gaps (important pour setMainPhoto)
        $this->existingPhotos = array_values($this->existingPhotos);

        // Si la photo supprimée était principale, définir la première restante comme principale
        if ($wasMainPhoto) {
            if (! empty($this->existingPhotos)) {
                // La première photo du tableau réindexé devient principale
                $this->mainPhotoId = $this->existingPhotos[0]['id'];
            } else {
                // Plus de photos existantes
                $this->mainPhotoId = null;
            }
        }
    }
}
