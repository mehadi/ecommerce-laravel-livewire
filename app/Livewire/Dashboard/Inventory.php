<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Dashboard\Concerns\HasDashboardAnalytics;
use App\Livewire\Dashboard\Concerns\HasDashboardFilters;
use App\Livewire\Dashboard\Concerns\HasReportingAnalytics;
use App\Models\Category;
use App\Support\Tenancy;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Inventory Valuation & Reorder report — turns the existing binary "low
 * stock" alert into an actionable view of capital tied up in stock and which
 * products haven't sold at all in the selected period (dead-stock
 * candidates). Gated behind the tenant's plan (advanced_analytics_enabled).
 */
class Inventory extends Component
{
    use HasDashboardAnalytics;
    use HasDashboardFilters;
    use HasReportingAnalytics;
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public string $stockFilter = ''; // '', 'low_stock', 'dead_stock'

    public string $sortField = 'value_at_cost';

    public string $sortDirection = 'desc';

    public bool $hasAdvancedAnalytics = false;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateRange' => ['except' => '30'],
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'stockFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->hasAdvancedAnalytics = Tenancy::hasFeature('advanced_analytics_enabled');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStockFilter(): void
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
    public function categories(): Collection
    {
        return Category::orderBy('name_en')->pluck('name_en')->unique()->values();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $rows = $this->inventoryValuationData;

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $rows = $rows->filter(fn (array $row) => str_contains(mb_strtolower($row['product_name']), $needle));
        }

        if ($this->categoryFilter !== '') {
            $rows = $rows->filter(fn (array $row) => $row['category'] === $this->categoryFilter);
        }

        if ($this->stockFilter === 'low_stock') {
            $rows = $rows->filter(fn (array $row) => $row['is_low_stock']);
        } elseif ($this->stockFilter === 'dead_stock') {
            $rows = $rows->filter(fn (array $row) => $row['is_dead_stock']);
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
        return view('livewire.dashboard.inventory')->layout('components.layouts.app', [
            'title' => __('Inventory Valuation'),
        ]);
    }
}
