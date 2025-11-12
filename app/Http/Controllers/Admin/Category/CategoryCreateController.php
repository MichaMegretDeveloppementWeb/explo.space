<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;

class CategoryCreateController extends Controller
{
    /**
     * Display the category creation form.
     *
     * This controller handles direct creation (admin-initiated): /admin/categories/creer
     */
    public function __invoke(): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if (! auth()->check() || ! auth()->user()->hasAdminRights()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.category.create');
    }
}
