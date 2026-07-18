<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

/**
 * Overview dashboard page: top-level KPI metric tiles only. No charts or
 * insights are assigned to this page (see cardAssignment in the project
 * plan), so availableChartCards()/availableInsightCards() are left
 * unoverridden and fall back to the base class's empty-array default.
 */
class Overview extends DashboardPageComponent
{
    public function pageKey(): string
    {
        return 'overview';
    }

    #[Computed]
    public function availableMetricCards(): array
    {
        return Arr::only($this->allMetricDefinitions(), [
            'total_revenue',
            'total_orders',
            'average_order_value',
            'revenue_growth',
            'total_products',
            'outstanding_payments',
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.overview')->layout('components.layouts.app', [
            'title' => __('Overview'),
        ]);
    }
}
