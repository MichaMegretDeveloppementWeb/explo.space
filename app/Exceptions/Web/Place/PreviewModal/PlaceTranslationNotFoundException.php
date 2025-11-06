<?php

namespace App\Exceptions\Web\Place\PreviewModal;

use Exception;

class PlaceTranslationNotFoundException extends Exception
{
    /**
     * Exception levée quand la traduction d'un lieu n'est pas trouvée pour la locale active
     *
     * @param  int  $placeId  ID du lieu
     * @param  string  $locale  Locale recherchée
     * @param  int  $code  Code d'erreur HTTP (404 par défaut)
     * @param  Exception|null  $previous  Exception précédente pour chaînage
     */
    public function __construct(int $placeId, string $locale, int $code = 404, ?Exception $previous = null)
    {
        $message = "Translation for place ID {$placeId} not found for locale '{$locale}' (status: published)";

        parent::__construct($message, $code, $previous);
    }
}
