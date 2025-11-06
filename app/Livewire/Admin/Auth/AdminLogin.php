<?php

namespace App\Livewire\Admin\Auth;

use App\Contracts\Services\Admin\Auth\AdminAuthenticationServiceInterface;
use App\DTO\Admin\Auth\LoginCredentialsDTO;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class AdminLogin extends Component
{
    #[Rule('required|email', message: [
        'required' => 'L\'adresse email est obligatoire.',
        'email' => 'L\'adresse email doit être valide.',
    ])]
    public string $email = '';

    #[Rule('required', message: 'Le mot de passe est obligatoire.')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Service d'authentification admin
     */
    protected AdminAuthenticationServiceInterface $authService;

    /**
     * Injection du service d'authentification
     */
    public function boot(AdminAuthenticationServiceInterface $authService): void
    {
        $this->authService = $authService;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.auth.admin-login');
    }

    /**
     * Tenter d'authentifier l'utilisateur
     *
     * Responsabilité : Validation des entrées + Appel du service + Gestion des redirections/erreurs UI
     */
    public function authenticate(): void
    {
        // Validation des entrées
        $this->validate();

        // Vérifier le rate limiting avant d'appeler le service
        $throttleKey = $this->authService->generateThrottleKey($this->email, request()->ip());

        if ($this->authService->isRateLimited($throttleKey)) {
            $seconds = $this->authService->getRateLimitSeconds($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes.",
            ]);
        }

        // Créer le DTO avec les credentials
        $credentials = LoginCredentialsDTO::fromArray([
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
            'ip' => request()->ip(),
        ]);

        // Appeler le service d'authentification
        $result = $this->authService->authenticate($credentials);

        // Gestion du résultat
        if ($result->success) {
            // Régénérer la session pour sécurité (responsabilité HTTP)
            session()->regenerate();

            // Succès : redirection vers le dashboard
            $this->redirectIntended($result->redirectUrl);

            return;
        }

        // Échec : reset password + affichage erreur
        $this->password = '';
        $this->addError('email', $result->errorMessage);
    }
}
