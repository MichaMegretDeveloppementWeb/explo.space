<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CategoryTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryTranslationFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'slug',
        'description',
        'status',
        'source_hash',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CategoryTranslation $translation) {
            if (empty($translation->slug)) {
                $translation->slug = Str::slug($translation->name);
            }
        });

        static::updating(function (CategoryTranslation $translation) {
            if ($translation->isDirty('name') && empty($translation->getOriginal('slug'))) {
                $translation->slug = Str::slug($translation->name);
            }
        });
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for published translations
     *
     * @param  Builder<CategoryTranslation>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /**
     * Scope for specific locale
     *
     * @param  Builder<CategoryTranslation>  $query
     */
    public function scopeForLocale(Builder $query, string $locale): void
    {
        $query->where('locale', $locale);
    }

    /**
     * Check if translation is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if translation is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
