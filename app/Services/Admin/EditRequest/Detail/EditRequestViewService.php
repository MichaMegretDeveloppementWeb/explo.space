<?php

namespace App\Services\Admin\EditRequest\Detail;

use App\Enums\RequestStatus;
use App\Models\EditRequest;

/**
 * Service pour gérer le marquage d'une demande de modification comme vue par un admin
 */
class EditRequestViewService
{
    /**
     * Marquer une demande comme vue si elle ne l'a pas encore été
     *
     * Change le statut de "submitted" à "pending" et enregistre l'admin
     * qui a consulté la demande pour la première fois.
     *
     * @return bool True si la demande a été marquée comme vue, false sinon
     */
    public function markAsViewedIfNeeded(EditRequest $editRequest, int $adminId): bool
    {
        // Si déjà vue par un admin, ne rien faire
        if ($editRequest->viewed_by_admin_id !== null) {
            return false;
        }

        // Marquer comme vue
        $editRequest->update([
            'status' => RequestStatus::Pending,
            'viewed_by_admin_id' => $adminId,
            'viewed_at' => now(),
        ]);

        return true;
    }
}
