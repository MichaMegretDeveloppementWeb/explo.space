<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CategoryListController extends Controller
{
    /**
     * Display the category list page
     */
    public function index(): View
    {
        return view('admin.category.index');
    }
}
