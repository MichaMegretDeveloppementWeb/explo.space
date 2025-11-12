<?php

namespace App\Http\Controllers\Web\Contact;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the contact page
     */
    public function index(Request $request, SeoBuilderAction $seoBuilderAction): View
    {
        $seo = $seoBuilderAction->execute('contact');

        return view('web.pages.contact', compact('seo'));
    }
}
