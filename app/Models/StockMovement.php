<?php

namespace App\Models;

use App\Enums\StockMovementType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per stock change on a product or product attribute (variant),
 * written by ProductObserver/ProductAttributeObserver whenever their
 * `stock` column changes. The `type`/`reason`/`changed_by` come from
 * whatever StockMovementContext the call site declared (sale at checkout,
 * return on order cancellation, or adjustment from the admin Inventory
 * screen) — see App\Support\StockMovementContext.
 */
class StockMovement extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'product_attribute_id',
        'warehouse_id',
        'type',
        'quantity_before',
        'quantity_after',
        'quantity_delta',
        'reason',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity_before' => 'integer',
            'quantity_after' => 'integer',
            'quantity_delta' => 'integer',
            'changed_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
