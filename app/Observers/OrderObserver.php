<?php

namespace App\Observers;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;

/**
 * Logs every order status transition to order_status_histories, going
 * forward only — this is the data source for the Fulfillment/SLA report.
 * Both order creation (initial status) and any later status change are
 * captured, regardless of call site (admin single/bulk update, customer
 * checkout), since this hooks Eloquent's own created/updated events rather
 * than any specific controller/component action.
 */
class OrderObserver
{
    public function created(Order $order): void
    {
        OrderStatusHistory::create([
            'tenant_id' => $order->tenant_id,
            'order_id' => $order->id,
            'status' => $order->status,
            'changed_by' => Auth::id(),
            'changed_at' => $order->created_at ?? now(),
        ]);
    }

    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        OrderStatusHistory::create([
            'tenant_id' => $order->tenant_id,
            'order_id' => $order->id,
            'status' => $order->status,
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);

        if ($order->status === 'cancelled' && $order->getOriginal('status') !== 'cancelled') {
            $this->restockCancelledOrder($order);
        }
    }

    /**
     * Restores stock for whichever items on this order actually had stock
     * deducted at creation (see OrderItem::stock_deducted). One-way only —
     * there's no real "un-cancel" workflow to mirror (canBeCancelled() only
     * allows pending/confirmed -> cancelled in the first place).
     */
    private function restockCancelledOrder(Order $order): void
    {
        $order->loadMissing('orderItems.product', 'orderItems.productAttribute', 'orderItems.warehouse');

        foreach ($order->orderItems as $item) {
            if (! $item->stock_deducted) {
                continue;
            }

            if (! $item->product) {
                continue;
            }

            // Pre-migration order items have no recorded warehouse_id; fall back
            // to the tenant's default warehouse rather than skip the restock.
            $warehouse = $item->warehouse ?? Warehouse::default();
            $warehouseStock = WarehouseStock::findOrCreateFor($warehouse->id, $item->product_id, $item->product_attribute_id);

            StockMovementContext::run([
                'type' => StockMovementType::Return,
                'reason' => "Order #{$order->order_number} cancelled",
                'changed_by' => Auth::id(),
            ], function () use ($warehouseStock, $item) {
                $warehouseStock->increment('stock', $item->quantity);
            });

            $item->update(['stock_deducted' => false]);
        }
    }
}
