<?php

namespace App\Exceptions\Web\Place\PreviewModal;

use Exception;

class PlaceNotFoundException extends Exception
{
    /**
     * Exception levée quand un lieu n'est pas trouvé lors de la récupération pour la modale de prévisualisation
     *
     * @param  int  $placeId  ID du lieu recherché
     * @param  int  $code  Code d'erreur HTTP (404 par défaut)
     * @param  Exception|null  $previous  Exception précédente pour chaînage
     */
    public function __construct(int $placeId, int $code = 404, ?Exception $previous = null)
    {
        $message = "Place with ID {$placeId} not found in database for preview modal";

        parent::__construct($message, $code, $previous);
    }
}
