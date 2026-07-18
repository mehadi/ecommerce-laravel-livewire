<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

/**
 * The "Sales & Revenue" dashboard sub-page: revenue/discount/payment metrics,
 * revenue & payment-method & discount-impact charts, plus the revenue-trend
 * and outstanding-payments insight callouts.
 */
class Sales extends DashboardPageComponent
{
    public function pageKey(): string
    {
        return 'sales';
    }

    #[Computed]
    public function availableMetricCards(): array
    {
        return Arr::only($this->allMetricDefinitions(), [
            'total_revenue',
            'average_order_value',
            'revenue_growth',
            'outstanding_payments',
            'total_discounts',
        ]);
    }

    #[Computed]
    public function availableChartCards(): array
    {
        return Arr::only($this->allChartDefinitions(), [
            'revenue_chart',
            'payment_method_chart',
            'discount_impact_chart',
        ]);
    }

    #[Computed]
    public function availableInsightCards(): array
    {
        return Arr::only($this->allInsightDefinitions(), [
            'revenue_trend',
            'outstanding_payments',
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.sales')->layout('components.layouts.app', [
            'title' => __('Sales & Revenue'),
        ]);
    }
}
