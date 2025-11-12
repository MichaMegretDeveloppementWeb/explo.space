<?php

namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;

class TagCreateController extends Controller
{
    /**
     * Display the tag creation form.
     *
     * This controller handles direct creation (admin-initiated): /admin/tags/creer
     */
    public function __invoke(): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->hasAdminRights()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.tag.create');
    }
}
