<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Dashboard\Concerns\HasCardPreferences;
use App\Livewire\Dashboard\Concerns\HasDashboardAnalytics;
use App\Livewire\Dashboard\Concerns\HasDashboardFilters;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Shared base for the 5 dashboard sub-pages (Overview, Sales, Orders,
 * Customers, Products). Combines the filter state, the analytics data
 * engine, and the card customization system, and defines the full
 * canonical set of card definitions that a subclass picks a subset from.
 *
 * Subclasses must implement pageKey() and override availableMetricCards(),
 * availableChartCards(), and/or availableInsightCards() to expose only the
 * cards that belong on that page (filtering allMetricDefinitions() etc. down
 * to their assigned keys). A page with no cards of a given type can leave
 * the corresponding accessor unoverridden — it defaults to [].
 */
abstract class DashboardPageComponent extends Component
{
    use HasDashboardFilters;
    use HasDashboardAnalytics;
    use HasCardPreferences;

    public bool $isRefreshing = false;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateRange' => ['except' => '30'],
    ];

    /**
     * Unique identifier for this page, used to scope card preferences
     * (user_id, card_key, card_type, page) independently per sub-page.
     */
    abstract public function pageKey(): string;

    /**
     * The full canonical set of metric card definitions (title/icon/color/
     * style), unfiltered. Every one of the 13 metric keys from the original
     * monolith is defined here exactly once.
     */
    protected function allMetricDefinitions(): array
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

    /**
     * The full canonical set of chart card definitions, unfiltered. Every
     * one of the 12 chart keys from the original monolith is defined here
     * exactly once.
     */
    protected function allChartDefinitions(): array
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

    /**
     * The full canonical set of insight card definitions, unfiltered. Every
     * one of the 7 insight keys from the original monolith is defined here
     * exactly once.
     */
    protected function allInsightDefinitions(): array
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
    public function availableMetricCards(): array
    {
        return [];
    }

    #[Computed]
    public function availableChartCards(): array
    {
        return [];
    }

    #[Computed]
    public function availableInsightCards(): array
    {
        return [];
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
}
