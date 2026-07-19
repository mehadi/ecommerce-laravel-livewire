<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Dashboard\Concerns\HasDashboardAnalytics;
use App\Livewire\Dashboard\Concerns\HasDashboardFilters;
use App\Livewire\Dashboard\Concerns\HasReportingAnalytics;
use App\Support\Tenancy;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Product Profitability & Margin report — the highest-priority gap identified
 * in the reporting analysis: buying_price (cost) is tracked on every product
 * but nowhere in the existing dashboard is margin ever surfaced. Gated behind
 * the tenant's plan (advanced_analytics_enabled), per product decision.
 */
class Profitability extends Component
{
    use HasDashboardAnalytics;
    use HasDashboardFilters;
    use HasReportingAnalytics;
    use WithPagination;

    public string $search = '';

    public string $sortField = 'profit';

    public string $sortDirection = 'desc';

    public bool $hasAdvancedAnalytics = false;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateRange' => ['except' => '30'],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->hasAdvancedAnalytics = Tenancy::hasFeature('advanced_analytics_enabled');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $rows = $this->productProfitabilityData;

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $rows = $rows->filter(fn (array $row) => str_contains(mb_strtolower($row['product_name']), $needle));
        }

        $rows = $rows->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc')->values();

        $perPage = 15;
        $page = $this->getPage();

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function render()
    {
        return view('livewire.dashboard.profitability')->layout('components.layouts.app', [
            'title' => __('Product Profitability'),
        ]);
    }
}
