<?php

namespace App\Models;

use Database\Factories\PlaceRequestPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @method static PlaceRequestPhotoFactory factory($count = null, $state = [])
 */
class PlaceRequestPhoto extends Model
{
    /** @use HasFactory<PlaceRequestPhotoFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'place_request_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the place request that owns the photo.
     *
     * @return BelongsTo<PlaceRequest, $this>
     */
    public function placeRequest(): BelongsTo
    {
        return $this->belongsTo(PlaceRequest::class);
    }

    /**
     * Disk used for storage
     */
    private string $disk = 'place_request_photos';

    /**
     * Get the full URL of the photo (original only, no thumbnails exist yet)
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->place_request_id.'/'.$this->filename);
    }

    /**
     * Get the thumbnail URL (returns original since thumbnails don't exist yet)
     */
    public function getThumbUrlAttribute(): string
    {
        return $this->getUrlAttribute();
    }

    /**
     * Get the medium size URL (returns original since medium size doesn't exist yet)
     */
    public function getMediumUrlAttribute(): string
    {
        return $this->getUrlAttribute();
    }
}
