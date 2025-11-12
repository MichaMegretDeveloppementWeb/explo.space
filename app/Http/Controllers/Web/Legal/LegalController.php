<?php

namespace App\Http\Controllers\Web\Legal;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * Display the legal notice page
     */
    public function index(Request $request, SeoBuilderAction $seoBuilderAction): View
    {
        $seo = $seoBuilderAction->execute('legal');

        return view('web.pages.legal', compact('seo'));
    }
}
