<?php

namespace App\Services\Admin\Settings\Invitation;

use App\Contracts\Repositories\Admin\Settings\Invitation\InvitationRepositoryInterface;
use App\Models\AdminInvitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvitationService
{
    /**
     * Constructor with repository injection.
     */
    public function __construct(
        private readonly InvitationRepositoryInterface $invitationRepository
    ) {}

    /**
     * Generate a unique invitation token for a user.
     *
     * Le token expire dans 7 jours.
     */
    public function generateInvitationToken(User $user): AdminInvitation
    {
        // Générer un token unique
        $token = Str::random(64);

        // Définir l'expiration à 7 jours
        $expiresAt = Carbon::now()->addDays(7);

        // Créer l'invitation via le repository
        $invitation = $this->invitationRepository->create($user->id, $token, $expiresAt);

        Log::info('Admin invitation created', [
            'user_id' => $user->id,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        return $invitation;
    }

    /**
     * Validate and retrieve invitation by token.
     *
     * @throws \InvalidArgumentException Si le token est invalide ou expiré
     */
    public function validateToken(string $token): AdminInvitation
    {
        $invitation = $this->invitationRepository->findValidToken($token);

        if (! $invitation) {
            throw new \InvalidArgumentException('Le lien d\'invitation est invalide ou a expiré.');
        }

        return $invitation;
    }

    /**
     * Mark an invitation as accepted.
     */
    public function markAsAccepted(AdminInvitation $invitation): AdminInvitation
    {
        $acceptedInvitation = $this->invitationRepository->markAsAccepted($invitation);

        Log::info('Admin invitation accepted', [
            'user_id' => $invitation->user_id,
            'invitation_id' => $invitation->id,
        ]);

        return $acceptedInvitation;
    }

    /**
     * Clean up expired invitations (for scheduled task).
     */
    public function cleanupExpiredInvitations(): int
    {
        $deletedCount = $this->invitationRepository->deleteExpired();

        if ($deletedCount > 0) {
            Log::info('Expired invitations cleaned up', [
                'count' => $deletedCount,
            ]);
        }

        return $deletedCount;
    }
}
