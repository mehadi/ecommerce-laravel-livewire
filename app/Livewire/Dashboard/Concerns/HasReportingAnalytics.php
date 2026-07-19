<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

/**
 * Data engine for the reports that filled genuine gaps in the existing
 * dashboard: product profitability, inventory valuation, coupon performance,
 * and fulfillment/SLA. Profitability/inventory/fulfillment figures reuse
 * $this->filteredOrderItems / $this->filteredOrders (from HasDashboardAnalytics)
 * so they respect the same date-range/status filters as the rest of the
 * dashboard; coupon performance is a point-in-time snapshot since coupon
 * usage isn't attributed to individual orders in this schema.
 */
trait HasReportingAnalytics
{
    /**
     * Hours a currently-open order can sit in one status before it's flagged
     * as an SLA breach on the fulfillment report. Fixed for now rather than a
     * per-tenant Setting — revisit if tenants need different thresholds.
     */
    public const SLA_BREACH_HOURS = 48;

    /**
     * Trait constants can't be referenced directly from Blade (only through
     * a concrete class), so this exposes it for the view.
     */
    public function slaBreachHours(): int
    {
        return self::SLA_BREACH_HOURS;
    }
    #[Computed]
    public function productProfitabilityData(): Collection
    {
        return $this->filteredOrderItems
            ->filter(fn (OrderItem $item) => $item->product !== null)
            ->groupBy('product_id')
            ->map(function (Collection $group) {
                /** @var OrderItem $first */
                $first = $group->first();
                $product = $first->product;
                $buyingPrice = (float) ($product->buying_price ?? 0);
                $unitsSold = (int) $group->sum('quantity');
                $revenue = (float) $group->sum(fn (OrderItem $item) => (float) $item->subtotal);
                $cost = $buyingPrice * $unitsSold;
                $profit = $revenue - $cost;

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name_en,
                    'category' => $product->category?->name_en ?? __('Uncategorized'),
                    'units_sold' => $unitsSold,
                    'revenue' => round($revenue, 2),
                    'cost' => round($cost, 2),
                    'profit' => round($profit, 2),
                    'margin_percent' => $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0.0,
                    'has_cost_data' => $buyingPrice > 0,
                ];
            })
            ->values();
    }

    #[Computed]
    public function profitabilitySummary(): array
    {
        $rows = $this->productProfitabilityData;
        $revenue = (float) $rows->sum('revenue');
        $profit = (float) $rows->sum('profit');

        return [
            'total_revenue' => round($revenue, 2),
            'total_profit' => round($profit, 2),
            'margin_percent' => $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0.0,
            'missing_cost_count' => $rows->where('has_cost_data', false)->count(),
        ];
    }

    /**
     * Point-in-time stock valuation (not date-filtered — a snapshot of stock
     * right now), joined with units-sold-in-the-selected-period for the
     * dead-stock/velocity flag only.
     */
    #[Computed]
    public function inventoryValuationData(): Collection
    {
        $unitsSoldByProduct = $this->filteredOrderItems
            ->groupBy('product_id')
            ->map(fn (Collection $group) => (int) $group->sum('quantity'));

        return Product::with(['productAttributes', 'category'])
            ->where('is_active', true)
            ->get()
            ->map(function (Product $product) use ($unitsSoldByProduct) {
                if ($product->hasAttributes()) {
                    $variants = $product->productAttributes->where('is_active', true);
                    $stock = (int) $variants->sum('stock');
                    $valueAtCost = (float) $variants->sum(fn ($variant) => (float) $variant->stock * (float) ($variant->buying_price ?? 0));
                    $valueAtRetail = (float) $variants->sum(fn ($variant) => (float) $variant->stock * (float) $variant->price);
                    $hasCostData = $variants->contains(fn ($variant) => (float) ($variant->buying_price ?? 0) > 0);
                } else {
                    $stock = (int) $product->stock;
                    $valueAtCost = $stock * (float) ($product->buying_price ?? 0);
                    $valueAtRetail = $stock * (float) $product->price;
                    $hasCostData = (float) ($product->buying_price ?? 0) > 0;
                }

                $unitsSold = (int) ($unitsSoldByProduct[$product->id] ?? 0);

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name_en,
                    'category' => $product->category?->name_en ?? __('Uncategorized'),
                    'stock' => $stock,
                    'value_at_cost' => round($valueAtCost, 2),
                    'value_at_retail' => round($valueAtRetail, 2),
                    'units_sold_in_period' => $unitsSold,
                    'is_low_stock' => $stock > 0 && $stock <= 10,
                    'is_out_of_stock' => $stock <= 0,
                    'is_dead_stock' => $stock > 0 && $unitsSold === 0,
                    'has_cost_data' => $hasCostData,
                ];
            })
            ->values();
    }

    #[Computed]
    public function inventorySummary(): array
    {
        $rows = $this->inventoryValuationData;

        return [
            'total_value_at_cost' => round((float) $rows->sum('value_at_cost'), 2),
            'total_value_at_retail' => round((float) $rows->sum('value_at_retail'), 2),
            'dead_stock_count' => $rows->where('is_dead_stock', true)->count(),
            'low_stock_count' => $rows->where('is_low_stock', true)->count(),
        ];
    }

    /**
     * Aggregate-only: this schema has no per-order coupon redemption record
     * (only a running used_count on the coupon itself), so this cannot be
     * attributed to specific orders/revenue — see usage note in the view.
     */
    #[Computed]
    public function couponPerformanceData(): Collection
    {
        return Coupon::query()
            ->orderByDesc('used_count')
            ->get()
            ->map(function (Coupon $coupon) {
                $usagePercent = $coupon->usage_limit
                    ? round(($coupon->used_count / max($coupon->usage_limit, 1)) * 100, 1)
                    : null;

                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'used_count' => $coupon->used_count,
                    'usage_limit' => $coupon->usage_limit,
                    'usage_percent' => $usagePercent,
                    'is_valid' => $coupon->isValid(),
                    'expires_at' => $coupon->expires_at,
                ];
            });
    }

    #[Computed]
    public function couponPerformanceSummary(): array
    {
        $rows = $this->couponPerformanceData;

        return [
            'total_redemptions' => $rows->sum('used_count'),
            'active_count' => $rows->where('is_valid', true)->count(),
        ];
    }

    /**
     * Currently-open orders (within the selected date range), sorted by how
     * long they've sat in their current status — the "chase these down"
     * list. Falls back to order created_at when an order has no history row
     * yet (shouldn't happen post-migration, but defensive).
     */
    #[Computed]
    public function fulfillmentAttentionData(): Collection
    {
        $openOrders = $this->filteredOrders
            ->reject(fn (Order $order) => in_array($order->status, ['delivered', 'cancelled'], true));

        if ($openOrders->isEmpty()) {
            return collect();
        }

        $latestChangeByOrder = OrderStatusHistory::query()
            ->whereIn('order_id', $openOrders->pluck('id'))
            ->orderByDesc('changed_at')
            ->get()
            ->groupBy('order_id')
            ->map(fn (Collection $rows) => $rows->first()->changed_at);

        return $openOrders
            ->map(function (Order $order) use ($latestChangeByOrder) {
                $lastChangedAt = $latestChangeByOrder->get($order->id) ?? $order->created_at;
                $hoursInStatus = $lastChangedAt ? $lastChangedAt->diffInHours(now()) : 0;

                return [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'status' => $order->status,
                    'hours_in_status' => $hoursInStatus,
                    'is_sla_breach' => $hoursInStatus > self::SLA_BREACH_HOURS,
                ];
            })
            ->sortByDesc('hours_in_status')
            ->values();
    }

    /**
     * Average time from first tracked status to delivered, counted only for
     * orders with more than one history row (i.e. a real observed transition
     * since order_status_histories started tracking) — a single row means
     * "no transition observed yet," not a same-day fulfillment.
     */
    #[Computed]
    public function fulfillmentSummary(): array
    {
        $deliveredOrderIds = $this->filteredOrders
            ->filter(fn (Order $order) => $order->status === 'delivered')
            ->pluck('id');

        $fulfillmentHours = OrderStatusHistory::query()
            ->whereIn('order_id', $deliveredOrderIds)
            ->orderBy('changed_at')
            ->get()
            ->groupBy('order_id')
            ->filter(fn (Collection $rows) => $rows->count() > 1)
            ->map(function (Collection $rows) {
                $first = $rows->first();
                $delivered = $rows->firstWhere('status', 'delivered') ?? $rows->last();

                return $first->changed_at->diffInHours($delivered->changed_at, true);
            });

        return [
            'avg_fulfillment_hours' => $fulfillmentHours->isNotEmpty() ? round($fulfillmentHours->avg(), 1) : null,
            'tracked_delivery_count' => $fulfillmentHours->count(),
            'open_orders_count' => $this->fulfillmentAttentionData->count(),
            'sla_breach_count' => $this->fulfillmentAttentionData->where('is_sla_breach', true)->count(),
        ];
    }
}
