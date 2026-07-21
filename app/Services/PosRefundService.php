<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRefund;
use App\Models\PosCashMovement;
use App\Models\PosShift;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Reverses one line (or, with order_item null, a whole-sale amount) of a
 * completed order: records the OrderRefund, restocks inventory through the
 * same StockMovementContext pattern OrderObserver::restockCancelledOrder()
 * uses for cancellations, and reverses money via store credit or a
 * cash-drawer ledger entry — there is no payment gateway to call back.
 */
class PosRefundService
{
    /**
     * @param  array{
     *     order: Order,
     *     order_item: OrderItem|null,
     *     quantity: int|null,
     *     amount: float,
     *     method: string,
     *     reason: string|null,
     *     refunded_by?: int|null,
     *     current_shift?: PosShift|null,
     * }  $data
     */
    public function refund(array $data): OrderRefund
    {
        return DB::transaction(function () use ($data) {
            $order = $data['order'];
            $item = $data['order_item'] ?? null;

            $refund = OrderRefund::create([
                'order_id' => $order->id,
                'order_item_id' => $item?->id,
                'quantity' => $data['quantity'] ?? null,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reason' => $data['reason'] ?? null,
                'refunded_by' => $data['refunded_by'] ?? Auth::id(),
            ]);

            if ($item && $item->stock_deducted && ! empty($data['quantity'])) {
                $warehouse = $item->warehouse ?? Warehouse::default();
                $warehouseStock = WarehouseStock::findOrCreateFor($warehouse->id, $item->product_id, $item->product_attribute_id);

                StockMovementContext::run([
                    'type' => StockMovementType::Return,
                    'reason' => "Refund on Order #{$order->order_number}",
                    'changed_by' => Auth::id(),
                ], fn () => $warehouseStock->increment('stock', $data['quantity']));
            }

            if ($data['method'] === 'store_credit' && $order->customer) {
                $order->customer->increment('store_credit_balance', $data['amount']);
            }

            if ($data['method'] === 'cash' && ! empty($data['current_shift'])) {
                PosCashMovement::create([
                    'shift_id' => $data['current_shift']->id,
                    'type' => 'refund_cash',
                    'amount' => $data['amount'],
                    'reason' => "Refund on Order #{$order->order_number}",
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);
            }

            return $refund;
        });
    }
}
