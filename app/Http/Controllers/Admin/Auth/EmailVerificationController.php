<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function show(Request $request): View|RedirectResponse
    {
        // Si déjà vérifié, rediriger vers dashboard
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        // Vérifier l'email
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('admin.dashboard')->with('success', 'Votre adresse email a été vérifiée avec succès.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
    }
}
