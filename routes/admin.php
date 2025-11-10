<?php

// =====================================
// ROUTES ADMINISTRATION (non localisées)
// =====================================

use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Dashboard\AdminDashboardController;
use App\Http\Controllers\Admin\EditRequest\EditRequestListController;
use App\Http\Controllers\Admin\EditRequest\EditRequestShowController;
use App\Http\Controllers\Admin\Place\PlaceCreateController;
use App\Http\Controllers\Admin\Place\PlaceDeleteController;
use App\Http\Controllers\Admin\Place\PlaceEditController;
use App\Http\Controllers\Admin\Place\PlaceListController;
use App\Http\Controllers\Admin\Place\PlaceShowController as AdminPlaceShowController;
use App\Http\Controllers\Admin\PlaceRequest\PlaceRequestListController;
use App\Http\Controllers\Admin\PlaceRequest\PlaceRequestShowController;
use App\Http\Controllers\Admin\Tag\TagListController;
use Illuminate\Support\Facades\Route;

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

        // Gestion des demandes de modification/signalement
        Route::get('/modifications-signalements', [EditRequestListController::class, 'index'])
            ->name('edit-requests.index');

        Route::get('/modifications-signalements/{id}', [EditRequestShowController::class, 'show'])
            ->name('edit-requests.show');

        // Gestion des tags
        Route::get('/tags', [TagListController::class, 'index'])
            ->name('tags.index');

    });
});

// Redirection /admin vers /admin/dashboard
Route::redirect('/admin', '/admin/dashboard', 301);
