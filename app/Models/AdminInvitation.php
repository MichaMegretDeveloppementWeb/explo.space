<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'token',
        'accepted_at',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user associated with this invitation.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the invitation is still valid (not expired and not accepted).
     */
    public function isValid(): bool
    {
        $expiresAt = $this->expires_at;
        if (! $expiresAt instanceof \Carbon\Carbon) {
            return false;
        }

        return $expiresAt->isFuture() && $this->accepted_at === null;
    }

    /**
     * Check if the invitation has been accepted.
     */
    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Check if the invitation has expired.
     */
    public function isExpired(): bool
    {
        $expiresAt = $this->expires_at;
        if (! $expiresAt instanceof \Carbon\Carbon) {
            return true;
        }

        return $expiresAt->isPast();
    }
}
