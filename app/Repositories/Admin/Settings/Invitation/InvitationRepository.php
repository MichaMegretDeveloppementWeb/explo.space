<?php

namespace App\Repositories\Admin\Settings\Invitation;

use App\Contracts\Repositories\Admin\Settings\Invitation\InvitationRepositoryInterface;
use App\Models\AdminInvitation;
use Carbon\Carbon;

class InvitationRepository implements InvitationRepositoryInterface
{
    /**
     * Create a new admin invitation.
     */
    public function create(int $userId, string $token, \DateTimeInterface $expiresAt): AdminInvitation
    {
        return AdminInvitation::create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Find a valid (non-expired, non-accepted) invitation by token.
     */
    public function findValidToken(string $token): ?AdminInvitation
    {
        return AdminInvitation::with('user')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Mark an invitation as accepted.
     */
    public function markAsAccepted(AdminInvitation $invitation): AdminInvitation
    {
        $invitation->update([
            'accepted_at' => Carbon::now(),
        ]);

        return $invitation->fresh();
    }

    /**
     * Delete expired invitations (cleanup).
     */
    public function deleteExpired(): int
    {
        return AdminInvitation::where('expires_at', '<', Carbon::now())
            ->whereNull('accepted_at')
            ->delete();
    }
}
