<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'category_id',
        'default_supplier_id',
        'name_en',
        'name_bn',
        'description_en',
        'description_bn',
        'price',
        'compare_at_price',
        'buying_price',
        'sku',
        'barcode',
        'stock',
        'low_stock_threshold',
        'abc_class',
        'tracks_batches',
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
            'low_stock_threshold' => 'integer',
            'tracks_batches' => 'boolean',
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

    public function defaultSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'default_supplier_id');
    }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * FEFO (first-expired-first-out) pick suggestion: the batch at this
     * warehouse that expires soonest (batches with no expiry date sort
     * last, since there's no urgency to deplete them first).
     */
    public function nextBatchToPick(int $warehouseId): ?ProductBatch
    {
        return $this->batches()
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->first();
    }

    /**
     * This product's own direct per-warehouse stock rows — only meaningful for
     * products without attributes (attribute-tracked products have their stock
     * rows scoped to each ProductAttribute instead, see
     * ProductAttribute::warehouseStocks()).
     */
    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class)->whereNull('product_attribute_id');
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

    /**
     * This product's own override if set, otherwise the tenant's configured
     * default (falling back to the historical hardcoded value of 10).
     */
    public function lowStockThreshold(): int
    {
        return $this->low_stock_threshold ?? (int) Setting::get('low_stock_threshold', '10');
    }

    public function isLowStock(): bool
    {
        $stock = $this->getSyncedStock();

        return $stock > 0 && $stock <= $this->lowStockThreshold();
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

    /**
     * Total reserved quantity across every warehouse (and, for attribute-
     * tracked products, every variant) — there is no denormalized `reserved`
     * column, so this reads WarehouseStock rows directly. Relies on the
     * caller having eager-loaded `productAttributes.warehouseStocks` (or
     * `warehouseStocks` for simple products) to avoid N+1 queries.
     */
    public function getSyncedReserved(): int
    {
        if ($this->hasAttributes()) {
            if (! $this->relationLoaded('productAttributes')) {
                return 0;
            }

            return $this->productAttributes->sum(fn (ProductAttribute $attribute) => $attribute->relationLoaded('warehouseStocks')
                ? $attribute->warehouseStocks->sum('reserved')
                : $attribute->warehouseStocks()->sum('reserved'));
        }

        return $this->relationLoaded('warehouseStocks')
            ? $this->warehouseStocks->sum('reserved')
            : $this->warehouseStocks()->sum('reserved');
    }

    public function getSyncedAvailable(): int
    {
        return $this->getSyncedStock() - $this->getSyncedReserved();
    }

    /**
     * Stock held at a single warehouse only (as opposed to getSyncedStock()'s
     * tenant-wide total across every warehouse) — relies on the same eager-loaded
     * `productAttributes.warehouseStocks` / `warehouseStocks` relations.
     */
    public function getStockForWarehouse(int $warehouseId): int
    {
        if ($this->hasAttributes()) {
            if (! $this->relationLoaded('productAttributes')) {
                return 0;
            }

            return $this->productAttributes->sum(fn (ProductAttribute $attribute) => $attribute->relationLoaded('warehouseStocks')
                ? $attribute->warehouseStocks->where('warehouse_id', $warehouseId)->sum('stock')
                : $attribute->warehouseStocks()->where('warehouse_id', $warehouseId)->sum('stock'));
        }

        return $this->relationLoaded('warehouseStocks')
            ? $this->warehouseStocks->where('warehouse_id', $warehouseId)->sum('stock')
            : $this->warehouseStocks()->where('warehouse_id', $warehouseId)->sum('stock');
    }

    /**
     * Reserved quantity at a single warehouse only. See getStockForWarehouse().
     */
    public function getReservedForWarehouse(int $warehouseId): int
    {
        if ($this->hasAttributes()) {
            if (! $this->relationLoaded('productAttributes')) {
                return 0;
            }

            return $this->productAttributes->sum(fn (ProductAttribute $attribute) => $attribute->relationLoaded('warehouseStocks')
                ? $attribute->warehouseStocks->where('warehouse_id', $warehouseId)->sum('reserved')
                : $attribute->warehouseStocks()->where('warehouse_id', $warehouseId)->sum('reserved'));
        }

        return $this->relationLoaded('warehouseStocks')
            ? $this->warehouseStocks->where('warehouse_id', $warehouseId)->sum('reserved')
            : $this->warehouseStocks()->where('warehouse_id', $warehouseId)->sum('reserved');
    }

    /**
     * Available (stock - reserved) at a single warehouse only.
     */
    public function getAvailableForWarehouse(int $warehouseId): int
    {
        return $this->getStockForWarehouse($warehouseId) - $this->getReservedForWarehouse($warehouseId);
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

    /**
     * Clone this product (and its variants) as an inactive draft. SKUs and
     * barcodes are unique per tenant so they are never copied; stock starts
     * at 0 so the copy doesn't fabricate inventory (warehouse allocations
     * belong to the original). Image files are physically copied so the two
     * products never share storage paths (deleting one would otherwise break
     * the other's images).
     */
    public function duplicate(): self
    {
        return DB::transaction(function () {
            $copy = $this->replicate(['sku', 'barcode']);
            $copy->name_en = $this->name_en.' (Copy)';
            $copy->name_bn = $this->name_bn ? $this->name_bn.' (Copy)' : null;
            $copy->is_active = false;
            $copy->is_featured = false;
            $copy->sku = null;
            $copy->barcode = null;
            $copy->stock = 0;
            $copy->order = ((int) static::max('order')) + 1;
            $copy->primary_image = $this->copyStoredImage($this->primary_image);
            $copy->gallery_images = collect($this->gallery_images ?? [])
                ->map(fn ($path) => $this->copyStoredImage($path))
                ->filter()
                ->values()
                ->all();
            $copy->save();

            foreach ($this->productAttributes()->get() as $variant) {
                $copy->productAttributes()->create([
                    'attribute_data' => $variant->attribute_data,
                    'price' => $variant->price,
                    'compare_at_price' => $variant->compare_at_price,
                    'buying_price' => $variant->buying_price,
                    'sku' => null,
                    'barcode' => null,
                    'stock' => 0,
                    'weight_kg' => $variant->weight_kg,
                    'is_active' => $variant->is_active,
                ]);
            }

            return $copy;
        });
    }

    private function copyStoredImage(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $newPath = Tenancy::storagePath('products').'/'.Str::uuid().($extension ? '.'.$extension : '');

        Storage::disk('public')->copy($path, $newPath);

        return $newPath;
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
