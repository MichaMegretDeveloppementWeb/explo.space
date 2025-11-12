<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;

class CategoryEditController extends Controller
{
    /**
     * Display the category edit form.
     *
     * The Category model is loaded once in the Livewire component
     * via CategoryUpdateService::loadForEdit() method.
     */
    public function __invoke(int $id): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->hasAdminRights()) {
            return redirect('/')->with('error', 'Accès non autorisé.');
        }

        // Note: Category existence is validated in Livewire component mount()
        // This avoids duplicate queries
        return view('admin.category.edit', [
            'categoryId' => $id,
        ]);
    }
}
