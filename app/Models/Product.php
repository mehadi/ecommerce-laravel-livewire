<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'category_id',
        'name_en',
        'name_bn',
        'description_en',
        'description_bn',
        'ingredients_en',
        'ingredients_bn',
        'benefits_en',
        'benefits_bn',
        'price',
        'compare_at_price',
        'buying_price',
        'sku',
        'stock',
        'weight_kg',
        'primary_image',
        'gallery_images',
        'is_active',
        'is_featured',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'stock' => 'integer',
            'weight_kg' => 'decimal:2',
            'gallery_images' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function hasAttributes(): bool
    {
        if ($this->relationLoaded('productAttributes')) {
            return $this->productAttributes->isNotEmpty();
        }

        return $this->productAttributes()->exists();
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->name_bn ? $this->name_bn : $this->name_en;
    }

    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'bn' && $this->description_bn ? $this->description_bn : $this->description_en;
    }

    public function getIngredientsAttribute(): ?string
    {
        return app()->getLocale() === 'bn' && $this->ingredients_bn ? $this->ingredients_bn : $this->ingredients_en;
    }

    public function getBenefitsAttribute(): ?string
    {
        return app()->getLocale() === 'bn' && $this->benefits_bn ? $this->benefits_bn : $this->benefits_en;
    }

    public function hasDiscount(): bool
    {
        $comparePrice = $this->getSyncedCompareAtPrice();
        $price = $this->getSyncedPrice();

        return $comparePrice && $comparePrice > $price;
    }

    public function discountPercentage(): float
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        $comparePrice = $this->getSyncedCompareAtPrice();
        $price = $this->getSyncedPrice();

        return round((($comparePrice - $price) / $comparePrice) * 100, 2);
    }

    public function isInStock(): bool
    {
        if ($this->hasAttributes()) {
            return $this->getSyncedStock() > 0;
        }

        return $this->stock > 0;
    }

    public function getSyncedPrice(): float
    {
        if ($this->hasAttributes() && $this->relationLoaded('productAttributes')) {
            $prices = $this->productAttributes->where('is_active', true)->pluck('price')->filter();
            if ($prices->isNotEmpty()) {
                return (float) $prices->min();
            }
        }

        return (float) $this->price;
    }

    public function getMaxPrice(): float
    {
        if ($this->hasAttributes() && $this->relationLoaded('productAttributes')) {
            $prices = $this->productAttributes->where('is_active', true)->pluck('price')->filter();
            if ($prices->isNotEmpty()) {
                return (float) $prices->max();
            }
        }

        return (float) $this->price;
    }

    public function getSyncedStock(): int
    {
        if ($this->hasAttributes() && $this->relationLoaded('productAttributes')) {
            return $this->productAttributes->sum('stock');
        }

        return (int) $this->stock;
    }

    public function getSyncedCompareAtPrice(): ?float
    {
        if ($this->hasAttributes() && $this->relationLoaded('productAttributes')) {
            $comparePrices = $this->productAttributes->where('is_active', true)
                ->pluck('compare_at_price')
                ->filter()
                ->filter(fn ($price) => $price > 0);

            if ($comparePrices->isNotEmpty()) {
                return (float) $comparePrices->max();
            }

            return null;
        }

        return $this->compare_at_price ? (float) $this->compare_at_price : null;
    }

    public function syncPriceAndStock(): void
    {
        if ($this->hasAttributes()) {
            $this->load('productAttributes');

            // Sync price to minimum attribute price
            $minPrice = $this->getSyncedPrice();
            $this->price = $minPrice;

            // Sync stock to sum of all attribute stocks
            $totalStock = $this->getSyncedStock();
            $this->stock = $totalStock;

            // Sync compare_at_price to maximum attribute compare_at_price
            $maxComparePrice = $this->getSyncedCompareAtPrice();
            $this->compare_at_price = $maxComparePrice;

            $this->saveQuietly();
        }
    }

    public function profit(): float
    {
        if (! $this->buying_price || $this->buying_price <= 0) {
            return 0;
        }

        return round($this->price - $this->buying_price, 2);
    }

    public function profitPercentage(): float
    {
        if (! $this->buying_price || $this->buying_price <= 0) {
            return 0;
        }

        return round((($this->price - $this->buying_price) / $this->buying_price) * 100, 2);
    }
}
