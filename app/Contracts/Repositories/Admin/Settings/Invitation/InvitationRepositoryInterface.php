<?php

namespace App\Contracts\Repositories\Admin\Settings\Invitation;

use App\Models\AdminInvitation;

interface InvitationRepositoryInterface
{
    /**
     * Create a new admin invitation.
     */
    public function create(int $userId, string $token, \DateTimeInterface $expiresAt): AdminInvitation;

    /**
     * Find a valid (non-expired, non-accepted) invitation by token.
     */
    public function findValidToken(string $token): ?AdminInvitation;

    /**
     * Mark an invitation as accepted.
     */
    public function markAsAccepted(AdminInvitation $invitation): AdminInvitation;

    /**
     * Delete expired invitations (cleanup).
     */
    public function deleteExpired(): int;
}
