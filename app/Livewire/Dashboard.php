<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserDashboardPreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $statusFilter = '';

    public string $dateRange = '30'; // '7', '30', '90', '365', 'custom'

    public bool $isCustomizing = false;

    public bool $showResetConfirmation = false;

    public bool $isRefreshing = false;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateRange' => ['except' => '30'],
    ];

    protected array $statusDefinitions = [
        'pending' => ['label' => 'Pending', 'tone' => 'amber', 'hex' => '#f59e0b'],
        'confirmed' => ['label' => 'Confirmed', 'tone' => 'blue', 'hex' => '#3b82f6'],
        'processing' => ['label' => 'Processing', 'tone' => 'purple', 'hex' => '#a855f7'],
        'shipped' => ['label' => 'Shipped', 'tone' => 'indigo', 'hex' => '#6366f1'],
        'delivered' => ['label' => 'Delivered', 'tone' => 'emerald', 'hex' => '#10b981'],
        'cancelled' => ['label' => 'Cancelled', 'tone' => 'rose', 'hex' => '#ef4444'],
    ];

    public function mount(): void
    {
        if (! $this->startDate && ! $this->endDate) {
            $this->endDate = Carbon::now()->format('Y-m-d');
            $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        $this->initializeUserPreferences();
    }

    public function refreshDashboard(): void
    {
        $this->isRefreshing = true;

        // Clear all computed properties to force refresh
        unset(
            $this->orderedMetricCards,
            $this->orderedChartCards,
            $this->userPreferences,
            $this->dashboardInsights,
            $this->filteredOrders,
            $this->filteredOrderItems,
            $this->totalRevenue,
            $this->totalOrders,
            $this->averageOrderValue,
            $this->revenueChartData,
            $this->ordersChartData,
            $this->statusSummary,
            $this->recentOrders,
            $this->lowStockProducts
        );

        // Get chart keys after clearing cache
        $chartKeys = $this->orderedChartCards->pluck('key')->toArray();

        // Dispatch refresh event
        $this->dispatch('dashboard:refresh-charts', [
            'visibleKeys' => $chartKeys,
            'data' => $this->chartDataBundle(),
        ]);

        $this->isRefreshing = false;
        session()->flash('message', __('Dashboard refreshed successfully.'));
    }

    #[Computed]
    public function availableMetricCards(): array
    {
        return [
            'total_revenue' => [
                'title' => __('Total Revenue'),
                'type' => 'metric',
                'icon' => 'currency',
                'color' => 'green',
                'style' => 'minimal',
            ],
            'total_orders' => [
                'title' => __('Total Orders'),
                'type' => 'metric',
                'icon' => 'shopping',
                'color' => 'blue',
                'style' => 'minimal',
            ],
            'average_order_value' => [
                'title' => __('Average Order Value'),
                'type' => 'metric',
                'icon' => 'chart',
                'color' => 'purple',
                'style' => 'minimal',
            ],
            'total_products' => [
                'title' => __('Total Products'),
                'type' => 'metric',
                'icon' => 'package',
                'color' => 'indigo',
                'style' => 'minimal',
            ],
            'revenue_growth' => [
                'title' => __('Revenue Growth'),
                'type' => 'metric',
                'icon' => 'trending',
                'color' => 'emerald',
                'style' => 'minimal',
            ],
            'outstanding_payments' => [
                'title' => __('Outstanding Payments'),
                'type' => 'metric',
                'icon' => 'payment',
                'color' => 'amber',
                'style' => 'minimal',
            ],
            'repeat_customer_rate' => [
                'title' => __('Repeat Customer Rate'),
                'type' => 'metric',
                'icon' => 'users',
                'color' => 'cyan',
                'style' => 'minimal',
            ],
            'average_customer_value' => [
                'title' => __('Avg. Customer Value'),
                'type' => 'metric',
                'icon' => 'user',
                'color' => 'emerald',
                'style' => 'minimal',
            ],
            'cancellation_rate' => [
                'title' => __('Cancellation Rate'),
                'type' => 'metric',
                'icon' => 'chart',
                'color' => 'red',
                'style' => 'minimal',
            ],
            'average_order_quantity' => [
                'title' => __('Avg. Order Quantity'),
                'type' => 'metric',
                'icon' => 'package',
                'color' => 'blue',
                'style' => 'minimal',
            ],
            'total_discounts' => [
                'title' => __('Total Discounts'),
                'type' => 'metric',
                'icon' => 'payment',
                'color' => 'green',
                'style' => 'minimal',
            ],
            'repeat_customers' => [
                'title' => __('Repeat Customers'),
                'type' => 'metric',
                'icon' => 'users',
                'color' => 'purple',
                'style' => 'minimal',
            ],
            'low_stock_items' => [
                'title' => __('Low Stock Items'),
                'type' => 'metric',
                'icon' => 'package',
                'color' => 'amber',
                'style' => 'minimal',
            ],
        ];
    }

    #[Computed]
    public function availableChartCards(): array
    {
        return [
            'revenue_chart' => [
                'title' => __('Revenue Over Time'),
                'type' => 'chart',
            ],
            'orders_chart' => [
                'title' => __('Orders Over Time'),
                'type' => 'chart',
            ],
            'status_chart' => [
                'title' => __('Orders by Status'),
                'type' => 'chart',
            ],
            'products_chart' => [
                'title' => __('Top Products by Revenue'),
                'type' => 'chart',
            ],
            'top_customers_chart' => [
                'title' => __('Top Customers by Revenue'),
                'type' => 'chart',
            ],
            'new_vs_returning_chart' => [
                'title' => __('New vs Returning Customers'),
                'type' => 'chart',
            ],
            'day_of_week_chart' => [
                'title' => __('Sales by Day of Week'),
                'type' => 'chart',
            ],
            'payment_method_chart' => [
                'title' => __('Revenue by Payment Method'),
                'type' => 'chart',
            ],
            'category_chart' => [
                'title' => __('Sales by Category'),
                'type' => 'chart',
            ],
            'city_chart' => [
                'title' => __('Top Cities by Revenue'),
                'type' => 'chart',
            ],
            'conversion_funnel_chart' => [
                'title' => __('Order Conversion Funnel'),
                'type' => 'chart',
            ],
            'discount_impact_chart' => [
                'title' => __('Discount Impact Analysis'),
                'type' => 'chart',
            ],
        ];
    }

    #[Computed]
    public function availableInsightCards(): array
    {
        return [
            'revenue_trend' => [
                'title' => __('Revenue Trend'),
                'type' => 'insight',
                'icon' => 'trending-up',
                'color' => 'green',
            ],
            'top_product' => [
                'title' => __('Top Product'),
                'type' => 'insight',
                'icon' => 'sparkles',
                'color' => 'blue',
            ],
            'customer_loyalty' => [
                'title' => __('Customer Loyalty'),
                'type' => 'insight',
                'icon' => 'users',
                'color' => 'purple',
            ],
            'outstanding_payments' => [
                'title' => __('Outstanding Payments'),
                'type' => 'insight',
                'icon' => 'wallet',
                'color' => 'amber',
            ],
            'regional_momentum' => [
                'title' => __('Regional Momentum'),
                'type' => 'insight',
                'icon' => 'map-pin',
                'color' => 'indigo',
            ],
            'fulfillment_focus' => [
                'title' => __('Fulfillment Focus'),
                'type' => 'insight',
                'icon' => 'clock',
                'color' => 'amber',
            ],
            'cancellations_watch' => [
                'title' => __('Cancellations Watch'),
                'type' => 'insight',
                'icon' => 'x-circle',
                'color' => 'red',
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
    public function userPreferences(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        $preferences = UserDashboardPreference::where('user_id', $user->id)->get();

        // If no preferences exist, create defaults
        if ($preferences->isEmpty()) {
            $this->createDefaultPreferences($user->id);

            return UserDashboardPreference::where('user_id', $user->id)->get();
        }

        return $preferences;
    }

    #[Computed]
    public function orderedMetricCards(): Collection
    {
        $preferences = $this->userPreferences;
        $available = $this->availableMetricCards;

        // Get cards with preferences
        $cardsWithPrefs = $preferences
            ->where('card_type', 'metric')
            ->where('is_visible', true)
            ->sortBy('order')
            ->map(function ($pref) use ($available) {
                $card = $available[$pref->card_key] ?? null;
                if ($card) {
                    $card['key'] = $pref->card_key;
                    $card['preference'] = $pref;
                    $card['order'] = $pref->order;
                }

                return $card;
            })
            ->filter();

        // Get cards without preferences (new cards) - default to visible
        $maxOrder = $preferences->where('card_type', 'metric')->max('order') ?? 0;
        $cardsWithoutPrefs = collect($available)
            ->filter(function ($card, $key) use ($preferences) {
                return ! $preferences->contains('card_key', $key);
            })
            ->map(function ($card, $key) use (&$maxOrder) {
                $card['key'] = $key;
                $card['order'] = ++$maxOrder;

                return $card;
            });

        // Merge and sort by order
        return $cardsWithPrefs
            ->merge($cardsWithoutPrefs)
            ->sortBy('order')
            ->values();
    }

    #[Computed]
    public function orderedChartCards(): Collection
    {
        $preferences = $this->userPreferences;
        $available = $this->availableChartCards;

        return $preferences
            ->where('card_type', 'chart')
            ->where('is_visible', true)
            ->sortBy('order')
            ->map(function ($pref) use ($available) {
                $card = $available[$pref->card_key] ?? null;
                if ($card) {
                    $card['key'] = $pref->card_key;
                    $card['preference'] = $pref;
                }

                return $card;
            })
            ->filter();
    }

    protected function initializeUserPreferences(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $existing = UserDashboardPreference::where('user_id', $user->id)->count();
        if ($existing === 0) {
            $this->createDefaultPreferences($user->id);
        }
    }

    protected function createDefaultPreferences(int $userId): void
    {
        $metrics = [
            'total_revenue' => 1,
            'total_orders' => 2,
            'average_order_value' => 3,
            'total_products' => 4,
            'revenue_growth' => 5,
            'outstanding_payments' => 6,
            'repeat_customer_rate' => 7,
            'average_customer_value' => 8,
            'cancellation_rate' => 9,
            'average_order_quantity' => 10,
            'total_discounts' => 11,
            'repeat_customers' => 12,
            'low_stock_items' => 13,
        ];

        $insights = [
            'revenue_trend' => 1,
            'top_product' => 2,
            'customer_loyalty' => 3,
            'outstanding_payments' => 4,
            'regional_momentum' => 5,
            'fulfillment_focus' => 6,
            'cancellations_watch' => 7,
        ];

        $charts = [
            'revenue_chart' => 1,
            'orders_chart' => 2,
            'status_chart' => 3,
            'products_chart' => 4,
            'top_customers_chart' => 5,
            'new_vs_returning_chart' => 6,
            'day_of_week_chart' => 7,
            'payment_method_chart' => 8,
            'category_chart' => 9,
            'city_chart' => 10,
            'conversion_funnel_chart' => 11,
            'discount_impact_chart' => 12,
        ];

        foreach ($metrics as $key => $order) {
            UserDashboardPreference::create([
                'user_id' => $userId,
                'card_key' => $key,
                'order' => $order,
                'is_visible' => true,
                'card_type' => 'metric',
            ]);
        }

        foreach ($insights as $key => $order) {
            UserDashboardPreference::create([
                'user_id' => $userId,
                'card_key' => $key,
                'order' => $order,
                'is_visible' => true,
                'card_type' => 'insight',
            ]);
        }

        foreach ($charts as $key => $order) {
            UserDashboardPreference::create([
                'user_id' => $userId,
                'card_key' => $key,
                'order' => $order,
                'is_visible' => true,
                'card_type' => 'chart',
            ]);
        }
    }

    public function toggleCardVisibility(string $cardKey): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        // Determine card type first
        $cardType = $this->availableMetricCards[$cardKey]['type']
            ?? $this->availableChartCards[$cardKey]['type']
            ?? $this->availableInsightCards[$cardKey]['type']
            ?? 'metric';

        // Find preference matching both key and type
        $pref = UserDashboardPreference::where('user_id', $user->id)
            ->where('card_key', $cardKey)
            ->where('card_type', $cardType)
            ->first();

        if ($pref) {
            $newVisibility = ! $pref->is_visible;
            $pref->update(['is_visible' => $newVisibility]);
        } else {
            // Create preference if it doesn't exist (for new cards)
            $maxOrder = UserDashboardPreference::where('user_id', $user->id)
                ->where('card_type', $cardType)
                ->max('order') ?? 0;

            UserDashboardPreference::create([
                'user_id' => $user->id,
                'card_key' => $cardKey,
                'card_type' => $cardType,
                'order' => $maxOrder + 1,
                'is_visible' => true,
            ]);
        }

        // Clear computed property cache
        unset($this->orderedMetricCards, $this->orderedChartCards, $this->userPreferences, $this->dashboardInsights);
    }

    public function updateCardOrder(array $cardOrder): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        foreach ($cardOrder as $index => $cardKey) {
            UserDashboardPreference::where('user_id', $user->id)
                ->where('card_key', $cardKey)
                ->update(['order' => $index + 1]);
        }

        // Clear computed property cache to refresh order
        unset($this->orderedMetricCards, $this->orderedChartCards, $this->userPreferences, $this->dashboardInsights);
    }

    public function toggleCustomization(): void
    {
        $this->isCustomizing = ! $this->isCustomizing;

        if (! $this->isCustomizing) {
            $this->showResetConfirmation = false;
        }
        // Clear cache to refresh cards
        unset($this->orderedMetricCards, $this->orderedChartCards, $this->dashboardInsights);

        // Dispatch event for JavaScript
        $this->dispatch('customization-toggled');
    }

    public function resetDashboardPreferences(): void
    {
        $user = auth()->user();
        if (! $user) {
            session()->flash('error', __('You must be logged in to reset dashboard preferences.'));

            return;
        }

        UserDashboardPreference::where('user_id', $user->id)->delete();
        $this->createDefaultPreferences($user->id);

        unset(
            $this->orderedMetricCards,
            $this->orderedChartCards,
            $this->userPreferences,
            $this->dashboardInsights,
            $this->filteredOrders,
            $this->filteredOrderItems
        );

        $this->showResetConfirmation = true;
        session()->flash('message', __('Dashboard layout was reset to the default arrangement.'));

        $this->dispatch('dashboard-preferences-reset');
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

    #[Computed]
    public function ordersQuery(): Builder
    {
        $query = Order::query();

        if ($this->startDate) {
            $query->where('created_at', '>=', Carbon::parse($this->startDate)->startOfDay());
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', Carbon::parse($this->endDate)->endOfDay());
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query;
    }

    protected function cloneOrdersQuery(): Builder
    {
        return clone $this->ordersQuery;
    }

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
                'body' => __(':product generated ৳:amount this period.', [
                    'product' => $topProductName,
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
                'body' => __('৳:amount is pending collection across open orders.', [
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
                'body' => __(':city leads revenue with ৳:amount in sales.', [
                    'city' => $topCity,
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

    public function updatedDateRange(string $value): void
    {
        $ranges = [
            '7' => 7,
            '30' => 30,
            '90' => 90,
            '365' => 365,
        ];

        if (array_key_exists($value, $ranges)) {
            $now = Carbon::now();
            $this->endDate = $now->format('Y-m-d');
            $this->startDate = $now->copy()->subDays($ranges[$value])->format('Y-m-d');
        }

        $this->clearFilterDependentCache();
    }

    public function updatedStartDate(?string $value): void
    {
        if ($value) {
            $this->dateRange = 'custom';
        }

        $this->validateDateRange();
    }

    public function updatedEndDate(?string $value): void
    {
        if ($value) {
            $this->dateRange = 'custom';
        }

        $this->validateDateRange();
    }

    public function updatedStatusFilter(?string $value): void
    {
        $this->clearFilterDependentCache();
    }

    protected function validateDateRange(): void
    {
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);

            if ($start->gt($end)) {
                $this->startDate = $end->copy()->subDays(7)->format('Y-m-d');
            }
        }

        $this->clearFilterDependentCache();
    }

    protected function clearFilterDependentCache(): void
    {
        // Clear all computed properties that depend on filtered orders
        unset(
            $this->ordersQuery,
            $this->filteredOrders,
            $this->filteredOrderItems,
            $this->totalRevenue,
            $this->totalOrders,
            $this->averageOrderValue,
            $this->revenueGrowthRate,
            $this->outstandingPayments,
            $this->totalDiscounts,
            $this->repeatCustomersCount,
            $this->repeatCustomerRate,
            $this->averageCustomerLifetimeValue,
            $this->cancellationRate,
            $this->averageOrderQuantity,
            $this->revenueChartData,
            $this->ordersChartData,
            $this->ordersByStatusData,
            $this->topProductsData,
            $this->topCustomersData,
            $this->newVsReturningCustomersData,
            $this->paymentMethodData,
            $this->salesByDayOfWeekData,
            $this->statusSummary,
            $this->recentOrders,
            $this->salesByCityData,
            $this->salesByCategoryData,
            $this->conversionFunnelData,
            $this->discountImpactData,
            $this->dashboardInsights,
            $this->chartDataBundle
        );
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

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.app', [
            'title' => __('Dashboard'),
        ]);
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
