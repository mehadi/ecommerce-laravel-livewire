<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

/**
 * Products dashboard sub-page.
 *
 * Metric cards: total_products, low_stock_items.
 * Chart cards: products_chart (Top Products by Revenue), category_chart
 * (Sales by Category).
 * Insight cards: top_product.
 *
 * The "Low Stock Alert" banner (using $this->lowStockProducts) is rendered
 * directly in the view — it isn't part of the card-preference system.
 */
class Products extends DashboardPageComponent
{
    public function pageKey(): string
    {
        return 'products';
    }

    #[Computed]
    public function availableMetricCards(): array
    {
        return Arr::only($this->allMetricDefinitions(), ['total_products', 'low_stock_items']);
    }

    #[Computed]
    public function availableChartCards(): array
    {
        return Arr::only($this->allChartDefinitions(), ['products_chart', 'category_chart']);
    }

    #[Computed]
    public function availableInsightCards(): array
    {
        return Arr::only($this->allInsightDefinitions(), ['top_product']);
    }

    public function render()
    {
        return view('livewire.dashboard.products')->layout('components.layouts.app', [
            'title' => __('Products'),
        ]);
    }
}
