<?php

namespace App\Livewire\Admin\Tag\Store\Concerns;

use App\Exceptions\Admin\Tag\TagNotFoundException;
use App\Http\Requests\Admin\Tag\TagStoreRequest;
use App\Services\Admin\Tag\Create\TagCreateService;
use App\Services\Admin\Tag\Edit\TagUpdateService;
use Illuminate\Support\Facades\Log;

trait ManagesSaving
{
    public bool $showDeleteModal = false;

    public ?int $associatedPlacesCount = null;

    /**
     * Save the tag (create or update).
     */
    public function save(): void
    {
        // Validate using Form Request rules
        $request = (new TagStoreRequest)->setTagId($this->tagId);

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
        } catch (TagNotFoundException $e) {
            $this->handleTagNotFoundException($e);
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
            'color' => $validated['color'],
            'is_active' => $validated['is_active'] ?? true,
            'translations' => $validated['translations'],
        ];
    }

    /**
     * Handle tag creation.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    private function handleCreate(array $data): void
    {
        $service = app(TagCreateService::class);
        $tag = $service->create($data);

        session()->flash('success', 'Tag créé avec succès.');

        // Redirect to edit page after creation
        $this->redirect(route('admin.tags.edit', $tag->id), navigate: true);
    }

    /**
     * Handle tag update.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    private function handleUpdate(array $data): void
    {
        $service = app(TagUpdateService::class);
        $tag = $service->update($this->tagId, $data);

        $this->dispatch('flash-message', type: 'success', message: 'Tag mis à jour avec succès.');

        // Stay on edit page after update (no redirect)
        $this->loadTag($tag->id);
    }

    /**
     * Handle TagNotFoundException (business error).
     */
    private function handleTagNotFoundException(TagNotFoundException $e): void
    {
        session()->flash('error', $e->getMessage());

        Log::warning('Tag not found during save', [
            'tag_id' => $this->tagId,
            'mode' => $this->mode,
            'admin_id' => auth()->id(),
        ]);

        $this->redirect(route('admin.tags.index'));
    }

    /**
     * Handle generic exception.
     */
    private function handleGenericException(\Throwable $e): void
    {
        $this->dispatch('flash-message', type: 'error', message: 'Une erreur inattendue est survenue. Veuillez réessayer.');

        Log::error('Unexpected error during tag save', [
            'mode' => $this->mode,
            'tag_id' => $this->tagId,
            'admin_id' => auth()->id(),
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Open delete confirmation modal
     */
    public function confirmDeleteModal(): void
    {
        if ($this->mode !== 'edit' || ! $this->tagId) {
            return;
        }

        // Count associated places for warning message
        // Use cached tag if available, otherwise load it
        $tag = $this->tag ?? \App\Models\Tag::find($this->tagId);
        $this->associatedPlacesCount = $tag ? $tag->places()->count() : 0;

        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->associatedPlacesCount = null;
    }

    /**
     * Confirm and execute tag deletion
     */
    public function delete(): void
    {
        if ($this->mode !== 'edit' || ! $this->tagId) {
            session()->flash('error', 'Action non autorisée.');

            return;
        }

        try {
            $service = app(TagUpdateService::class);
            $deleted = $service->delete($this->tagId);

            if ($deleted) {
                session()->flash('success', 'Tag supprimé avec succès.');
                $this->redirect(route('admin.tags.index'), navigate: true);
            } else {
                session()->flash('error', 'La suppression du tag a échoué.');
            }

        } catch (TagNotFoundException $e) {
            session()->flash('error', $e->getMessage());
            $this->redirect(route('admin.tags.index'));

        } catch (\Throwable $e) {
            Log::error('Unexpected error during tag deletion', [
                'tag_id' => $this->tagId,
                'admin_id' => auth()->id(),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Une erreur inattendue est survenue lors de la suppression.');
            $this->showDeleteModal = false;
        }
    }
}
