<?php

namespace App\Http\Controllers\Web\Privacy;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function index(Request $request, SeoBuilderAction $seoBuilderAction): View
    {
        $seo = $seoBuilderAction->execute('privacy');

        return view('web.pages.privacy', compact('seo'));
    }
}
