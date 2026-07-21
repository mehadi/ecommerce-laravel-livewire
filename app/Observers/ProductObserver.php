<?php

namespace App\Observers;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;

/**
 * Logs every change to products.stock as a StockMovement, regardless of call
 * site (checkout decrement, admin Inventory adjustment, direct edit via the
 * Product form). The type/reason/changed_by come from whatever
 * StockMovementContext the call site declared; absent that, this is a plain
 * admin edit, so it defaults to an 'adjustment' attributed to the current
 * user.
 */
class ProductObserver
{
    /**
     * A simple (non-attribute) product created with a nonzero initial stock
     * needs a matching WarehouseStock row in the default warehouse — without
     * this, the first warehouse-mediated mutation would resync
     * products.stock down to 0 (the sum of its, currently nonexistent,
     * warehouse rows), silently discarding the initial quantity.
     */
    public function created(Product $product): void
    {
        if ($product->stock <= 0 || $product->hasAttributes()) {
            return;
        }

        WarehouseStock::create([
            'tenant_id' => $product->tenant_id,
            'warehouse_id' => Warehouse::default()->id,
            'product_id' => $product->id,
            'product_attribute_id' => null,
            'stock' => $product->stock,
            'reserved' => 0,
        ]);
    }

    public function updated(Product $product): void
    {
        if (! $product->wasChanged('stock')) {
            return;
        }

        $context = StockMovementContext::current();
        $before = (int) $product->getOriginal('stock');
        $after = (int) $product->stock;

        StockMovement::create([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'product_attribute_id' => null,
            'type' => $context['type'] ?? StockMovementType::Adjustment,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'quantity_delta' => $after - $before,
            'reason' => $context['reason'] ?? null,
            'changed_by' => $context['changed_by'] ?? Auth::id(),
            'changed_at' => now(),
        ]);
    }
}
