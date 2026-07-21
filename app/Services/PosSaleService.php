<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The single place that turns a cart (POS terminal, or the admin manual-order
 * screen) into a real Order: locks and decrements WarehouseStock exactly like
 * the storefront checkout does (HasShoppingCart::placeOrder(), which this
 * service does not touch — its validation shape is different and out of
 * scope here), then creates the Order/OrderItem/OrderPayment rows.
 *
 * A line's `product_attribute_id` lets a caller with a real variant picker
 * (the POS terminal) deduct at the exact variant level. The admin
 * manual-order screen has no variant picker, so it still can't safely resolve
 * *which* variant to touch — rather than guessing (or, worse, deducting
 * against the wrong stock row), it marks such lines `skip_stock: true`,
 * which records the sale without touching inventory, exactly matching that
 * screen's previous behavior for attribute-having products.
 */
class PosSaleService
{
    /**
     * @param  array{
     *     order: array<string, mixed>,
     *     lines: array<int, array{
     *         product_id: int,
     *         product_attribute_id: int|null,
     *         product_name: string,
     *         attribute_data: array|null,
     *         quantity: int,
     *         unit_price: float,
     *         skip_stock?: bool,
     *     }>,
     *     payments: array<int, array{
     *         method: string,
     *         amount: float,
     *         reference?: string|null,
     *         change_given?: float|null,
     *     }>,
     *     warehouse: Warehouse,
     * }  $data
     *
     * @throws RuntimeException when any stock-tracked line is out of stock once locked
     */
    public function checkout(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $warehouse = $data['warehouse'];

            // Lock and stock-check every trackable line up front, before creating
            // anything, so a sale never partially commits when one line is oversold.
            $plannedLines = [];

            foreach ($data['lines'] as $line) {
                if (! empty($line['skip_stock'])) {
                    $plannedLines[] = ['line' => $line, 'stock' => null];

                    continue;
                }

                $stock = WarehouseStock::findOrCreateFor(
                    $warehouse->id,
                    $line['product_id'],
                    $line['product_attribute_id'] ?? null
                );

                $locked = WarehouseStock::whereKey($stock->id)->lockForUpdate()->first();

                if ($locked->available() < $line['quantity']) {
                    throw new RuntimeException("Insufficient stock for \"{$line['product_name']}\".");
                }

                $plannedLines[] = ['line' => $line, 'stock' => $locked];
            }

            // customer_email/customer_phone/shipping_address are NOT NULL columns
            // (required by the storefront/admin-manual paths, which always collect
            // them) — an in-person sale often has none of these, so default to
            // blank rather than loosening that constraint for every order type.
            // 'status' needs an explicit default here (rather than relying on the
            // orders.status column's DB-level default) because OrderObserver::created()
            // reads $order->status off the in-memory model synchronously right after
            // insert, before any DB-applied default would be visible without a refresh.
            $order = Order::create(array_merge([
                'customer_name' => 'Walk-in Customer',
                'customer_email' => '',
                'customer_phone' => '',
                'shipping_address' => '',
                'status' => 'pending',
                'subtotal' => 0,
                'discount' => 0,
                'shipping_cost' => 0,
                'total' => 0,
                'payment_method' => $data['payments'][0]['method'] ?? 'cash',
            ], $data['order']));

            foreach ($plannedLines as $planned) {
                $line = $planned['line'];
                $stock = $planned['stock'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product_id'],
                    'product_attribute_id' => $line['product_attribute_id'] ?? null,
                    'warehouse_id' => $stock ? $warehouse->id : null,
                    'product_name' => $line['product_name'],
                    'attribute_data' => $line['attribute_data'] ?? null,
                    'price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['unit_price'] * $line['quantity'],
                    'stock_deducted' => (bool) $stock,
                ]);

                if ($stock) {
                    StockMovementContext::run([
                        'type' => StockMovementType::Sale,
                        'reason' => "Order #{$order->order_number}",
                        'changed_by' => Auth::id(),
                    ], fn () => $stock->decrement('stock', $line['quantity']));
                }
            }

            foreach ($data['payments'] as $payment) {
                OrderPayment::create(array_merge([
                    'order_id' => $order->id,
                    'created_by' => Auth::id(),
                ], $payment));
            }

            return $order->fresh(['items', 'payments']);
        });
    }
}
