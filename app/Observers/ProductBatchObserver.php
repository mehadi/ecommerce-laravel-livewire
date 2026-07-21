<?php

namespace App\Observers;

use App\Enums\StockMovementType;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;

/**
 * Logs batch-quantity changes as StockMovements directly (mirroring
 * WarehouseStockObserver one layer up), then quietly resyncs the parent
 * WarehouseStock row (the sum of all this product's batches at that
 * warehouse) — the third and final layer of the same denormalization
 * chain (batches -> warehouse stock -> attribute/product stock).
 */
class ProductBatchObserver
{
    public function created(ProductBatch $batch): void
    {
        $this->logMovement($batch, 0);
        $this->resyncWarehouseStock($batch);
    }

    public function updated(ProductBatch $batch): void
    {
        if (! $batch->wasChanged('quantity')) {
            return;
        }

        $this->logMovement($batch, (int) $batch->getOriginal('quantity'));
        $this->resyncWarehouseStock($batch);
    }

    private function logMovement(ProductBatch $batch, int $before): void
    {
        $context = StockMovementContext::current();
        $after = (int) $batch->quantity;
        $reason = $context['reason'] ?? null;

        StockMovement::create([
            'tenant_id' => $batch->tenant_id,
            'product_id' => $batch->product_id,
            'product_attribute_id' => null,
            'warehouse_id' => $batch->warehouse_id,
            'type' => $context['type'] ?? StockMovementType::Adjustment,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'quantity_delta' => $after - $before,
            'reason' => $reason ? "{$reason} (batch {$batch->batch_number})" : "Batch {$batch->batch_number}",
            'changed_by' => $context['changed_by'] ?? Auth::id(),
            'changed_at' => now(),
        ]);
    }

    private function resyncWarehouseStock(ProductBatch $batch): void
    {
        $totalQuantity = ProductBatch::where('warehouse_id', $batch->warehouse_id)
            ->where('product_id', $batch->product_id)
            ->sum('quantity');

        $warehouseStock = WarehouseStock::findOrCreateFor($batch->warehouse_id, $batch->product_id, null);
        $warehouseStock->stock = $totalQuantity;
        $warehouseStock->saveQuietly();

        // saveQuietly() above means WarehouseStockObserver never fires, so
        // the Product::stock resync it would normally do has to happen here.
        $product = $batch->product;

        if ($product && ! $product->hasAttributes()) {
            $product->stock = $product->warehouseStocks()->sum('stock');
            $product->saveQuietly();
        }
    }
}
