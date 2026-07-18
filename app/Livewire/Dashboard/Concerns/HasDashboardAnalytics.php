<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

/**
 * The single shared data engine for every dashboard sub-page: the filtered
 * orders/order-items collections and every metric, chart-data, and insight
 * #[Computed] property, extracted verbatim from the original monolith
 * Dashboard component. Calculation logic is untouched.
 */
trait HasDashboardAnalytics
{
    protected array $statusDefinitions = [
        'pending' => ['label' => 'Pending', 'tone' => 'amber', 'hex' => '#f59e0b'],
        'confirmed' => ['label' => 'Confirmed', 'tone' => 'blue', 'hex' => '#3b82f6'],
        'processing' => ['label' => 'Processing', 'tone' => 'purple', 'hex' => '#a855f7'],
        'shipped' => ['label' => 'Shipped', 'tone' => 'indigo', 'hex' => '#6366f1'],
        'delivered' => ['label' => 'Delivered', 'tone' => 'emerald', 'hex' => '#10b981'],
        'cancelled' => ['label' => 'Cancelled', 'tone' => 'rose', 'hex' => '#ef4444'],
    ];

    #[Computed]
    public function filteredOrders(): Collection
    {
        return $this->cloneOrdersQuery()
            ->select([
                'id',
                'total',
                'discount',
                'advance_payment',
                'status',
                'customer_email',
                'customer_name',
                'order_number',
                'payment_method',
                'created_at',
                'shipping_city',
            ])
            ->with([
                'items' => function ($query) {
                    $query->select(['id', 'order_id', 'product_id', 'product_name', 'quantity', 'subtotal'])
                        ->with([
                            'product:id,name_en,category_id',
                            'product.category:id,name_en',
                        ]);
                },
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function filteredOrderItems(): Collection
    {
        return $this->filteredOrders
            ->flatMap(fn (Order $order) => $order->items)
            ->values();
    }

    #[Computed]
    public function totalRevenue(): float
    {
        return (float) $this->filteredOrders->sum(fn (Order $order) => (float) $order->total);
    }

    #[Computed]
    public function totalOrders(): int
    {
        return $this->filteredOrders->count();
    }

    #[Computed]
    public function averageOrderValue(): float
    {
        $count = $this->totalOrders;

        return $count > 0 ? $this->totalRevenue / $count : 0;
    }

    #[Computed]
    public function totalProducts(): int
    {
        return Product::where('is_active', true)->count();
    }

    #[Computed]
    public function revenueChartData(): array
    {
        $orders = $this->filteredOrders;

        $start = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDays(30);
        $end = $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();
        $daysDiff = $start->diffInDays($end);

        $labels = [];
        $data = [];

        if ($daysDiff <= 31) {
            // Daily data
            $current = $start->copy();
            while ($current->lte($end)) {
                $labels[] = $current->format('M d');
                $dayRevenue = $orders
                    ->filter(fn (Order $order) => Carbon::parse($order->created_at)->isSameDay($current))
                    ->sum(fn (Order $order) => (float) $order->total);
                $data[] = $dayRevenue;
                $current->addDay();
            }
        } elseif ($daysDiff <= 93) {
            // Weekly data
            $current = $start->copy()->startOfWeek();
            while ($current->lte($end)) {
                $weekEnd = $current->copy()->endOfWeek();
                if ($weekEnd->gt($end)) {
                    $weekEnd = $end->copy();
                }
                $labels[] = $current->format('M d').' - '.$weekEnd->format('M d');
                $weekRevenue = $orders
                    ->filter(function (Order $order) use ($current, $weekEnd) {
                        $orderDate = Carbon::parse($order->created_at);

                        return $orderDate->gte($current) && $orderDate->lte($weekEnd);
                    })
                    ->sum(fn (Order $order) => (float) $order->total);
                $data[] = $weekRevenue;
                $current->addWeek();
            }
        } else {
            // Monthly data
            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $monthEnd = $current->copy()->endOfMonth();
                if ($monthEnd->gt($end)) {
                    $monthEnd = $end->copy();
                }
                $labels[] = $current->format('M Y');
                $monthRevenue = $orders
                    ->filter(function (Order $order) use ($current, $monthEnd) {
                        $orderDate = Carbon::parse($order->created_at);

                        return $orderDate->gte($current) && $orderDate->lte($monthEnd);
                    })
                    ->sum(fn (Order $order) => (float) $order->total);
                $data[] = $monthRevenue;
                $current->addMonth();
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    #[Computed]
    public function ordersChartData(): array
    {
        $orders = $this->filteredOrders;

        $start = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDays(30);
        $end = $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();
        $daysDiff = $start->diffInDays($end);

        $labels = [];
        $data = [];

        if ($daysDiff <= 31) {
            // Daily data
            $current = $start->copy();
            while ($current->lte($end)) {
                $labels[] = $current->format('M d');
                $dayCount = $orders
                    ->filter(fn (Order $order) => Carbon::parse($order->created_at)->isSameDay($current))
                    ->count();
                $data[] = $dayCount;
                $current->addDay();
            }
        } elseif ($daysDiff <= 93) {
            // Weekly data
            $current = $start->copy()->startOfWeek();
            while ($current->lte($end)) {
                $weekEnd = $current->copy()->endOfWeek();
                if ($weekEnd->gt($end)) {
                    $weekEnd = $end->copy();
                }
                $labels[] = $current->format('M d').' - '.$weekEnd->format('M d');
                $weekCount = $orders
                    ->filter(function (Order $order) use ($current, $weekEnd) {
                        $orderDate = Carbon::parse($order->created_at);

                        return $orderDate->gte($current) && $orderDate->lte($weekEnd);
                    })
                    ->count();
                $data[] = $weekCount;
                $current->addWeek();
            }
        } else {
            // Monthly data
            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $monthEnd = $current->copy()->endOfMonth();
                if ($monthEnd->gt($end)) {
                    $monthEnd = $end->copy();
                }
                $labels[] = $current->format('M Y');
                $monthCount = $orders
                    ->filter(function (Order $order) use ($current, $monthEnd) {
                        $orderDate = Carbon::parse($order->created_at);

                        return $orderDate->gte($current) && $orderDate->lte($monthEnd);
                    })
                    ->count();
                $data[] = $monthCount;
                $current->addMonth();
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    #[Computed]
    public function ordersByStatusData(): array
    {
        $orders = $this->filteredOrders;

        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $data = [];
        $labels = [];
        $colors = [
            'pending' => '#fbbf24',
            'confirmed' => '#3b82f6',
            'processing' => '#a855f7',
            'shipped' => '#6366f1',
            'delivered' => '#10b981',
            'cancelled' => '#ef4444',
        ];
        $chartColors = [];

        foreach ($statuses as $status) {
            $count = $orders
                ->filter(fn (Order $order) => $order->status === $status)
                ->count();
            if ($count > 0 || ! $this->statusFilter) {
                $labels[] = ucfirst($status);
                $data[] = $count;
                $chartColors[] = $colors[$status];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $chartColors,
        ];
    }

    #[Computed]
    public function topProductsData(): array
    {
        $items = $this->filteredOrderItems;

        if ($items->isEmpty()) {
            return [
                'labels' => [],
                'revenue' => [],
                'quantity' => [],
            ];
        }

        $topProducts = $items
            ->groupBy(function (OrderItem $item) {
                return $item->product_id ?? $item->product_name ?? 'unknown';
            })
            ->map(function (Collection $group) {
                /** @var OrderItem $first */
                $first = $group->first();

                return [
                    'name' => $first->product_name ?? __('Unnamed Product'),
                    'total_quantity' => $group->sum('quantity'),
                    'total_revenue' => $group->sum(fn (OrderItem $item) => (float) $item->subtotal),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values();

        return [
            'labels' => $topProducts->pluck('name')->toArray(),
            'revenue' => $topProducts->pluck('total_revenue')->map(fn ($v) => (float) $v)->toArray(),
            'quantity' => $topProducts->pluck('total_quantity')->toArray(),
        ];
    }

    // Advanced Business Metrics

    #[Computed]
    public function revenueGrowthRate(): float
    {
        $currentPeriodRevenue = $this->totalRevenue;

        $start = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDays(30);
        $end = $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();
        $periodLength = $start->diffInDays($end);

        $previousStart = $start->copy()->subDays($periodLength);
        $previousEnd = $start->copy()->subDay();

        $previousRevenue = Order::query()
            ->where('created_at', '>=', $previousStart->startOfDay())
            ->where('created_at', '<=', $previousEnd->endOfDay())
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->sum('total');

        if ($previousRevenue == 0) {
            return $currentPeriodRevenue > 0 ? 100 : 0;
        }

        return round((($currentPeriodRevenue - $previousRevenue) / $previousRevenue) * 100, 2);
    }

    #[Computed]
    public function outstandingPayments(): float
    {
        return (float) $this->filteredOrders
            ->sum(fn (Order $order) => max(0, (float) $order->total - (float) $order->advance_payment));
    }

    #[Computed]
    public function totalDiscounts(): float
    {
        return (float) $this->filteredOrders->sum(fn (Order $order) => (float) $order->discount);
    }

    #[Computed]
    public function repeatCustomersCount(): int
    {
        return $this->filteredOrders
            ->filter(fn (Order $order) => filled($order->customer_email))
            ->groupBy('customer_email')
            ->filter(fn (Collection $orders) => $orders->count() > 1)
            ->count();
    }

    #[Computed]
    public function repeatCustomerRate(): float
    {
        $totalCustomers = $this->filteredOrders
            ->pluck('customer_email')
            ->filter()
            ->unique()
            ->count();
        $repeatCustomers = $this->repeatCustomersCount;

        return $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 2) : 0;
    }

    #[Computed]
    public function averageCustomerLifetimeValue(): float
    {
        $customerRevenue = $this->filteredOrders
            ->filter(fn (Order $order) => filled($order->customer_email))
            ->groupBy('customer_email')
            ->map(fn (Collection $orders) => $orders->sum(fn (Order $order) => (float) $order->total));

        return $customerRevenue->isNotEmpty() ? round($customerRevenue->average(), 2) : 0;
    }

    #[Computed]
    public function topCustomersData(): array
    {
        $topCustomers = $this->filteredOrders
            ->filter(fn (Order $order) => filled($order->customer_email))
            ->groupBy('customer_email')
            ->map(function (Collection $orders, string $email) {
                /** @var Order $latest */
                $latest = $orders->sortByDesc('created_at')->first();

                return [
                    'name' => $latest->customer_name ?: $email,
                    'total_spent' => $orders->sum(fn (Order $order) => (float) $order->total),
                    'order_count' => $orders->count(),
                ];
            })
            ->sortByDesc('total_spent')
            ->take(10)
            ->values();

        return [
            'labels' => $topCustomers->pluck('name')->toArray(),
            'revenue' => $topCustomers->pluck('total_spent')->map(fn ($v) => (float) $v)->toArray(),
            'orders' => $topCustomers->pluck('order_count')->toArray(),
        ];
    }

    #[Computed]
    public function newVsReturningCustomersData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDays(30);
        $previousPeriodEnd = $start->copy()->subDay();

        $previousCustomers = Order::where('created_at', '<=', $previousPeriodEnd->endOfDay())
            ->distinct('customer_email')
            ->pluck('customer_email')
            ->toArray();

        $currentCustomers = $this->filteredOrders
            ->pluck('customer_email')
            ->filter()
            ->unique()
            ->toArray();

        $newCustomers = array_diff($currentCustomers, $previousCustomers);
        $returningCustomers = array_intersect($currentCustomers, $previousCustomers);

        return [
            'labels' => ['New Customers', 'Returning Customers'],
            'data' => [count($newCustomers), count($returningCustomers)],
            'colors' => ['#3b82f6', '#10b981'],
        ];
    }

    #[Computed]
    public function paymentMethodData(): array
    {
        $methods = $this->filteredOrders
            ->groupBy(fn (Order $order) => $order->payment_method ?: __('Unknown Method'));

        $labels = $methods
            ->keys()
            ->map(fn ($method) => ucfirst(str_replace('_', ' ', (string) $method)))
            ->toArray();

        $counts = $methods
            ->map(fn (Collection $orders) => $orders->count())
            ->values()
            ->toArray();

        $revenue = $methods
            ->map(fn (Collection $orders) => $orders->sum(fn (Order $order) => (float) $order->total))
            ->values()
            ->toArray();

        $baseColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#f97316', '#22d3ee'];

        return [
            'labels' => $labels,
            'count' => $counts,
            'revenue' => array_map('floatval', $revenue),
            'colors' => array_slice($baseColors, 0, max(count($labels), 1)),
        ];
    }

    #[Computed]
    public function salesByDayOfWeekData(): array
    {
        $orders = $this->filteredOrders;
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $data = [];

        foreach ($daysOfWeek as $day) {
            $dayOrders = $orders
                ->filter(fn (Order $order) => Carbon::parse($order->created_at)->format('l') === $day);
            $data[] = [
                'day' => substr($day, 0, 3),
                'orders' => $dayOrders->count(),
                'revenue' => (float) $dayOrders->sum(fn (Order $order) => (float) $order->total),
            ];
        }

        return [
            'labels' => array_column($data, 'day'),
            'orders' => array_column($data, 'orders'),
            'revenue' => array_column($data, 'revenue'),
        ];
    }

    #[Computed]
    public function statusSummary(): array
    {
        $orders = $this->filteredOrders;

        if ($orders->isEmpty()) {
            return [];
        }

        $availableStatuses = $orders
            ->pluck('status')
            ->filter()
            ->unique()
            ->values();

        $orderedStatuses = collect(array_keys($this->statusDefinitions))
            ->filter(fn (string $status) => $availableStatuses->contains($status))
            ->values()
            ->concat(
                $availableStatuses->reject(fn (string $status) => array_key_exists($status, $this->statusDefinitions))
            );

        $totalOrders = max($orders->count(), 1);

        return $orderedStatuses
            ->map(function (string $status) use ($orders, $totalOrders) {
                $matching = $orders->filter(fn (Order $order) => $order->status === $status);
                $count = $matching->count();
                $revenue = $matching->sum(fn (Order $order) => (float) $order->total);
                $meta = $this->statusMeta($status);

                return [
                    'status' => $status,
                    'label' => $meta['label'],
                    'tone' => $meta['tone'],
                    'hex' => $meta['hex'],
                    'count' => $count,
                    'percentage' => $totalOrders > 0 ? round(($count / $totalOrders) * 100, 1) : 0,
                    'revenue' => round($revenue, 2),
                ];
            })
            ->filter(fn (array $summary) => $summary['count'] > 0)
            ->values()
            ->toArray();
    }

    #[Computed]
    public function recentOrders(): Collection
    {
        return $this->filteredOrders
            ->sortByDesc(fn (Order $order) => $order->created_at)
            ->take(5)
            ->values();
    }

    #[Computed]
    public function salesByCityData(): array
    {
        $cityData = $this->filteredOrders
            ->filter(fn (Order $order) => filled($order->shipping_city))
            ->groupBy('shipping_city')
            ->map(function (Collection $orders, string $city) {
                return [
                    'city' => $city,
                    'order_count' => $orders->count(),
                    'total_revenue' => $orders->sum(fn (Order $order) => (float) $order->total),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values();

        return [
            'labels' => $cityData->pluck('city')->toArray(),
            'orders' => $cityData->pluck('order_count')->toArray(),
            'revenue' => $cityData->pluck('total_revenue')->map(fn ($v) => (float) $v)->toArray(),
        ];
    }

    #[Computed]
    public function salesByCategoryData(): array
    {
        $items = $this->filteredOrderItems;

        if ($items->isEmpty()) {
            return [
                'labels' => [],
                'revenue' => [],
                'quantity' => [],
            ];
        }

        $categoryData = $items
            ->groupBy(function (OrderItem $item) {
                return $item->product?->category?->name_en ?? __('Uncategorized');
            })
            ->map(function (Collection $group, string $categoryName) {
                return [
                    'category' => $categoryName,
                    'total_quantity' => $group->sum('quantity'),
                    'total_revenue' => $group->sum(fn (OrderItem $item) => (float) $item->subtotal),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values();

        return [
            'labels' => $categoryData->pluck('category')->toArray(),
            'revenue' => $categoryData->pluck('total_revenue')->map(fn ($v) => (float) $v)->toArray(),
            'quantity' => $categoryData->pluck('total_quantity')->toArray(),
        ];
    }

    #[Computed]
    public function cancellationRate(): float
    {
        $total = $this->totalOrders;
        $cancelled = $this->filteredOrders
            ->filter(fn (Order $order) => $order->status === 'cancelled')
            ->count();

        return $total > 0 ? round(($cancelled / $total) * 100, 2) : 0;
    }

    #[Computed]
    public function averageOrderQuantity(): float
    {
        $totalQuantity = $this->filteredOrderItems->sum('quantity');
        $orderCount = $this->totalOrders;

        return $orderCount > 0 ? round($totalQuantity / $orderCount, 2) : 0;
    }

    #[Computed]
    public function lowStockProducts(): Collection
    {
        return Product::with('productAttributes')
            ->where('is_active', true)
            ->get()
            ->filter(function ($product) {
                return $product->getSyncedStock() <= 10 && $product->getSyncedStock() > 0;
            })
            ->sortBy(fn ($product) => $product->getSyncedStock())
            ->take(10)
            ->values();
    }

    #[Computed]
    public function conversionFunnelData(): array
    {
        $orders = $this->filteredOrders;
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
        $data = [];

        foreach ($statuses as $status) {
            $data[] = $orders
                ->filter(fn (Order $order) => $order->status === $status)
                ->count();
        }

        return [
            'labels' => array_map('ucfirst', $statuses),
            'data' => $data,
        ];
    }

    #[Computed]
    public function discountImpactData(): array
    {
        $orders = $this->filteredOrders;
        $withDiscount = $orders->filter(fn (Order $order) => (float) $order->discount > 0);
        $withoutDiscount = $orders->filter(fn (Order $order) => (float) $order->discount == 0);

        return [
            'labels' => ['With Discount', 'Without Discount'],
            'count' => [$withDiscount->count(), $withoutDiscount->count()],
            'average_order_value' => [
                $withDiscount->count() > 0 ? round($withDiscount->average(fn (Order $order) => (float) $order->total), 2) : 0,
                $withoutDiscount->count() > 0 ? round($withoutDiscount->average(fn (Order $order) => (float) $order->total), 2) : 0,
            ],
        ];
    }

    #[Computed]
    public function chartDataBundle(): array
    {
        return [
            'revenue_chart' => $this->revenueChartData,
            'orders_chart' => $this->ordersChartData,
            'status_chart' => $this->ordersByStatusData,
            'products_chart' => $this->topProductsData,
            'top_customers_chart' => $this->topCustomersData,
            'new_vs_returning_chart' => $this->newVsReturningCustomersData,
            'day_of_week_chart' => $this->salesByDayOfWeekData,
            'payment_method_chart' => $this->paymentMethodData,
            'category_chart' => $this->salesByCategoryData,
            'city_chart' => $this->salesByCityData,
            'conversion_funnel_chart' => $this->conversionFunnelData,
            'discount_impact_chart' => $this->discountImpactData,
        ];
    }

    #[Computed]
    public function dashboardInsights(): array
    {
        if ($this->totalOrders === 0) {
            return [];
        }

        $preferences = $this->userPreferences;
        $availableInsights = $this->availableInsightCards;

        $allInsights = [];
        $growth = $this->revenueGrowthRate;

        $allInsights['revenue_trend'] = [
            'key' => 'revenue_trend',
            'title' => __('Revenue Trend'),
            'body' => $growth >= 0
                ? __('Revenue is up :percentage% compared to the previous period.', ['percentage' => number_format($growth, 1)])
                : __('Revenue is down :percentage% compared to the previous period.', ['percentage' => number_format(abs($growth), 1)]),
            'variant' => $growth >= 0 ? 'success' : 'danger',
            'icon' => $growth >= 0 ? 'trending-up' : 'trending-down',
        ];

        $topProducts = $this->topProductsData;
        if (! empty($topProducts['labels'])) {
            $topProductName = $topProducts['labels'][0];
            $topProductRevenue = $topProducts['revenue'][0] ?? 0;

            $allInsights['top_product'] = [
                'key' => 'top_product',
                'title' => __('Top Product'),
                'body' => __(':product generated :currency:amount this period.', [
                    'product' => $topProductName,
                    'currency' => Setting::get('currency_symbol', '৳'),
                    'amount' => number_format((float) $topProductRevenue, 2),
                ]),
                'variant' => 'info',
                'icon' => 'sparkles',
            ];
        }

        $repeatRate = $this->repeatCustomerRate;
        $allInsights['customer_loyalty'] = [
            'key' => 'customer_loyalty',
            'title' => __('Customer Loyalty'),
            'body' => $repeatRate > 0
                ? __(':percentage% of orders came from returning customers.', ['percentage' => number_format($repeatRate, 1)])
                : __('All orders came from new customers this period.'),
            'variant' => $repeatRate > 0 ? 'warning' : 'primary',
            'icon' => $repeatRate > 0 ? 'users' : 'user-plus',
        ];

        $statusSummary = collect($this->statusSummary);

        $pendingSummary = $statusSummary->firstWhere('status', 'pending');
        if ($pendingSummary && $pendingSummary['percentage'] >= 35) {
            $allInsights['fulfillment_focus'] = [
                'key' => 'fulfillment_focus',
                'title' => __('Fulfillment Focus'),
                'body' => __(':percentage% of orders are still pending. Prioritize confirmations to keep things moving.', [
                    'percentage' => number_format((float) $pendingSummary['percentage'], 1),
                ]),
                'variant' => 'warning',
                'icon' => 'clock',
            ];
        }

        $cancelledSummary = $statusSummary->firstWhere('status', 'cancelled');
        if ($cancelledSummary && $cancelledSummary['count'] > 0) {
            $allInsights['cancellations_watch'] = [
                'key' => 'cancellations_watch',
                'title' => __('Cancellations Watch'),
                'body' => __(':count orders were cancelled this period. Investigate the reasons to protect revenue.', [
                    'count' => $cancelledSummary['count'],
                ]),
                'variant' => 'danger',
                'icon' => 'x-circle',
            ];
        }

        $outstanding = $this->outstandingPayments;
        if ($outstanding > 0) {
            $allInsights['outstanding_payments'] = [
                'key' => 'outstanding_payments',
                'title' => __('Outstanding Payments'),
                'body' => __(':currency:amount is pending collection across open orders.', [
                    'currency' => Setting::get('currency_symbol', '৳'),
                    'amount' => number_format($outstanding, 2),
                ]),
                'variant' => 'warning',
                'icon' => 'wallet',
            ];
        }

        $topCityData = $this->salesByCityData;
        if (! empty($topCityData['labels'])) {
            $topCity = $topCityData['labels'][0];
            $topCityRevenue = $topCityData['revenue'][0] ?? 0;

            $allInsights['regional_momentum'] = [
                'key' => 'regional_momentum',
                'title' => __('Regional Momentum'),
                'body' => __(':city leads revenue with :currency:amount in sales.', [
                    'city' => $topCity,
                    'currency' => Setting::get('currency_symbol', '৳'),
                    'amount' => number_format((float) $topCityRevenue, 2),
                ]),
                'variant' => 'info',
                'icon' => 'map-pin',
            ];
        }

        // Filter insights based on user preferences
        $visibleInsights = collect($allInsights)->filter(function ($insight, $key) use ($preferences, $availableInsights) {
            // If preference exists (must be insight type), check visibility
            $pref = $preferences->firstWhere(function ($pref) use ($key) {
                return $pref->card_key === $key && $pref->card_type === 'insight';
            });
            if ($pref) {
                return $pref->is_visible;
            }

            // If no preference exists but insight is available, default to visible
            return isset($availableInsights[$key]);
        })->values()->toArray();

        return $visibleInsights;
    }

    public function getMetricValue(string $cardKey): mixed
    {
        return match ($cardKey) {
            'total_revenue' => $this->totalRevenue,
            'total_orders' => $this->totalOrders,
            'average_order_value' => $this->averageOrderValue,
            'total_products' => $this->totalProducts,
            'revenue_growth' => $this->revenueGrowthRate,
            'outstanding_payments' => $this->outstandingPayments,
            'repeat_customer_rate' => $this->repeatCustomerRate,
            'average_customer_value' => $this->averageCustomerLifetimeValue,
            'cancellation_rate' => $this->cancellationRate,
            'average_order_quantity' => $this->averageOrderQuantity,
            'total_discounts' => $this->totalDiscounts,
            'repeat_customers' => $this->repeatCustomersCount,
            'low_stock_items' => $this->lowStockProducts->count(),
            default => null,
        };
    }

    public function getMetricSubtitle(string $cardKey): ?string
    {
        return match ($cardKey) {
            'total_revenue' => $this->ordersQuery->count() > 0 ? $this->totalOrders.' '.__('orders') : null,
            'repeat_customer_rate' => $this->repeatCustomersCount.' '.__('repeat customers'),
            'average_customer_value' => __('Customer Lifetime Value'),
            'revenue_growth' => __('vs previous period'),
            default => null,
        };
    }

    public function statusLabel(string $status): string
    {
        return $this->statusMeta($status)['label'];
    }

    public function statusBadgeClasses(string $status): string
    {
        return match ($this->statusMeta($status)['tone']) {
            'amber' => 'border border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
            'blue' => 'border border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
            'purple' => 'border border-purple-200 bg-purple-50 text-purple-700 dark:border-purple-800 dark:bg-purple-900/30 dark:text-purple-200',
            'indigo' => 'border border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200',
            'emerald' => 'border border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200',
            'rose' => 'border border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-200',
            default => 'border border-zinc-200 bg-zinc-50 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900/40 dark:text-zinc-300',
        };
    }

    public function statusChipClasses(string $status): string
    {
        return match ($this->statusMeta($status)['tone']) {
            'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200',
            'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200',
            'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-200',
            'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200',
            'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
            'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
            default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-900/40 dark:text-zinc-300',
        };
    }

    public function statusProgressClasses(string $status): string
    {
        return match ($this->statusMeta($status)['tone']) {
            'amber' => 'bg-amber-500',
            'blue' => 'bg-blue-500',
            'purple' => 'bg-purple-500',
            'indigo' => 'bg-indigo-500',
            'emerald' => 'bg-emerald-500',
            'rose' => 'bg-rose-500',
            default => 'bg-zinc-500',
        };
    }

    public function formatPaymentMethod(?string $method): string
    {
        if (! $method) {
            return __('Unknown Method');
        }

        return Str::of($method)
            ->replace('_', ' ')
            ->headline()
            ->toString();
    }

    protected function statusMeta(string $status): array
    {
        $definition = $this->statusDefinitions[$status] ?? null;

        return [
            'label' => $definition ? __($definition['label']) : Str::headline($status),
            'tone' => $definition['tone'] ?? 'zinc',
            'hex' => $definition['hex'] ?? '#71717a',
        ];
    }
}
