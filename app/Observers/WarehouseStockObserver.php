<?php

namespace App\Observers;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;

/**
 * Logs every change to a warehouse's `stock`/`reserved` counters as a
 * StockMovement, then resyncs the parent ProductAttribute/Product's
 * denormalized `stock` column (the sum of its rows across all warehouses),
 * via saveQuietly() so this resync never re-triggers ProductObserver/
 * ProductAttributeObserver's own logging — the same no-double-log pattern
 * Product::syncPriceAndStock() already established one layer down.
 */
class WarehouseStockObserver
{
    public function updated(WarehouseStock $warehouseStock): void
    {
        if ($warehouseStock->wasChanged('stock')) {
            $this->logMovement($warehouseStock, 'stock', StockMovementType::Adjustment);
            $this->resyncParent($warehouseStock);
        }

        if ($warehouseStock->wasChanged('reserved')) {
            $this->logMovement($warehouseStock, 'reserved', StockMovementType::Reservation);
        }
    }

    private function logMovement(WarehouseStock $warehouseStock, string $column, StockMovementType $default): void
    {
        $context = StockMovementContext::current();
        $before = (int) $warehouseStock->getOriginal($column);
        $after = (int) $warehouseStock->{$column};

        StockMovement::create([
            'tenant_id' => $warehouseStock->tenant_id,
            'product_id' => $warehouseStock->product_id,
            'product_attribute_id' => $warehouseStock->product_attribute_id,
            'warehouse_id' => $warehouseStock->warehouse_id,
            'type' => $context['type'] ?? $default,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'quantity_delta' => $after - $before,
            'reason' => $context['reason'] ?? null,
            'changed_by' => $context['changed_by'] ?? Auth::id(),
            'changed_at' => now(),
        ]);
    }

    private function resyncParent(WarehouseStock $warehouseStock): void
    {
        if ($warehouseStock->product_attribute_id) {
            $attribute = $warehouseStock->productAttribute;

            if ($attribute) {
                $attribute->stock = $attribute->warehouseStocks()->sum('stock');
                $attribute->saveQuietly();
                $attribute->product?->syncPriceAndStock();
            }

            return;
        }

        $product = $warehouseStock->product;

        if ($product && ! $product->hasAttributes()) {
            $product->stock = $product->warehouseStocks()->sum('stock');
            $product->saveQuietly();
        }
    }
}
