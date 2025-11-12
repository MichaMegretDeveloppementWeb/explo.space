<?php

namespace App\Exceptions\Admin\Category;

use Exception;

/**
 * Exception levée lorsqu'une catégorie n'est pas trouvée.
 *
 * Cette exception représente une erreur métier (utilisateur)
 * et doit afficher un message clair à l'utilisateur.
 */
class CategoryNotFoundException extends Exception
{
    /**
     * Create a new CategoryNotFoundException instance.
     */
    public function __construct(string $message = "La catégorie que vous essayez de modifier n'existe pas.", int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
