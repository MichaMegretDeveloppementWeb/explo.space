<?php

namespace App\Contracts\Services\Admin\EditRequest\Detail;

use App\Models\EditRequest;

interface EditRequestTranslationServiceInterface
{
    /**
     * Traduire un champ spécifique dans suggested_changes
     *
     * @param  string  $fieldName  Le nom du champ à traduire
     * @return bool True si la traduction a réussi
     */
    public function translateField(EditRequest $editRequest, string $fieldName): bool;

    /**
     * Traduire la description de l'EditRequest
     *
     * @return bool True si la traduction a réussi
     */
    public function translateDescription(EditRequest $editRequest): bool;
}
