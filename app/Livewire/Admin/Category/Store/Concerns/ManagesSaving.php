<?php

namespace App\Livewire\Admin\Category\Store\Concerns;

use App\Exceptions\Admin\Category\CategoryNotFoundException;
use App\Http\Requests\Admin\Category\CategoryStoreRequest;
use App\Services\Admin\Category\Create\CategoryCreateService;
use App\Services\Admin\Category\Edit\CategoryUpdateService;
use Illuminate\Support\Facades\Log;

trait ManagesSaving
{
    public bool $showDeleteModal = false;

    public ?int $associatedPlacesCount = null;

    /**
     * Save the category (create or update)
     */
    public function save(): void
    {
        // Validate using Form Request rules
        $request = (new CategoryStoreRequest)->setCategoryId($this->categoryId);

        try {
            $validated = $this->validate($request->rules(), $request->messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
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
        } catch (CategoryNotFoundException $e) {
            $this->handleCategoryNotFoundException($e);
        } catch (\Throwable $e) {
            $this->handleGenericException($e);
        }
    }

    /**
     * Prepare data array for service layer
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function prepareDataForService(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'],
            'is_active' => $validated['is_active'] ?? true,
        ];
    }

    /**
     * Handle category creation
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    private function handleCreate(array $data): void
    {
        $service = app(CategoryCreateService::class);
        $category = $service->create($data);

        session()->flash('success', 'Catégorie créée avec succès.');

        // Redirect to edit page after creation
        $this->redirect(route('admin.categories.edit', $category->id), navigate: true);
    }

    /**
     * Handle category update
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    private function handleUpdate(array $data): void
    {
        $service = app(CategoryUpdateService::class);
        $category = $service->update($this->categoryId, $data);

        $this->dispatch('flash-message', type: 'success', message: 'Catégorie mise à jour avec succès.');

        // Stay on edit page after update (no redirect)
        $this->loadCategory($category->id);
    }

    /**
     * Handle CategoryNotFoundException (business error)
     */
    private function handleCategoryNotFoundException(CategoryNotFoundException $e): void
    {
        session()->flash('error', $e->getMessage());

        Log::warning('Category not found during save', [
            'category_id' => $this->categoryId,
            'mode' => $this->mode,
            'admin_id' => auth()->id(),
        ]);

        $this->redirect(route('admin.categories.index'));
    }

    /**
     * Handle generic exception
     */
    private function handleGenericException(\Throwable $e): void
    {
        $this->dispatch('flash-message', type: 'error', message: 'Une erreur inattendue est survenue. Veuillez réessayer.');

        Log::error('Unexpected error during category save', [
            'mode' => $this->mode,
            'category_id' => $this->categoryId,
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
        if ($this->mode !== 'edit' || ! $this->categoryId) {
            return;
        }

        // Count associated places for warning message
        // Use cached category if available, otherwise load it
        $category = $this->category ?? \App\Models\Category::find($this->categoryId);
        $this->associatedPlacesCount = $category ? $category->places()->count() : 0;

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
     * Confirm and execute category deletion
     */
    public function delete(): void
    {
        if ($this->mode !== 'edit' || ! $this->categoryId) {
            session()->flash('error', 'Action non autorisée.');

            return;
        }

        try {
            $service = app(CategoryUpdateService::class);
            $deleted = $service->delete($this->categoryId);

            if ($deleted) {
                session()->flash('success', 'Catégorie supprimée avec succès.');
                $this->redirect(route('admin.categories.index'), navigate: true);
            } else {
                session()->flash('error', 'La suppression de la catégorie a échoué.');
            }

        } catch (CategoryNotFoundException $e) {
            session()->flash('error', $e->getMessage());
            $this->redirect(route('admin.categories.index'));

        } catch (\Throwable $e) {
            Log::error('Unexpected error during category deletion', [
                'category_id' => $this->categoryId,
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
