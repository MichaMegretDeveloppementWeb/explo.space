<?php

namespace App\Livewire\Admin\Place\Store\Concerns;

use App\Exceptions\Admin\Place\PlaceNotFoundException;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Http\Requests\Admin\Place\PlaceStoreRequest;
use App\Services\Admin\Place\Create\PlaceCreateService;
use App\Services\Admin\Place\Edit\PlaceUpdateService;
use Illuminate\Support\Facades\Log;

trait ManagesSaving
{
    /**
     * Save the place (create or update).
     */
    public function save(): void
    {
        // Validate using Form Request rules
        $request = (new PlaceStoreRequest)->setPlaceId($this->placeId);

        try {
            $validated = $this->validate($request->rules(), $request->messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Detect first locale with error from exception validator
            $errors = $e->validator->errors();
            $supportedLocales = config('locales.supported', ['fr', 'en']);
            $firstErrorLocale = null;

            foreach ($supportedLocales as $locale) {
                if ($errors->has("translations.{$locale}.*")) {
                    $firstErrorLocale = $locale;
                    break;
                }
            }

            // Switch to first error tab if needed
            if ($firstErrorLocale) {
                $this->activeTranslationTab = $firstErrorLocale;
            }

            // Dispatch event to trigger scroll to first error
            $this->dispatch('scroll-to-validation-error');

            throw $e;
        }

        try {
            $data = $this->prepareDataForService($validated);

            if ($this->mode === 'create') {
                $this->handleCreate($data);
            } else {
                $this->handleUpdate($data);
            }
        } catch (PlaceNotFoundException $e) {
            $this->handlePlaceNotFoundException($e);
        } catch (PhotoValidationException $e) {
            $this->handlePhotoValidationException($e);
        } catch (PhotoProcessingException $e) {
            $this->handlePhotoProcessingException($e);
        } catch (UnexpectedPhotoException $e) {
            $this->handlePhotoUnexpectedException($e);
        } catch (\Throwable $e) {
            $this->handleGenericException($e);
        }
    }

    /**
     * Prepare data array for service layer.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function prepareDataForService(array $validated): array
    {
        return [
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['address'] ?? null,
            'is_featured' => $validated['is_featured'] ?? false,
            'translations' => $validated['translations'],
            'category_ids' => $validated['categoryIds'] ?? [],
            'tag_ids' => $validated['tagIds'] ?? [],
            'admin_id' => auth()->id(),
        ];
    }

    /**
     * Handle place creation.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    private function handleCreate(array $data): void
    {
        $data['request_id'] = $this->placeRequestId;
        $data['place_request_photos'] = $this->placeRequestPhotos;
        $data['photos'] = $this->photos;

        // Note: On utilise app() au lieu d'injecter le service en propriété pour éviter
        // la sérialisation du service à chaque cycle Livewire (impact performance).
        // Les services sont instanciés à la demande uniquement quand nécessaire.
        $service = app(PlaceCreateService::class);
        $place = $service->create($data);

        session()->flash('success', 'Lieu créé avec succès.');
        $this->redirect(route('admin.places.show', $place->id));
    }

    /**
     * Handle place update.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    private function handleUpdate(array $data): void
    {
        $data['new_photos'] = $this->photos;
        $data['deleted_photo_ids'] = $this->deletedPhotoIds;
        $data['photo_order'] = $this->photoOrder;
        $data['main_photo_id'] = $this->mainPhotoId;

        // Ajouter les données EditRequest si présentes
        if ($this->editRequestId !== null) {
            $data['edit_request_id'] = $this->editRequestId;
            $data['edit_request_photos'] = $this->editRequestPhotos;
            $data['selected_fields'] = $this->selectedFields;
            $data['selected_photos'] = $this->selectedPhotos;
        }

        // Note: On utilise app() au lieu d'injecter le service en propriété pour éviter
        // la sérialisation du service à chaque cycle Livewire (impact performance).
        // Les services sont instanciés à la demande uniquement quand nécessaire.
        $service = app(PlaceUpdateService::class);
        $place = $service->update($this->placeId, $data);

        session()->flash('success', 'Lieu mis à jour avec succès.');
        $this->redirect(route('admin.places.show', $place->id));
    }

    /**
     * Handle PlaceNotFoundException (business error).
     */
    private function handlePlaceNotFoundException(PlaceNotFoundException $e): void
    {
        $this->dispatch('flash-message', type: 'error', message: $e->getMessage());

        Log::warning('Place not found during save', [
            'place_id' => $this->placeId,
            'mode' => $this->mode,
            'admin_id' => auth()->id(),
        ]);
    }

    /**
     * Handle PhotoValidationException (user validation error).
     */
    private function handlePhotoValidationException(PhotoValidationException $e): void
    {
        $this->dispatch('flash-message', type: 'error', message: $e->getMessage());

        Log::warning('Photo validation failed during save', [
            'mode' => $this->mode,
            'place_id' => $this->placeId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle PhotoProcessingException (technical photo processing error).
     */
    private function handlePhotoProcessingException(PhotoProcessingException $e): void
    {
        $this->dispatch('flash-message', type: 'error', message: $e->getMessage());

        Log::error('Photo processing failed during save', [
            'mode' => $this->mode,
            'place_id' => $this->placeId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'previous' => $e->getPrevious()?->getMessage(),
        ]);
    }

    private function handlePhotoUnexpectedException(UnexpectedPhotoException $e): void
    {
        $this->dispatch('flash-message', type: 'error', message: $e->getMessage());

        Log::error('Photo processing catch unexpected error', [
            'mode' => $this->mode,
            'place_id' => $this->placeId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'previous' => $e->getPrevious()?->getMessage(),
        ]);
    }

    /**
     * Handle generic exceptions (technical errors).
     */
    private function handleGenericException(\Throwable $e): void
    {
        $message = 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer. Si le problème persiste, contactez l\'administrateur.';

        if (app()->environment('local', 'development')) {
            $message .= ' (Détail technique : '.$e->getMessage().')';
        }

        $this->dispatch('flash-message', type: 'error', message: $message);

        Log::error('Place save failed', [
            'mode' => $this->mode,
            'place_id' => $this->placeId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
