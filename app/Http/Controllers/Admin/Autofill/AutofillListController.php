<?php

namespace App\Http\Controllers\Admin\Autofill;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AutofillListController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.autofill.index');
    }
}
