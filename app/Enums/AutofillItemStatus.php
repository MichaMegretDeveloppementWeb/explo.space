<?php

namespace App\Enums;

enum AutofillItemStatus: string
{
    case Discovered = 'discovered';
    case Selected = 'selected';
    case Enriching = 'enriching';
    case Enriched = 'enriched';
    case ImagesSearching = 'images_searching';
    case ImagesFound = 'images_found';
    case Translating = 'translating';
    case Translated = 'translated';
    case Saving = 'saving';
    case Saved = 'saved';
    case Failed = 'failed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Discovered => 'Découvert',
            self::Selected => 'Sélectionné',
            self::Enriching => 'Enrichissement',
            self::Enriched => 'Enrichi',
            self::ImagesSearching => 'Recherche images',
            self::ImagesFound => 'Images trouvées',
            self::Translating => 'Traduction',
            self::Translated => 'Traduit',
            self::Saving => 'Enregistrement',
            self::Saved => 'Enregistré',
            self::Failed => 'Échoué',
            self::Skipped => 'Ignoré',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Saved, self::Failed, self::Skipped]);
    }

    public function isSuccessful(): bool
    {
        return $this === self::Saved;
    }
}
