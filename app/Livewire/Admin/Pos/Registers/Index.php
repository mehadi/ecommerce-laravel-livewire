<?php

namespace App\Livewire\Admin\Pos\Registers;

use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\Warehouse;
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

    public $perPage = 15;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $name = '';

    public $code = '';

    public $warehouse_id = null;

    public $is_active = true;

    protected $queryString = ['search', 'perPage', 'sortField', 'sortDirection'];

    public function mount(): void
    {
        Gate::authorize('manage pos registers');
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('pos_registers', 'code')->ignore($this->editingId)],
            'warehouse_id' => 'required|exists:warehouses,id',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch(): void
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

    public function createRegister(): void
    {
        $this->reset(['editingId', 'name', 'code', 'warehouse_id', 'is_active']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function editRegister(PosRegister $register): void
    {
        $this->editingId = $register->id;
        $this->name = $register->name;
        $this->code = $register->code;
        $this->warehouse_id = $register->warehouse_id;
        $this->is_active = $register->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function saveRegister(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'warehouse_id' => $this->warehouse_id,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            PosRegister::findOrFail($this->editingId)->update($data);
            session()->flash('message', __('Register updated successfully.'));
        } else {
            PosRegister::create($data);
            session()->flash('message', __('Register created successfully.'));
        }

        $this->showModal = false;
    }

    public function deleteRegister($registerId): void
    {
        $register = PosRegister::findOrFail($registerId);

        if ($register->openShift()) {
            session()->flash('error', __('Cannot delete a register with an open shift. Close it first.'));

            return;
        }

        $register->delete();
        session()->flash('message', __('Register deleted successfully.'));
    }

    protected function getRegistersQuery()
    {
        return PosRegister::query()
            ->with('warehouse')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%');
                });
            });
    }

    public function render()
    {
        $registers = $this->getRegistersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => PosRegister::count(),
            'active' => PosRegister::where('is_active', true)->count(),
            'open_shifts' => PosShift::where('status', 'open')->count(),
        ];

        return view('livewire.admin.pos.registers.index', [
            'registers' => $registers,
            'stats' => $stats,
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('POS Registers'),
        ]);
    }
}
