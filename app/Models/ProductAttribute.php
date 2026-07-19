<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Tenancy;
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
                $attribute = static::weightLookupAttribute($key);

                if ($attribute && $attribute->isWeight()) {
                    return (float) $value;
                }
            }
        }

        return null;
    }

    /**
     * @var array<string, Attribute|false>
     */
    private static array $weightLookupCache = [];

    /**
     * Memoized per-tenant lookup so resolving weight for N cart/order lines
     * doesn't re-run the same Attribute query for every repeated key.
     */
    private static function weightLookupAttribute(string $key): ?Attribute
    {
        $cacheKey = Tenancy::id().'::'.strtolower($key);

        if (! array_key_exists($cacheKey, static::$weightLookupCache)) {
            static::$weightLookupCache[$cacheKey] = Attribute::where('slug', strtolower($key))
                ->orWhere('name', $key)
                ->first() ?? false;
        }

        return static::$weightLookupCache[$cacheKey] ?: null;
    }
}
