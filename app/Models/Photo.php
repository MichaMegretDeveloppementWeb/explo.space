<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read string $url URL complète de la photo
 * @property-read string $thumb_url URL de la miniature (150x150)
 * @property-read string $medium_url URL de la taille moyenne (400px)
 */
class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'place_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'is_main',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'sort_order' => 'integer',
            'size' => 'integer',
        ];
    }

    private string $disk = 'place_photos';

    /**
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PhotoTranslation::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->filename);
    }

    public function getThumbUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url('thumbs/'.$this->filename);
    }

    public function getMediumUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url('medium/'.$this->filename);
    }
}
