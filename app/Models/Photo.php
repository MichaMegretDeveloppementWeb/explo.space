<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'alt_text',
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

    /**
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/photos/'.$this->filename);
    }

    public function getThumbUrlAttribute(): string
    {
        $pathinfo = pathinfo($this->filename);
        $thumbFilename = $pathinfo['filename'].'_thumb.'.$pathinfo['extension'];

        return asset('storage/photos/thumbs/'.$thumbFilename);
    }

    public function getMediumUrlAttribute(): string
    {
        $pathinfo = pathinfo($this->filename);
        $mediumFilename = $pathinfo['filename'].'_medium.'.$pathinfo['extension'];

        return asset('storage/photos/medium/'.$mediumFilename);
    }
}
