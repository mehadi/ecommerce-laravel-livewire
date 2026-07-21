<?php

namespace App\Observers;

use App\Enums\StockMovementType;
use App\Models\ProductAttribute;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;

/**
 * Same logging as ProductObserver, but for variant-level stock. Also
 * resyncs the parent product's denormalized stock/price cache afterwards —
 * previously nothing did this after a checkout decrement, so products.stock
 * could go stale for any product with variants.
 */
class ProductAttributeObserver
{
    /**
     * See ProductObserver::created() — same reasoning, one level down.
     */
    public function created(ProductAttribute $productAttribute): void
    {
        if ($productAttribute->stock <= 0) {
            return;
        }

        WarehouseStock::create([
            'tenant_id' => $productAttribute->tenant_id,
            'warehouse_id' => Warehouse::default()->id,
            'product_id' => $productAttribute->product_id,
            'product_attribute_id' => $productAttribute->id,
            'stock' => $productAttribute->stock,
            'reserved' => 0,
        ]);

        $productAttribute->product?->syncPriceAndStock();
    }

    public function updated(ProductAttribute $productAttribute): void
    {
        if (! $productAttribute->wasChanged('stock')) {
            return;
        }

        $context = StockMovementContext::current();
        $before = (int) $productAttribute->getOriginal('stock');
        $after = (int) $productAttribute->stock;

        StockMovement::create([
            'tenant_id' => $productAttribute->tenant_id,
            'product_id' => $productAttribute->product_id,
            'product_attribute_id' => $productAttribute->id,
            'type' => $context['type'] ?? StockMovementType::Adjustment,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'quantity_delta' => $after - $before,
            'reason' => $context['reason'] ?? null,
            'changed_by' => $context['changed_by'] ?? Auth::id(),
            'changed_at' => now(),
        ]);

        $productAttribute->product?->syncPriceAndStock();
    }
}
