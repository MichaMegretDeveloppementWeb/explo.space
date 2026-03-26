<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AiModelPricing extends Model
{
    public $timestamps = false;

    const UPDATED_AT = 'updated_at';

    protected $table = 'ai_model_pricing';

    protected $fillable = [
        'provider',
        'model',
        'price_input_per_million',
        'price_output_per_million',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'price_input_per_million' => 'decimal:4',
            'price_output_per_million' => 'decimal:4',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Calculate cost in USD for given token counts.
     */
    public function calculateCost(int $tokensIn, int $tokensOut): float
    {
        $inputCost = ($tokensIn / 1_000_000) * (float) $this->price_input_per_million;
        $outputCost = ($tokensOut / 1_000_000) * (float) $this->price_output_per_million;

        return round($inputCost + $outputCost, 6);
    }

    /**
     * @param  Builder<AiModelPricing>  $query
     * @return Builder<AiModelPricing>
     */
    public function scopeForModel(Builder $query, string $provider, string $model): Builder
    {
        return $query->where('provider', $provider)->where('model', $model);
    }
}
