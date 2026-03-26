<?php

namespace App\Models;

use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutofillMessage extends Model
{
    /** @use HasFactory<\Database\Factories\AutofillMessageFactory> */
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'workflow_id',
        'type',
        'role',
        'payload',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AutofillMessageType::class,
            'role' => AutofillMessageRole::class,
            'payload' => 'array',
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
}
