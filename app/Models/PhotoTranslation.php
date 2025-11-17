<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_id',
        'locale',
        'alt_text',
    ];

    protected function casts(): array
    {
        return [
            'locale' => 'string',
        ];
    }

    /**
     * @return BelongsTo<Photo, $this>
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Scope for specific locale
     *
     * @param  Builder<PhotoTranslation>  $query
     */
    public function scopeForLocale(Builder $query, string $locale): void
    {
        $query->where('locale', $locale);
    }
}
