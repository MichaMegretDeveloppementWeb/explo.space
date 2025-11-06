<?php

namespace App\Services\Admin\PlaceRequest\Detail;

use App\Enums\RequestStatus;
use App\Models\PlaceRequest;

/**
 * Service pour gérer le refus d'une proposition de lieu
 */
class PlaceRequestRefusalService
{
    /**
     * Refuser une proposition de lieu
     *
     * Change le statut à "refused", enregistre la raison du refus,
     * et marque la proposition comme traitée par l'admin.
     *
     * @param  PlaceRequest  $placeRequest  La proposition à refuser
     * @param  int  $adminId  ID de l'admin qui refuse
     * @param  string|null  $reason  Raison du refus (optionnelle)
     * @return bool True si le refus a été enregistré
     */
    public function refuse(PlaceRequest $placeRequest, int $adminId, ?string $reason = null): bool
    {
        // Vérifier que la proposition peut être refusée
        /** @var \App\Enums\RequestStatus $status */
        $status = $placeRequest->status;

        if (! $status->canBeRefused()) {
            return false;
        }

        // Enregistrer le refus
        $placeRequest->update([
            'status' => RequestStatus::Refused,
            'admin_reason' => $reason,
            'processed_by_admin_id' => $adminId,
            'processed_at' => now(),
        ]);

        return true;
    }
}
