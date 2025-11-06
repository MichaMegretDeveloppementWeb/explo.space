<?php

namespace App\Livewire\Web\Place\PhotoSuggestion\Concerns;

use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Http\Requests\Web\Place\PhotoSuggestion\PhotoSuggestionStoreRequest;
use App\Models\Place;
use App\Services\Web\Place\PhotoSuggestion\PhotoSuggestionCreateService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait ManagesSaving
{
    /**
     * Submit the photo suggestion form
     */
    public function submit(?string $recaptchaToken = null): void
    {
        // Assign reCAPTCHA token received from Alpine.js
        if ($recaptchaToken) {
            $this->recaptcha_token = $recaptchaToken;
        }

        // Check if place still exists before processing
        if (! Place::query()->where('id', $this->place->id)->exists()) {
            $this->addError('submit', __('errors/photo-suggestion.place_not_found'));

            return;
        }

        // Create FormRequest instance for validation
        $formRequest = new PhotoSuggestionStoreRequest;
        $formRequest->merge([
            'photos' => $this->photos,
            'contact_email' => $this->contact_email,
            'recaptcha_token' => $this->recaptcha_token,
        ]);

        // Validate using FormRequest rules and messages
        try {
            $validated = $this->validate($formRequest->rules(), $formRequest->messages());
        } catch (ValidationException $e) {
            // Let Livewire handle validation errors automatically
            throw $e;
        }

        try {
            // Call service to create photo suggestion
            $service = app(PhotoSuggestionCreateService::class);
            $service->create([
                'place_id' => $this->place->id,
                'contact_email' => $validated['contact_email'],
                'photos' => $validated['photos'],
            ]);

            // Flash success message
            session()->flash('success', __('web/pages/place-show.photo_suggestion.success'));

            // Reset form
            $this->reset(['pendingPhotos', 'photos', 'contact_email', 'recaptcha_token']);
            $this->showForm = false;

        } catch (PhotoValidationException $e) {
            $this->handlePhotoValidationSubmitError($e);

        } catch (PhotoProcessingException $e) {
            $this->handlePhotoProcessingError($e);

        } catch (UnexpectedPhotoException $e) {
            $this->handleUnexpectedPhotoSubmitError($e);

        } catch (QueryException $e) {
            $this->handleDatabaseError($e);

        } catch (\Throwable $e) {
            $this->handleUnexpectedSubmitError($e);
        }
    }

    // ========================================================================
    // ERROR HANDLERS - Form Submission
    // ========================================================================

    /**
     * Handle PhotoValidationException during submission
     * User error: Photos failed validation checks
     */
    private function handlePhotoValidationSubmitError(PhotoValidationException $exception): void
    {
        Log::warning('Photo validation failed during photo suggestion submission', [
            'place_id' => $this->place->id,
            'exception_message' => $exception->getMessage(),
            'error_code' => $exception->getErrorType(),
        ]);

        $message = __('errors/photo-suggestion.photo_validation');

        if (app()->environment('local', 'development')) {
            $message .= ' [Code : '.$exception->getErrorType().' - '.$exception->getMessage().']';
        }

        $this->addError('submit', $message);
    }

    /**
     * Handle PhotoProcessingException
     * Technical error: Photo processing failed (storage, optimization, etc.)
     */
    private function handlePhotoProcessingError(PhotoProcessingException $exception): void
    {
        Log::error('Photo processing failed during photo suggestion submission', [
            'place_id' => $this->place->id,
            'exception_type' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'error_code' => $exception->getErrorType(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $message = __('errors/photo-suggestion.photo_processing');

        if (app()->environment('local', 'development')) {
            $message .= ' [Code : '.$exception->getErrorType().' - '.$exception->getMessage().']';
        }

        $this->addError('submit', $message);
    }

    /**
     * Handle UnexpectedPhotoException
     * Technical error: Unexpected error during photo processing
     */
    private function handleUnexpectedPhotoSubmitError(UnexpectedPhotoException $exception): void
    {
        Log::critical('Unexpected photo error during photo suggestion submission', [
            'place_id' => $this->place->id,
            'exception_type' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'error_code' => $exception->getErrorType(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $message = __('errors/photo-suggestion.unexpected_photo');

        if (app()->environment('local', 'development')) {
            $message .= ' [Code : '.$exception->getErrorType().' - '.$exception->getMessage().']';
        }

        $this->addError('submit', $message);
    }

    /**
     * Handle QueryException
     * Technical error: Database error occurred
     */
    private function handleDatabaseError(QueryException $exception): void
    {
        Log::critical('Database error during photo suggestion submission', [
            'place_id' => $this->place->id,
            'exception_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'sql' => $exception->getSql() ?? 'N/A',
            'bindings' => $exception->getBindings() ?? [],
            'trace' => $exception->getTraceAsString(),
        ]);

        $message = __('errors/photo-suggestion.database');

        if (app()->environment('local', 'development')) {
            $message .= ' [SQL Error : '.$exception->getMessage().']';
        }

        $this->addError('submit', $message);
    }

    /**
     * Handle any other unexpected errors during submission
     * Technical error: Unknown error occurred
     */
    private function handleUnexpectedSubmitError(\Throwable $exception): void
    {
        Log::critical('Unexpected error during photo suggestion submission', [
            'place_id' => $this->place->id,
            'exception_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $message = __('errors/photo-suggestion.unexpected');

        if (app()->environment('local', 'development')) {
            $message .= ' [Erreur : '.$exception->getMessage().' dans '.$exception->getFile().':'.$exception->getLine().']';
        }

        $this->addError('submit', $message);
    }
}
