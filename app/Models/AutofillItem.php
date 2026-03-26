<?php

namespace App\Models;

use App\Enums\AutofillItemStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutofillItem extends Model
{
    /** @use HasFactory<\Database\Factories\AutofillItemFactory> */
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'name',
        'status',
        'step_failed_at',
        'enrichment_data',
        'images_data',
        'place_id',
        'tokens_in',
        'tokens_out',
        'cost',
        'images_count',
        'error_message',
        'error_technical',
        'suggested_tags',
    ];

    protected function casts(): array
    {
        return [
            'status' => AutofillItemStatus::class,
            'enrichment_data' => 'array',
            'images_data' => 'array',
            'cost' => 'decimal:6',
            'tokens_in' => 'integer',
            'tokens_out' => 'integer',
            'images_count' => 'integer',
            'suggested_tags' => 'array',
        ];
    }

    /**
     * @return BelongsTo<AutofillWorkflow, $this>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutofillWorkflow::class, 'workflow_id');
    }

    /**
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * @return HasMany<AutofillStepLog, $this>
     */
    public function stepLogs(): HasMany
    {
        return $this->hasMany(AutofillStepLog::class, 'item_id');
    }

    /**
     * @param  Builder<AutofillItem>  $query
     * @return Builder<AutofillItem>
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', AutofillItemStatus::Saved->value);
    }

    /**
     * @param  Builder<AutofillItem>  $query
     * @return Builder<AutofillItem>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', AutofillItemStatus::Failed->value);
    }

    /**
     * @param  Builder<AutofillItem>  $query
     * @return Builder<AutofillItem>
     */
    public function scopeSelected(Builder $query): Builder
    {
        return $query->where('status', '!=', AutofillItemStatus::Discovered->value)
            ->where('status', '!=', AutofillItemStatus::Skipped->value);
    }
}
