<?php

namespace App\Http\Controllers\Web;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use App\Services\Web\HomeService;
use DeepL\DeepLException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage
     *
     * @throws DeepLException
     */
    public function index(Request $request, HomeService $homeService, SeoBuilderAction $seoBuilderAction): View
    {

        // Récupérer les 3 derniers lieux featured avec leurs relations
        $featuredPlaces = $homeService->getFeaturedPlaces();

        // Alternates multilingues pour la homepage
        $alternates = [
            'fr' => url('/fr'),
            'en' => url('/en'),
        ];

        $seo = $seoBuilderAction->execute('homepage');

        return view('web.home.homepage', compact('featuredPlaces', 'seo'));
    }
}
