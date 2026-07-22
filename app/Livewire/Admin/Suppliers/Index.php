<?php

namespace App\Livewire\Admin\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;
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

    public $contact_name = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public $lead_time_days = null;

    public $is_active = true;

    protected $queryString = ['search', 'filterStatus', 'perPage', 'sortField', 'sortDirection'];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'lead_time_days' => 'nullable|integer|min:0',
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

    public function createSupplier(): void
    {
        Gate::authorize('create suppliers');

        $this->reset(['editingId', 'name', 'contact_name', 'email', 'phone', 'address', 'lead_time_days', 'is_active']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function editSupplier(Supplier $supplier): void
    {
        $this->editingId = $supplier->id;
        $this->name = $supplier->name;
        $this->contact_name = $supplier->contact_name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->lead_time_days = $supplier->lead_time_days;
        $this->is_active = $supplier->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function storeSupplier(): void
    {
        Gate::authorize('create suppliers');

        $this->validate();

        Supplier::create([
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'lead_time_days' => $this->lead_time_days,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Supplier created successfully.'));
        $this->showModal = false;
    }

    public function updateSupplier(): void
    {
        Gate::authorize('edit suppliers');

        $this->validate();

        Supplier::findOrFail($this->editingId)->update([
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'lead_time_days' => $this->lead_time_days,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Supplier updated successfully.'));
        $this->showModal = false;
    }

    public function deleteSupplier($supplierId): void
    {
        Gate::authorize('delete suppliers');

        $supplier = Supplier::findOrFail($supplierId);

        if ($supplier->products()->exists()) {
            session()->flash('error', __('Cannot delete a supplier that is set as the default supplier for one or more products.'));

            return;
        }

        if ($supplier->purchaseOrders()->exists()) {
            session()->flash('error', __('Cannot delete a supplier that has purchase orders on record.'));

            return;
        }

        $supplier->delete();
        session()->flash('message', __('Supplier deleted successfully.'));
    }

    protected function getSuppliersQuery()
    {
        return Supplier::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('contact_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            });
    }

    public function render()
    {
        $suppliers = $this->getSuppliersQuery()
            ->withCount('purchaseOrders')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('is_active', true)->count(),
            'inactive' => Supplier::where('is_active', false)->count(),
        ];

        return view('livewire.admin.suppliers.index', [
            'suppliers' => $suppliers,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Suppliers'),
        ]);
    }
}
