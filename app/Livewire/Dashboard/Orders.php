<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

/**
 * Orders & Fulfillment dashboard sub-page.
 *
 * Surfaces order-throughput metrics (total orders, cancellation rate,
 * average order quantity), the charts that explain order flow (orders over
 * time, status breakdown, day-of-week distribution, conversion funnel), and
 * the two insight cards relevant to fulfillment health. The "Order Status
 * Health" progress-bar block and "Recent Orders" mini-table are rendered
 * directly in this page's view (they are not part of the card-preference
 * system).
 */
class Orders extends DashboardPageComponent
{
    public function pageKey(): string
    {
        return 'orders';
    }

    #[Computed]
    public function availableMetricCards(): array
    {
        return Arr::only($this->allMetricDefinitions(), [
            'total_orders',
            'cancellation_rate',
            'average_order_quantity',
        ]);
    }

    #[Computed]
    public function availableChartCards(): array
    {
        return Arr::only($this->allChartDefinitions(), [
            'orders_chart',
            'status_chart',
            'day_of_week_chart',
            'conversion_funnel_chart',
        ]);
    }

    #[Computed]
    public function availableInsightCards(): array
    {
        return Arr::only($this->allInsightDefinitions(), [
            'fulfillment_focus',
            'cancellations_watch',
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.orders')->layout('components.layouts.app', [
            'title' => __('Orders & Fulfillment'),
        ]);
    }
}
