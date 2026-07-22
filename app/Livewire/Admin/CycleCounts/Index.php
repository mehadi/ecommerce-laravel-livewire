<?php

namespace App\Livewire\Admin\CycleCounts;

use App\Models\Category;
use App\Models\CycleCount;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filterStatus = '';

    public $perPage = 15;

    protected $queryString = ['filterStatus', 'perPage'];

    // Create modal
    public $showCreateModal = false;

    public $warehouse_id = null;

    public $scope = 'all';

    public $filterCategoryId = null;

    public $filterAbcClass = null;

    public $notes = '';

    protected function rules(): array
    {
        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'scope' => 'required|in:all,category,abc_class',
            'filterCategoryId' => 'required_if:scope,category|nullable|exists:categories,id',
            'filterAbcClass' => 'required_if:scope,abc_class|nullable|in:A,B,C',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('view cycle counts');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        Gate::authorize('create cycle counts');

        $this->reset(['warehouse_id', 'scope', 'filterCategoryId', 'filterAbcClass', 'notes']);
        $this->scope = 'all';
        $this->warehouse_id = Warehouse::default()->id;
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createCycleCount(): void
    {
        Gate::authorize('create cycle counts');

        $this->validate();

        $productIds = Product::query()
            ->when($this->scope === 'category', fn ($query) => $query->where('category_id', $this->filterCategoryId))
            ->when($this->scope === 'abc_class', fn ($query) => $query->where('abc_class', $this->filterAbcClass))
            ->pluck('id');

        $warehouseStocks = WarehouseStock::where('warehouse_id', $this->warehouse_id)
            ->whereIn('product_id', $productIds)
            ->get();

        if ($warehouseStocks->isEmpty()) {
            session()->flash('error', __('No stock rows match that scope at the selected warehouse.'));

            return;
        }

        $cycleCount = CycleCount::create([
            'warehouse_id' => $this->warehouse_id,
            'status' => 'pending',
            'scope' => $this->scope,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($warehouseStocks as $warehouseStock) {
            $cycleCount->items()->create([
                'product_id' => $warehouseStock->product_id,
                'product_attribute_id' => $warehouseStock->product_attribute_id,
                'expected_quantity' => $warehouseStock->stock,
            ]);
        }

        session()->flash('message', __('Cycle count created with :count item(s).', ['count' => $warehouseStocks->count()]));
        $this->showCreateModal = false;
    }

    public function render()
    {
        $cycleCounts = CycleCount::query()
            ->with(['warehouse', 'items'])
            ->when($this->filterStatus !== '', fn ($query) => $query->where('status', $this->filterStatus))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.cycle-counts.index', [
            'cycleCounts' => $cycleCounts,
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::orderBy('name_en')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('Cycle Counts'),
        ]);
    }
}
