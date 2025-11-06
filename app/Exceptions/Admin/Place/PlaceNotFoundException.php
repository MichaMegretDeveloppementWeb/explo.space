<?php

namespace App\Exceptions\Admin\Place;

use Exception;

/**
 * Exception levée lorsqu'un lieu n'est pas trouvé.
 *
 * Cette exception représente une erreur métier (utilisateur)
 * et doit afficher un message clair à l'utilisateur.
 */
class PlaceNotFoundException extends Exception
{
    /**
     * Create a new PlaceNotFoundException instance.
     */
    public function __construct(string $message = "Le lieu que vous essayez de modifier n'existe pas.", int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
