<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

/**
 * Customers dashboard sub-page: repeat-customer metrics, the top-customers /
 * new-vs-returning / top-cities charts, and the customer-loyalty +
 * regional-momentum insight callouts.
 */
class Customers extends DashboardPageComponent
{
    public function pageKey(): string
    {
        return 'customers';
    }

    #[Computed]
    public function availableMetricCards(): array
    {
        return Arr::only($this->allMetricDefinitions(), [
            'repeat_customer_rate',
            'average_customer_value',
            'repeat_customers',
        ]);
    }

    #[Computed]
    public function availableChartCards(): array
    {
        return Arr::only($this->allChartDefinitions(), [
            'top_customers_chart',
            'new_vs_returning_chart',
            'city_chart',
        ]);
    }

    #[Computed]
    public function availableInsightCards(): array
    {
        return Arr::only($this->allInsightDefinitions(), [
            'customer_loyalty',
            'regional_momentum',
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.customers')->layout('components.layouts.app', [
            'title' => __('Customers'),
        ]);
    }
}
