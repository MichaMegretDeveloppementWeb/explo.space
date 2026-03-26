<?php

namespace App\Enums;

enum AutofillWorkflowState: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Abandoned = 'abandoned';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Paused => 'En pause',
            self::Abandoned => 'Abandonné',
            self::Completed => 'Terminé',
        };
    }

    /**
     * A "current" workflow is one the admin is working on (active or paused).
     * It occupies a slot — the admin must deal with it before starting a new one.
     */
    public function isCurrent(): bool
    {
        return in_array($this, [self::Active, self::Paused]);
    }

    /**
     * A "dismissed" workflow is definitively done (completed or abandoned).
     * It no longer appears in the chat and doesn't block new workflows.
     */
    public function isDismissed(): bool
    {
        return in_array($this, [self::Completed, self::Abandoned]);
    }
}
