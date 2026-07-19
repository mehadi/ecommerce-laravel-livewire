<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
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
    }
}
