<?php

namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TagListController extends Controller
{
    /**
     * Display the tag list page
     */
    public function index(): View
    {
        return view('admin.tag.index');
    }
}
