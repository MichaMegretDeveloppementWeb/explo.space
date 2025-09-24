<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlaceRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PlaceRequestFactory> */
    use HasFactory;

    // Les visiteurs proposent des lieux sans sélectionner de tags
    // L'admin assigne tags et catégories lors de la validation

    protected $fillable = [
        'contact_email',
        'detected_language',
        'title',
        'slug',
        'description',
        'latitude',
        'longitude',
        'address',
        'practical_info',
        'status',
        'admin_reason',
        'viewed_by_admin_id',
        'processed_by_admin_id',
        'viewed_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'viewed_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function viewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewed_by_admin_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function processedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_admin_id');
    }

    /**
     * @return HasOne<Place, $this>
     */
    public function place(): HasOne
    {
        return $this->hasOne(Place::class, 'request_id');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRefused(): bool
    {
        return $this->status === 'refused';
    }
}
