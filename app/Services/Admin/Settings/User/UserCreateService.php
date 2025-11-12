<?php

namespace App\Services\Admin\Settings\User;

use App\Contracts\Repositories\Admin\Settings\User\UserManagementRepositoryInterface;
use App\Mail\Admin\AdminInvitationMail;
use App\Models\User;
use App\Services\Admin\Settings\Invitation\InvitationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserCreateService
{
    /**
     * Constructor with repository and service injection.
     */
    public function __construct(
        private readonly UserManagementRepositoryInterface $userRepository,
        private readonly InvitationService $invitationService
    ) {}

    /**
     * Create a new admin user and send invitation email.
     *
     * @param  array<string, mixed>  $data  ['name' => string, 'email' => string, 'role' => string]
     *
     * @throws \InvalidArgumentException Si l'email existe déjà
     */
    public function createAdmin(array $data): User
    {
        // Vérifier que l'email n'existe pas déjà
        if ($this->userRepository->existsWithEmail($data['email'])) {
            throw new \InvalidArgumentException('Cette adresse email est déjà utilisée.');
        }

        // Générer un mot de passe temporaire aléatoire (sera remplacé via l'invitation)
        $temporaryPassword = Str::random(10);

        // Créer l'utilisateur avec email_verified_at = null (sera vérifié via invitation)
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $temporaryPassword, // Sera hashé automatiquement via le cast
            'email_verified_at' => null,
        ]);

        // Générer le token d'invitation
        $invitation = $this->invitationService->generateInvitationToken($user);

        // Envoyer l'email d'invitation
        Mail::to($user->email)->send(new AdminInvitationMail($user, $invitation->token));

        Log::info('Admin user created and invitation sent', [
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        return $user;
    }
}
