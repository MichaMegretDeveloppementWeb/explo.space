<?php

namespace App\Exceptions\Web\Place\Show;

use Exception;

class PlaceNotFoundException extends Exception
{
    private string $slug;

    private string $locale;

    /**
     * Créer une exception pour un lieu introuvable
     *
     * @param  string  $slug  Le slug du lieu recherché
     * @param  string  $locale  La locale de recherche
     */
    public function __construct(string $slug, string $locale)
    {
        $this->slug = $slug;
        $this->locale = $locale;

        $message = "Place not found with slug '{$slug}' for locale '{$locale}'.";

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
            'slug' => $this->slug,
            'locale' => $this->locale,
        ];
    }
}
