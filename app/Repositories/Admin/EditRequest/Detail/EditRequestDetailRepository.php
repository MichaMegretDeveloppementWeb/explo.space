<?php

namespace App\Repositories\Admin\EditRequest\Detail;

use App\Contracts\Repositories\Admin\EditRequest\Detail\EditRequestDetailRepositoryInterface;
use App\Models\EditRequest;

/**
 * Repository pour la gestion du détail des demandes de modification
 */
class EditRequestDetailRepository implements EditRequestDetailRepositoryInterface
{
    /**
     * Find EditRequest by ID with all relations for detail view
     *
     * @param  int  $id  ID de la demande de modification
     * @return EditRequest|null La demande avec ses relations, ou null si non trouvée
     */
    public function findWithRelations(int $id): ?EditRequest
    {
        return EditRequest::with([
            'place.translations',
            'place.photos',
            'viewedByAdmin:id,name',
            'processedByAdmin:id,name',
        ])->find($id);
    }
}
