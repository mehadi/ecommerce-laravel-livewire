<?php

namespace App\Livewire\Admin\Pos;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRefund;
use App\Models\PosShift;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * A lean, standalone stats page rather than a subclass of
 * DashboardPageComponent — that base class's HasDashboardAnalytics trait is a
 * large, tightly-coupled engine built around date-range/status filters across
 * the whole store, and retrofitting a `channel` dimension through it is a
 * bigger and riskier change than a POS-specific reporting page needs for v1.
 * This queries the same underlying data (Order/OrderItem/OrderRefund/PosShift)
 * directly, filtered to channel = 'pos'.
 */
class Dashboard extends Component
{
    public function mount(): void
    {
        Gate::authorize('view pos reports');
    }

    public function render()
    {
        $todaySales = Order::where('channel', 'pos')->whereDate('created_at', today());

        $stats = [
            'today_sales_total' => (clone $todaySales)->sum('total'),
            'today_sales_count' => (clone $todaySales)->count(),
            'active_shifts' => PosShift::where('status', 'open')->count(),
            'cash_in_drawers' => PosShift::where('status', 'open')->get()->sum(function (PosShift $shift) {
                $movements = $shift->cashMovements()->get();

                return $shift->opening_cash
                    + $movements->whereIn('type', ['cash_in', 'sale_cash'])->sum('amount')
                    - $movements->whereIn('type', ['cash_out', 'refund_cash'])->sum('amount');
            }),
            'today_refunds_total' => OrderRefund::whereDate('created_at', today())
                ->whereHas('order', fn ($q) => $q->where('channel', 'pos'))
                ->sum('amount'),
        ];

        $topProducts = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('channel', 'pos')->whereDate('created_at', '>=', today()->subDays(30)))
            ->selectRaw('product_name, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $recentSales = Order::where('channel', 'pos')
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.admin.pos.dashboard', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
        ])->layout('components.layouts.app', [
            'title' => __('POS Dashboard'),
        ]);
    }
}
