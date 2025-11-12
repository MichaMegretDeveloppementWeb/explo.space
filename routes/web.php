<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Place\ExplorePlaceController;
use App\Http\Controllers\Web\Place\PlaceRequestCreateController;
use App\Http\Controllers\Web\Place\PlaceShowController as WebPlaceShowController;
use App\Support\LocaleUrl;
use Illuminate\Support\Facades\Route;

$locales = config('locales.supported');

foreach ($locales as $locale) {
    Route::prefix($locale)
        ->middleware("set-locale:$locale")
        ->group(function () use ($locale) {

            // Homepage
            Route::get(
                '/',
                [HomeController::class, 'index']
            )->name("home.$locale");

            // Pages statiques
            Route::get(LocaleUrl::segment('about', $locale), function () {
                return view('web.pages.about');
            })->name("about.$locale");

            Route::get(LocaleUrl::segment('features', $locale), function () {
                return view('web.pages.features');
            })->name("features.$locale");

            Route::get(LocaleUrl::segment('contact', $locale), function () {
                return view('web.pages.contact');
            })->name("contact.$locale");

            // Explorer
            Route::get(
                LocaleUrl::segment('explore', $locale),
                [ExplorePlaceController::class, 'index']
            )->name("explore.$locale");

            // Detail lieu
            Route::get(
                LocaleUrl::segment('places', $locale).'/{slug}',
                [WebPlaceShowController::class, 'show']
            )->name("places.show.$locale");

            // Proposer un lieu (Livewire handles submission, no POST route needed)
            Route::get(
                LocaleUrl::segment('propose_place', $locale),
                [PlaceRequestCreateController::class, 'create']
            )->name("place_requests.create.$locale");

        });
}

// Redirection racine vers langue par défaut
Route::get('/', function () {
    return redirect()->to('/'.config('locales.default').'/', 301);
});

include 'admin.php';
