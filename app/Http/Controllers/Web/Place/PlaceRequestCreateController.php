<?php

namespace App\Http\Controllers\Web\Place;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaceRequestCreateController extends Controller
{
    public function __construct(
        private readonly SeoBuilderAction $seoBuilderAction
    ) {}

    /**
     * Affiche le formulaire de proposition de lieu (/proposer-lieu)
     */
    public function create(Request $request): View
    {
        // Génération des données SEO
        $seo = $this->seoBuilderAction->execute('place-request');

        return view('web.place.place-request', [
            'locale' => app()->getLocale(),
            'seo' => $seo,
        ]);
    }
}
