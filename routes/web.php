<?php

use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Dashboard\AdminDashboardController;
use App\Http\Controllers\Admin\Place\PlaceCreateController;
use App\Http\Controllers\Admin\Place\PlaceDeleteController;
use App\Http\Controllers\Admin\Place\PlaceEditController;
use App\Http\Controllers\Admin\Place\PlaceListController;
use App\Http\Controllers\Admin\Place\PlaceShowController as AdminPlaceShowController;
use App\Http\Controllers\Admin\PlaceRequest\PlaceRequestListController;
use App\Http\Controllers\Admin\PlaceRequest\PlaceRequestShowController;
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

            // Tags
            Route::get('tags/{slug}', function ($locale, $slug) {
                return view('web.tags.show', compact('slug'));
            })->name("tags.show.$locale");

        });
}

// Redirection racine vers langue par défaut
Route::get('/', function () {
    return redirect()->to('/'.config('locales.default').'/', 301);
});

// =====================================
// ROUTES ADMINISTRATION (non localisées)
// =====================================

Route::prefix('admin')->name('admin.')->group(function () {

    // Routes publiques (connexion)
    Route::get('/connexion', [AdminAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/connexion', [AdminAuthController::class, 'login'])
        ->middleware('guest')
        ->name('login.post');

    // Routes protégées (admin connecté uniquement)
    Route::middleware('admin')->group(function () {

        Route::post('/deconnexion', [AdminAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Gestion des lieux
        Route::get('/lieux', [PlaceListController::class, 'index'])
            ->name('places.index');

        Route::get('/lieux/creer', PlaceCreateController::class)
            ->name('places.create');

        Route::get('/lieux/{id}/editer', PlaceEditController::class)
            ->name('places.edit');

        Route::get('/lieux/{id}', [AdminPlaceShowController::class, 'show'])
            ->name('places.show');

        Route::delete('/lieux/{id}', [PlaceDeleteController::class, 'destroy'])
            ->name('places.destroy');

        // Gestion des propositions de lieux
        Route::get('/propositions-lieux', [PlaceRequestListController::class, 'index'])
            ->name('place-requests.index');

        Route::get('/propositions-lieux/{id}', [PlaceRequestShowController::class, 'show'])
            ->name('place-requests.show');

    });
});

// Redirection /admin vers /admin/dashboard
Route::redirect('/admin', '/admin/dashboard', 301);
