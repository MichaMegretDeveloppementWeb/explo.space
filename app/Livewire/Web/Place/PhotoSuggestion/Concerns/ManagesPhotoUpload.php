<?php

namespace App\Livewire\Web\Place\PhotoSuggestion\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait ManagesPhotoUpload
{
    /**
     * Validation automatique lors de la sélection de nouvelles photos
     * Hook Livewire appelé automatiquement quand $pendingPhotos change
     */
    public function updatedPendingPhotos(): void
    {
        // Si aucune photo pending, ne rien faire
        if (empty($this->pendingPhotos)) {
            return;
        }

        // Reset des erreurs précédentes
        $this->resetErrorBag('pendingPhotos');
        $this->resetErrorBag('pendingPhotos.*');

        // Vérifier que le total ne dépasse pas la limite
        $maxFiles = config('upload.images.max_files');
        $currentPhotosCount = count($this->photos);
        $newCount = count($this->pendingPhotos);
        $totalAfterAdd = $currentPhotosCount + $newCount;

        if ($totalAfterAdd > $maxFiles) {
            $this->addError('pendingPhotos', __('errors/photo-suggestion.photos_limit_exceeded', [
                'max' => $maxFiles,
                'current' => $currentPhotosCount,
            ]));
            $this->pendingPhotos = [];

            return;
        }

        // Valider uniquement les nouvelles photos (pas l'email ni le recaptcha)
        try {
            $this->validate(
                $this->getPhotoValidationRules('pendingPhotos'),
                $this->getPhotoValidationMessages('pendingPhotos')
            );

            // Succès : transférer vers photos validées
            $this->photos = array_merge($this->photos, $this->pendingPhotos);
            $this->pendingPhotos = [];

        } catch (ValidationException $e) {
            // Erreur de validation : afficher le message et vider pendingPhotos
            $this->handlePhotoValidationError($e);
            $this->pendingPhotos = [];

        } catch (\Throwable $e) {
            // Erreur inattendue : log + message générique
            $this->handleUnexpectedPhotoError($e);
            $this->pendingPhotos = [];
        }
    }

    // ========================================================================
    // ERROR HANDLERS - Photo Upload (updatedPendingPhotos)
    // ========================================================================

    /**
     * Handle photo validation errors during upload
     * User error: Photos don't meet requirements (size, format, etc.)
     */
    private function handlePhotoValidationError(ValidationException $exception): void
    {
        // Extract the first validation error message
        $errors = $exception->validator->errors();
        $firstError = $errors->first();

        Log::info('Photo validation failed during upload', [
            'place_id' => $this->place->id,
            'errors' => $errors->all(),
        ]);

        $this->addError('pendingPhotos', $firstError);
    }

    /**
     * Handle unexpected errors during photo upload
     * Technical error: Something went wrong on the server
     */
    private function handleUnexpectedPhotoError(\Throwable $exception): void
    {
        Log::error('Unexpected error during photo upload validation', [
            'place_id' => $this->place->id,
            'exception_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $message = __('errors/photo-suggestion.unexpected_upload');

        // Add technical details in development mode
        if (app()->environment('local', 'development')) {
            $message .= ' [Erreur technique : '.$exception->getMessage().']';
        }

        $this->addError('pendingPhotos', $message);
    }
}
