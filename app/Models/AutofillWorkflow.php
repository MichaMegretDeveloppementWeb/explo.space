<?php

namespace App\Models;

use App\Enums\AutofillWorkflowState;
use App\Enums\AutofillWorkflowStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property AutofillWorkflowStatus $status
 * @property AutofillWorkflowState $state
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 */
class AutofillWorkflow extends Model
{
    /** @use HasFactory<\Database\Factories\AutofillWorkflowFactory> */
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'query',
        'provider',
        'requested_quantity',
        'status',
        'state',
        'total_tokens_in',
        'total_tokens_out',
        'total_cost',
        'error_message',
        'error_technical',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AutofillWorkflowStatus::class,
            'state' => AutofillWorkflowState::class,
            'total_cost' => 'decimal:6',
            'requested_quantity' => 'integer',
            'total_tokens_in' => 'integer',
            'total_tokens_out' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @return HasMany<AutofillItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(AutofillItem::class, 'workflow_id');
    }

    /**
     * @return HasMany<AutofillMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AutofillMessage::class, 'workflow_id');
    }

    /**
     * @return HasMany<AutofillStepLog, $this>
     */
    public function stepLogs(): HasMany
    {
        return $this->hasMany(AutofillStepLog::class, 'workflow_id');
    }

    /**
     * Workflows that are running (jobs executing, no errors).
     *
     * @param  Builder<AutofillWorkflow>  $query
     * @return Builder<AutofillWorkflow>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('state', AutofillWorkflowState::Active);
    }

    /**
     * Workflows that the admin is currently working on (active or paused).
     * These occupy a slot — the admin must deal with them before starting a new one.
     *
     * @param  Builder<AutofillWorkflow>  $query
     * @return Builder<AutofillWorkflow>
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereIn('state', [
            AutofillWorkflowState::Active,
            AutofillWorkflowState::Paused,
        ]);
    }

    /**
     * @param  Builder<AutofillWorkflow>  $query
     * @return Builder<AutofillWorkflow>
     */
    public function scopeForAdmin(Builder $query, int $adminId): Builder
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Is this workflow currently running (jobs executing)?
     */
    public function isActive(): bool
    {
        return $this->state === AutofillWorkflowState::Active;
    }

    /**
     * Is this workflow paused due to an error?
     */
    public function isPaused(): bool
    {
        return $this->state === AutofillWorkflowState::Paused;
    }

    /**
     * Is this workflow still the admin's "current" workflow (active or paused)?
     */
    public function isCurrent(): bool
    {
        return $this->state->isCurrent();
    }

    /**
     * Is this workflow definitively done (completed or abandoned)?
     */
    public function isDismissed(): bool
    {
        return $this->state->isDismissed();
    }

    /**
     * Duration in seconds, or null if not started/completed.
     */
    public function totalDuration(): ?int
    {
        if (! $this->started_at) {
            return null;
        }

        $end = $this->completed_at ?? now();

        return (int) $this->started_at->diffInSeconds($end);
    }
}
