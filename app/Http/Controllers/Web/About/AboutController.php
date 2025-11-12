<?php

namespace App\Http\Controllers\Web\About;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the about page
     */
    public function index(Request $request, SeoBuilderAction $seoBuilderAction): View
    {
        $seo = $seoBuilderAction->execute('about');

        return view('web.pages.about', compact('seo'));
    }
}
