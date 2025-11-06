<?php

namespace App\Http\Controllers\Web\Place;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExplorePlaceController extends Controller
{
    public function __construct(
        private readonly SeoBuilderAction $seoBuilderAction
    ) {}

    /**
     * Affiche la page d'exploration des lieux (/explorer)
     */
    public function index(Request $request): View
    {
        // Récupération des paramètres bruts (validation sera faite côté Livewire)
        $filters = $request->only([
            'mode', 'lat', 'lng', 'radius', 'address', 'tags', 'page',
        ]);

        // Génération des données SEO
        $seo = $this->seoBuilderAction->execute('explore');

        // Transmission des données initiales pour les composants Livewire
        return view('web.place.index', [
            'filters' => $filters,
            'locale' => app()->getLocale(),
            'seo' => $seo,
        ]);
    }
}
