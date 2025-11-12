<?php

namespace App\Services\Admin\Settings\Password;

use App\Contracts\Repositories\Admin\Settings\User\UserManagementRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordUpdateService
{
    /**
     * Constructor with repository injection.
     */
    public function __construct(
        private readonly UserManagementRepositoryInterface $userRepository
    ) {}

    /**
     * Update user password.
     *
     * @throws \InvalidArgumentException Si le nouveau mot de passe est identique à l'ancien
     */
    public function updatePassword(User $user, string $newPassword): User
    {
        // Vérifier que le nouveau mot de passe est différent de l'ancien (optionnel)
        if (Hash::check($newPassword, $user->password)) {
            throw new \InvalidArgumentException('Le nouveau mot de passe doit être différent de l\'ancien.');
        }

        // Mettre à jour le mot de passe (sera hashé automatiquement via le cast)
        $updatedUser = $this->userRepository->update($user, [
            'password' => $newPassword,
        ]);

        Log::info('User password updated', [
            'user_id' => $updatedUser->id,
        ]);

        return $updatedUser;
    }
}
