<?php

namespace App\Livewire\Admin\Warehouses;

use App\Models\CycleCount;
use App\Models\PosRegister;
use App\Models\ProductBatch;
use App\Models\PurchaseOrder;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $showModal = false;

    public $editingId = null;

    public $search = '';

    public $filterStatus = '';

    public $perPage = 15;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $name = '';

    public $code = '';

    public $address = '';

    public $city = '';

    public $phone = '';

    public $is_active = true;

    protected $queryString = ['search', 'filterStatus', 'perPage', 'sortField', 'sortDirection'];

    protected function rules(): array
    {
        $tenantId = Tenancy::id();

        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50',
                Rule::unique('warehouses', 'code')
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
                    ->ignore($this->editingId),
            ],
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function createWarehouse(): void
    {
        Gate::authorize('create warehouses');

        $this->reset(['editingId', 'name', 'code', 'address', 'city', 'phone', 'is_active']);
        $this->showModal = true;
    }

    public function editWarehouse(Warehouse $warehouse): void
    {
        $this->editingId = $warehouse->id;
        $this->name = $warehouse->name;
        $this->code = $warehouse->code;
        $this->address = $warehouse->address;
        $this->city = $warehouse->city;
        $this->phone = $warehouse->phone;
        $this->is_active = $warehouse->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function storeWarehouse(): void
    {
        Gate::authorize('create warehouses');

        $this->validate();

        Warehouse::create([
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Warehouse created successfully.'));
        $this->showModal = false;
    }

    public function updateWarehouse(): void
    {
        Gate::authorize('edit warehouses');

        $this->validate();

        Warehouse::findOrFail($this->editingId)->update([
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Warehouse updated successfully.'));
        $this->showModal = false;
    }

    public function setDefault($warehouseId): void
    {
        Gate::authorize('edit warehouses');

        $warehouse = Warehouse::findOrFail($warehouseId);

        Warehouse::where('is_default', true)->update(['is_default' => false]);
        $warehouse->update(['is_default' => true]);

        session()->flash('message', __(':name is now the default warehouse.', ['name' => $warehouse->name]));
    }

    public function deleteWarehouse($warehouseId): void
    {
        Gate::authorize('delete warehouses');

        $warehouse = Warehouse::findOrFail($warehouseId);

        if ($warehouse->is_default) {
            session()->flash('error', __('Cannot delete the default warehouse. Set another warehouse as default first.'));

            return;
        }

        if ($warehouse->warehouseStocks()->where('stock', '>', 0)->exists()) {
            session()->flash('error', __('Cannot delete a warehouse that still holds stock. Transfer or clear its stock first.'));

            return;
        }

        if ($blocker = $this->warehouseDeletionBlocker($warehouse)) {
            session()->flash('error', $blocker);

            return;
        }

        $warehouse->delete();
        session()->flash('message', __('Warehouse deleted successfully.'));
    }

    /**
     * Every stock-related module keeps a plain (non-cascading) FK to
     * warehouses, so deleting one while it's still referenced would raise a
     * raw RESTRICT QueryException — except pos_registers, which cascades
     * and would silently wipe out its registers instead. Checking each table
     * up front turns both failure modes into a friendly, specific message.
     */
    protected function warehouseDeletionBlocker(Warehouse $warehouse): ?string
    {
        if (StockTransfer::where(function ($query) use ($warehouse) {
            $query->where('from_warehouse_id', $warehouse->id)
                ->orWhere('to_warehouse_id', $warehouse->id);
        })->exists()) {
            return __('Cannot delete :name — it is still referenced by stock transfers.', ['name' => $warehouse->name]);
        }

        if (PurchaseOrder::where('warehouse_id', $warehouse->id)->exists()) {
            return __('Cannot delete :name — it is still referenced by purchase orders.', ['name' => $warehouse->name]);
        }

        if (CycleCount::where('warehouse_id', $warehouse->id)->exists()) {
            return __('Cannot delete :name — it is still referenced by cycle counts.', ['name' => $warehouse->name]);
        }

        if (ProductBatch::where('warehouse_id', $warehouse->id)->exists()) {
            return __('Cannot delete :name — it is still referenced by product batches.', ['name' => $warehouse->name]);
        }

        if (PosRegister::where('warehouse_id', $warehouse->id)->exists()) {
            return __('Cannot delete :name — it still has POS registers assigned to it. Reassign or remove them first.', ['name' => $warehouse->name]);
        }

        return null;
    }

    protected function getWarehousesQuery()
    {
        return Warehouse::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%')
                        ->orWhere('city', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            });
    }

    public function render()
    {
        $warehouses = $this->getWarehousesQuery()
            ->withCount('warehouseStocks')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Warehouse::count(),
            'active' => Warehouse::where('is_active', true)->count(),
            'inactive' => Warehouse::where('is_active', false)->count(),
        ];

        return view('livewire.admin.warehouses.index', [
            'warehouses' => $warehouses,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Warehouses'),
        ]);
    }
}
