<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view the settings page.
     *
     * Tous les admins (admin + super_admin) peuvent accéder aux paramètres.
     */
    public function viewSettings(User $user): bool
    {
        return $user->hasAdminRights();
    }

    /**
     * Determine if the user can update their own profile.
     *
     * Un admin peut uniquement modifier son propre profil.
     */
    public function updateOwnProfile(User $authUser, User $targetUser): bool
    {
        return $authUser->hasAdminRights() && $authUser->id === $targetUser->id;
    }

    /**
     * Determine if the user can update their own password.
     *
     * Un admin peut uniquement modifier son propre mot de passe.
     */
    public function updateOwnPassword(User $authUser, User $targetUser): bool
    {
        return $authUser->hasAdminRights() && $authUser->id === $targetUser->id;
    }

    /**
     * Determine if the user can create new admin users.
     *
     * Seuls les super_admin peuvent créer des comptes administrateurs.
     */
    public function createUser(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the list of all admins.
     *
     * Seuls les super_admin peuvent voir la liste complète des administrateurs.
     */
    public function viewUsersList(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the admin list page.
     *
     * Seuls les super_admin peuvent voir la liste des administrateurs.
     */
    public function viewAdminList(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can change another admin's role.
     *
     * Seuls les super_admin peuvent changer les rôles.
     * Un super_admin ne peut pas changer son propre rôle.
     */
    public function changeRole(User $authUser, User $targetUser): bool
    {
        return $authUser->isSuperAdmin() && $authUser->id !== $targetUser->id;
    }

    /**
     * Determine if the user can delete another admin.
     *
     * Seuls les super_admin peuvent supprimer des administrateurs.
     * Un super_admin ne peut pas se supprimer lui-même.
     */
    public function deleteAdmin(User $authUser, User $targetUser): bool
    {
        return $authUser->isSuperAdmin() && $authUser->id !== $targetUser->id;
    }
}
