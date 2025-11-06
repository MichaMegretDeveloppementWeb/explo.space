<?php

namespace App\Http\Controllers\Admin\PlaceRequest;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceRequestListController extends Controller
{
    /**
     * Afficher la liste des propositions de lieux pour l'administration
     */
    public function index(): View
    {
        return view('admin.place-request.index');
    }
}
