<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Dashboard\Concerns\HasReportingAnalytics;
use App\Support\Tenancy;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

/**
 * The "Sales & Revenue" dashboard sub-page: revenue/discount/payment metrics,
 * revenue & payment-method & discount-impact charts, plus the revenue-trend
 * and outstanding-payments insight callouts. Also carries the Coupon
 * Performance table (via HasReportingAnalytics) as the natural sibling of the
 * Discount Impact chart — gated behind advanced_analytics_enabled since it's
 * one of the reports the reporting-gap analysis added.
 */
class Sales extends DashboardPageComponent
{
    use HasReportingAnalytics;

    public bool $hasAdvancedAnalytics = false;

    public function mount(): void
    {
        $this->hasAdvancedAnalytics = Tenancy::hasFeature('advanced_analytics_enabled');
    }

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
