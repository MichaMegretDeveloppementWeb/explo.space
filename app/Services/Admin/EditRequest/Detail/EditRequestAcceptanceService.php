<?php

namespace App\Services\Admin\EditRequest\Detail;

use App\Enums\RequestStatus;
use App\Models\EditRequest;

/**
 * Service pour gérer l'acceptation d'un signalement simple
 */
class EditRequestAcceptanceService
{
    /**
     * Accepter un signalement simple
     *
     * Usage : Admin a lu le signalement, fait les modifications manuelles nécessaires,
     * et valide que le signalement est traité.
     *
     * Change le statut à "accepted" et enregistre l'admin qui a traité.
     *
     * @param  EditRequest  $editRequest  Le signalement à accepter
     * @param  int  $adminId  ID de l'admin qui accepte
     * @return bool True si l'acceptation a été enregistrée
     */
    public function acceptSignalement(EditRequest $editRequest, int $adminId): bool
    {
        // Vérifier que c'est bien un signalement
        if (! $editRequest->isSignalement()) {
            return false;
        }

        // Vérifier que le statut permet l'acceptation
        /** @var \App\Enums\RequestStatus $status */
        $status = $editRequest->status;

        if (! $status->canBeAccepted()) {
            return false;
        }

        // Marquer comme accepté
        $editRequest->update([
            'status' => RequestStatus::Accepted,
            'processed_by_admin_id' => $adminId,
            'processed_at' => now(),
        ]);

        return true;
    }
}
