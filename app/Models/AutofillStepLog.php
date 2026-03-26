<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutofillStepLog extends Model
{
    /** @use HasFactory<\Database\Factories\AutofillStepLogFactory> */
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'workflow_id',
        'item_id',
        'step',
        'input_data',
        'raw_output',
        'tokens_in',
        'tokens_out',
        'cost',
        'model',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'input_data' => 'array',
            'tokens_in' => 'integer',
            'tokens_out' => 'integer',
            'cost' => 'decimal:6',
            'created_at' => 'datetime',
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
     * @return BelongsTo<AutofillItem, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(AutofillItem::class, 'item_id');
    }
}
