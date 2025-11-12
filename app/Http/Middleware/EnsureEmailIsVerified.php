<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * Vérifie que l'utilisateur a bien vérifié son email.
     * Redirige vers admin.verification.notice si non vérifié.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        // Vérifier si l'utilisateur est connecté et implémente MustVerifyEmail
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {

            // Si c'est une requête expecting JSON, retourner erreur 403
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your email address is not verified.'], 403);
            }

            // Sinon, rediriger vers la page de vérification admin
            return Redirect::guest(
                URL::route($redirectToRoute ?: 'admin.verification.notice')
            );
        }

        return $next($request);
    }
}
