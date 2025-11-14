<?php

// =====================================
// ROUTES ADMINISTRATION (non localisées)
// =====================================

use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Auth\EmailVerificationController;
use App\Http\Controllers\Admin\Auth\InvitationController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;
use App\Http\Controllers\Admin\Category\CategoryCreateController;
use App\Http\Controllers\Admin\Category\CategoryEditController;
use App\Http\Controllers\Admin\Category\CategoryListController;
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
use App\Http\Controllers\Admin\Settings\SettingsShowController;
use App\Http\Controllers\Admin\Tag\TagCreateController;
use App\Http\Controllers\Admin\Tag\TagEditController;
use App\Http\Controllers\Admin\Tag\TagListController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // =====================================
    // Routes publiques (non authentifiées)
    // =====================================

    // Connexion
    Route::get('/connexion', [AdminAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/connexion', [AdminAuthController::class, 'login'])
        ->middleware('guest')
        ->name('login.post');

    // Password Reset
    Route::get('/password/reset', [PasswordResetController::class, 'showLinkRequestForm'])
        ->middleware('guest')
        ->name('password.request');

    Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->middleware('guest')
        ->name('password.email');

    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])
        ->middleware('guest')
        ->name('password.reset');

    Route::post('/password/reset', [PasswordResetController::class, 'reset'])
        ->middleware('guest')
        ->name('password.update');

    // Admin Invitation
    Route::get('/invitation/{token}', [InvitationController::class, 'accept'])
        ->name('invitation.accept');

    // =====================================
    // Routes authentifiées (sans verification email)
    // =====================================

    Route::middleware('auth')->group(function () {

        // Email Verification
        Route::get('/email/verify', [EmailVerificationController::class, 'show'])
            ->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');

        Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');
    });

    // =====================================
    // Routes protégées (admin + verified)
    // =====================================

    Route::middleware(['admin', 'verified'])->group(function () {

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

        Route::get('/tags/creer', TagCreateController::class)
            ->name('tags.create');

        Route::get('/tags/{id}/editer', TagEditController::class)
            ->name('tags.edit');

        // Gestion des catégories
        Route::get('/categories', [CategoryListController::class, 'index'])
            ->name('categories.index');

        Route::get('/categories/creer', CategoryCreateController::class)
            ->name('categories.create');

        Route::get('/categories/{id}/editer', CategoryEditController::class)
            ->name('categories.edit');

        // Paramètres (Settings)
        Route::get('/parametres', [SettingsShowController::class, 'show'])
            ->name('settings.show');

    });
});

// Redirection /admin vers /admin/dashboard
Route::redirect('/admin', '/admin/dashboard', 301);
