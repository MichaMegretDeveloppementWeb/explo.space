<?php

namespace App\Contracts\Repositories\Admin\EditRequest\Detail;

use App\Models\EditRequest;

/**
 * Interface pour le repository de détail des demandes de modification
 */
interface EditRequestDetailRepositoryInterface
{
    /**
     * Find EditRequest by ID with all relations for detail view
     *
     * Relations loaded:
     * - place.translations
     * - place.photos
     * - viewedByAdmin (select id, name)
     * - processedByAdmin (select id, name)
     *
     * @param  int  $id  ID de la demande de modification
     * @return EditRequest|null La demande avec ses relations, ou null si non trouvée
     */
    public function findWithRelations(int $id): ?EditRequest;
}
