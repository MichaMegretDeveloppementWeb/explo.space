<?php

namespace App\Contracts\Repositories\Web\Place\EditRequest;

use App\Models\EditRequest;

interface EditRequestCreateRepositoryInterface
{
    /**
     * Créer une nouvelle demande de modification/signalement
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): EditRequest;
}
