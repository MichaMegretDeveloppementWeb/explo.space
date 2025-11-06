<?php

namespace App\Http\Controllers\Admin\EditRequest;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EditRequestListController extends Controller
{
    /**
     * Afficher la liste des demandes de modification/signalement pour l'administration
     */
    public function index(): View
    {
        return view('admin.edit-request.index');
    }
}
