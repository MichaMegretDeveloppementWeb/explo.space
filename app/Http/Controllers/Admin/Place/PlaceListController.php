<?php

namespace App\Http\Controllers\Admin\Place;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceListController extends Controller
{
    /**
     * Afficher la liste des lieux pour l'administration
     */
    public function index(): View
    {
        return view('admin.place.index');
    }
}
