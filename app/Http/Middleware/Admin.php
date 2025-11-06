<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * Vérifie que l'utilisateur est authentifié ET possède un rôle admin (admin ou super_admin)
     * Sinon, redirige vers la page d'accueil
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->hasAdminRights()) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
