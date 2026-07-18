<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariationCombination extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'product_id',
        'variation_data',
        'price',
        'compare_at_price',
        'buying_price',
        'sku',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variation_data' => 'array',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getVariationLabelAttribute(): string
    {
        if (empty($this->variation_data)) {
            return 'Default';
        }

        return collect($this->variation_data)
            ->map(fn ($value, $key) => "{$key}: {$value}")
            ->join(', ');
    }
}
