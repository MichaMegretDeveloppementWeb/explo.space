<?php

namespace App\Exceptions\Web\Place\Show;

use Exception;

class PlaceTranslationNotFoundException extends Exception
{
    /**
     * Créer une exception pour une traduction de lieu introuvable
     *
     * @param  int  $placeId  L'ID du lieu
     * @param  string  $locale  La locale recherchée
     */
    public function __construct(int $placeId, string $locale)
    {
        $message = "Translation not found for place ID '{$placeId}' in locale '{$locale}'.";

        parent::__construct($message, 404);
    }

    /**
     * Obtenir le contexte de l'exception
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'place_id' => $this->getPlaceIdFromMessage(),
            'locale' => $this->getLocaleFromMessage(),
        ];
    }

    /**
     * Extraire l'ID du lieu du message d'erreur
     */
    private function getPlaceIdFromMessage(): ?int
    {
        if (preg_match("/place ID '(\d+)'/", $this->getMessage(), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extraire la locale du message d'erreur
     */
    private function getLocaleFromMessage(): ?string
    {
        if (preg_match("/locale '([^']+)'/", $this->getMessage(), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
