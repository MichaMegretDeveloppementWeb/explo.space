<?php

namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;

class TagEditController extends Controller
{
    /**
     * Display the tag edit form.
     *
     * The Tag model is loaded once in the Livewire component
     * via TagUpdateService::loadForEdit() method.
     */
    public function __invoke(int $id): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->hasAdminRights()) {
            return redirect('/')->with('error', 'Accès non autorisé.');
        }

        // Note: Tag existence is validated in Livewire component mount()
        // This avoids duplicate queries
        return view('admin.tag.edit', [
            'tagId' => $id,
        ]);
    }
}
