<?php

namespace App\Services\Admin\Settings\Profile;

use App\Contracts\Repositories\Admin\Settings\User\UserManagementRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProfileUpdateService
{
    /**
     * Constructor with repository injection.
     */
    public function __construct(
        private readonly UserManagementRepositoryInterface $userRepository
    ) {}

    /**
     * Update user profile (name and/or email).
     *
     * Si l'email change, email_verified_at est remis à null et un email de vérification est envoyé.
     *
     * @param  array<string, mixed>  $data  ['name' => string, 'email' => string]
     *
     * @throws \InvalidArgumentException Si l'email existe déjà
     */
    public function updateProfile(User $user, array $data): User
    {
        $emailChanged = false;

        // Vérifier si l'email a changé
        if (isset($data['email']) && $data['email'] !== $user->email) {
            // Vérifier l'unicité de l'email
            if ($this->userRepository->existsWithEmail($data['email'], $user->id)) {
                throw new \InvalidArgumentException('Cette adresse email est déjà utilisée.');
            }

            $emailChanged = true;
            $data['email_verified_at'] = null; // Réinitialiser la vérification
        }

        // Mettre à jour via le repository
        $updatedUser = $this->userRepository->update($user, $data);

        // Si l'email a changé, envoyer l'email de vérification
        if ($emailChanged) {
            $updatedUser->sendEmailVerificationNotification();

            Log::info('Email verification sent after profile update', [
                'user_id' => $updatedUser->id,
                'new_email' => $updatedUser->email,
            ]);
        }

        Log::info('User profile updated', [
            'user_id' => $updatedUser->id,
            'email_changed' => $emailChanged,
        ]);

        return $updatedUser;
    }
}
