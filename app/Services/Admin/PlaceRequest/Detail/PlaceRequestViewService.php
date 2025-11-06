<?php

namespace App\Services\Admin\PlaceRequest\Detail;

use App\Enums\RequestStatus;
use App\Models\PlaceRequest;

/**
 * Service pour gérer le marquage d'une proposition de lieu comme vue par un admin
 */
class PlaceRequestViewService
{
    /**
     * Marquer une proposition comme vue si elle ne l'a pas encore été
     *
     * Change le statut de "submitted" à "pending" et enregistre l'admin
     * qui a consulté la proposition pour la première fois.
     *
     * @return bool True si la proposition a été marquée comme vue, false sinon
     */
    public function markAsViewedIfNeeded(PlaceRequest $placeRequest, int $adminId): bool
    {
        // Si déjà vue par un admin, ne rien faire
        if ($placeRequest->viewed_by_admin_id !== null) {
            return false;
        }

        // Marquer comme vue
        $placeRequest->update([
            'status' => RequestStatus::Pending,
            'viewed_by_admin_id' => $adminId,
            'viewed_at' => now(),
        ]);

        return true;
    }
}
