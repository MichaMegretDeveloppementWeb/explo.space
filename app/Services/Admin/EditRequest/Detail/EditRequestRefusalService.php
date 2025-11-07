<?php

namespace App\Services\Admin\EditRequest\Detail;

use App\Enums\RequestStatus;
use App\Models\EditRequest;

/**
 * Service pour gérer le refus d'une demande de modification
 */
class EditRequestRefusalService
{
    /**
     * Refuser une demande de modification
     *
     * Change le statut à "refused", enregistre la raison du refus,
     * et marque la demande comme traitée par l'admin.
     *
     * @param  EditRequest  $editRequest  La demande à refuser
     * @param  int  $adminId  ID de l'admin qui refuse
     * @param  string|null  $reason  Raison du refus (optionnelle)
     * @return bool True si le refus a été enregistré
     */
    public function refuse(EditRequest $editRequest, int $adminId, ?string $reason = null): bool
    {
        // Vérifier que la demande peut être refusée
        /** @var \App\Enums\RequestStatus $status */
        $status = $editRequest->status;

        if (! $status->canBeRefused()) {
            return false;
        }

        // Enregistrer le refus
        $editRequest->update([
            'status' => RequestStatus::Refused,
            'admin_reason' => $reason,
            'processed_by_admin_id' => $adminId,
            'processed_at' => now(),
        ]);

        return true;
    }
}
