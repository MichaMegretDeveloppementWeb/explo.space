<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Contracts\Services\Admin\Auth\AdminAuthenticationServiceInterface;
use App\DTO\Admin\Auth\LoginCredentialsDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Service d'authentification admin
     */
    protected AdminAuthenticationServiceInterface $authService;

    /**
     * Injection du service d'authentification
     */
    public function __construct(AdminAuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Afficher le formulaire de connexion admin
     *
     * Si l'utilisateur est déjà connecté et admin, redirection vers dashboard
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Si déjà connecté et admin, redirection vers dashboard
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Traiter la tentative de connexion
     *
     * Responsabilité : Validation des entrées + Appel du service + Gestion des redirections/erreurs
     */
    public function login(Request $request): RedirectResponse
    {
        // Validation des champs
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Vérifier le rate limiting
        $throttleKey = $this->authService->generateThrottleKey(
            $request->input('email'),
            $request->ip()
        );

        if ($this->authService->isRateLimited($throttleKey)) {
            $seconds = $this->authService->getRateLimitSeconds($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes.",
            ]);
        }

        // Créer le DTO avec les credentials
        $credentials = LoginCredentialsDTO::fromArray([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'remember' => $request->boolean('remember'),
            'ip' => $request->ip(),
        ]);

        // Appeler le service d'authentification
        $result = $this->authService->authenticate($credentials);

        // Gestion du résultat
        if ($result->success) {
            // Régénérer la session pour sécurité (responsabilité HTTP)
            $request->session()->regenerate();

            return redirect()->intended($result->redirectUrl);
        }

        // Échec : retourner erreur de validation
        throw ValidationException::withMessages([
            'email' => $result->errorMessage,
        ]);
    }

    /**
     * Déconnecter l'utilisateur admin
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect('/');
    }
}
