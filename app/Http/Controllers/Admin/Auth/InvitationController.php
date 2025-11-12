<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\Admin\Settings\Invitation\InvitationService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    /**
     * Constructor with service injection.
     */
    public function __construct(
        private readonly InvitationService $invitationService
    ) {}

    /**
     * Accept an admin invitation.
     *
     * Workflow:
     * 1. Valider le token d'invitation
     * 2. Connecter automatiquement l'utilisateur
     * 3. Marquer l'email comme vérifié
     * 4. Marquer l'invitation comme acceptée
     * 5. Rediriger vers la page paramètres avec message pour changer le mot de passe
     */
    public function accept(string $token): RedirectResponse
    {
        try {
            // Valider le token et récupérer l'invitation
            $invitation = $this->invitationService->validateToken($token);

            $user = $invitation->user;

            // Connecter automatiquement l'utilisateur
            Auth::login($user);

            // Marquer l'email comme vérifié
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            // Marquer l'invitation comme acceptée
            $this->invitationService->markAsAccepted($invitation);

            Log::info('Admin invitation accepted successfully', [
                'user_id' => $user->id,
                'invitation_id' => $invitation->id,
            ]);

            // Rediriger vers la page paramètres (onglet password) avec message pour changer le mot de passe
            return redirect()->route('admin.settings.show', ['tab' => 'password'])
                ->with('info', 'Bienvenue ! Veuillez changer votre mot de passe pour sécuriser votre compte.');

        } catch (\InvalidArgumentException $e) {
            Log::warning('Invalid invitation token attempt', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.login')
                ->with('error', 'Le lien d\'invitation est invalide ou a expiré. Veuillez contacter un administrateur.');
        }
    }
}
