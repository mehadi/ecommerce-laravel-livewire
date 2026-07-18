<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_data',
        'price',
        'compare_at_price',
        'buying_price',
        'sku',
        'stock',
        'weight_kg',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attribute_data' => 'array',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'stock' => 'integer',
            'weight_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAttributeLabelAttribute(): string
    {
        if (empty($this->attribute_data)) {
            return 'Default';
        }

        return collect($this->attribute_data)
            ->map(fn ($value, $key) => "{$key}: {$value}")
            ->join(', ');
    }

    public function getWeightFromAttribute(): ?float
    {
        if (! empty($this->weight_kg)) {
            return (float) $this->weight_kg;
        }

        // Try to extract weight from attribute_data
        if (! empty($this->attribute_data)) {
            foreach ($this->attribute_data as $key => $value) {
                $attribute = Attribute::where('slug', strtolower($key))
                    ->orWhere('name', $key)
                    ->first();

                if ($attribute && $attribute->isWeight()) {
                    return (float) $value;
                }
            }
        }

        return null;
    }
}
