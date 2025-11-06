<?php

namespace App\Repositories\Web\Place\EditRequest;

use App\Contracts\Repositories\Web\Place\EditRequest\EditRequestCreateRepositoryInterface;
use App\Models\EditRequest;

class EditRequestCreateRepository implements EditRequestCreateRepositoryInterface
{
    /**
     * Créer une nouvelle demande de modification/signalement
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): EditRequest
    {
        return EditRequest::create($data);
    }
}
