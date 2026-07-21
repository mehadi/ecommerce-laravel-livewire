<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The per-warehouse stock ledger — the source of truth for physical stock
 * quantities going forward. `Product::stock`/`ProductAttribute::stock` remain
 * as denormalized totals (the sum of a product/attribute's rows here across
 * all warehouses), kept in sync by WarehouseStockObserver, exactly mirroring
 * how Product::stock was already kept in sync from ProductAttribute rows.
 */
class WarehouseStock extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'product_attribute_id',
        'stock',
        'reserved',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'reserved' => 'integer',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function available(): int
    {
        return $this->stock - $this->reserved;
    }

    /**
     * Finds (or atomically creates) the stock row for a given warehouse +
     * sellable unit. Uses createOrFirst() rather than firstOrCreate() so a
     * race between two concurrent requests for a never-before-stocked
     * product/variant can't insert duplicate rows — the partial unique
     * indexes on this table make the losing insert retry into a plain read.
     */
    public static function findOrCreateFor(int $warehouseId, int $productId, ?int $productAttributeId): self
    {
        return static::createOrFirst([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'product_attribute_id' => $productAttributeId,
        ]);
    }
}
