<?php

namespace App\Exceptions\Admin\Tag;

use Exception;

/**
 * Exception levée lorsqu'un tag n'est pas trouvé.
 *
 * Cette exception représente une erreur métier (utilisateur)
 * et doit afficher un message clair à l'utilisateur.
 */
class TagNotFoundException extends Exception
{
    /**
     * Create a new TagNotFoundException instance.
     */
    public function __construct(string $message = "Le tag que vous essayez de modifier n'existe pas.", int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
