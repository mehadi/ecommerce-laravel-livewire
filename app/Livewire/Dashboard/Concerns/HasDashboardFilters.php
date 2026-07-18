<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;

/**
 * Date range + status filter state shared by every dashboard sub-page, plus
 * the query-building logic and cache-busting that all of them depend on.
 */
trait HasDashboardFilters
{
    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $statusFilter = '';

    public string $dateRange = '30'; // '7', '30', '90', '365', 'custom'

    public function mountHasDashboardFilters(): void
    {
        if (! $this->startDate && ! $this->endDate) {
            $this->endDate = Carbon::now()->format('Y-m-d');
            $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }
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
}
