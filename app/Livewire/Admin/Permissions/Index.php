<?php

namespace App\Livewire\Admin\Permissions;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('manage permissions');
    }

    public $search = '';

    public $perPage = 10;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $showModal = false;

    public $editingId = null;

    public $name = '';

    protected $queryString = ['search', 'perPage', 'sortField', 'sortDirection'];

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

    public function deletePermission($permissionId): void
    {
        $permission = Permission::findOrFail($permissionId);
        $permission->delete();
        session()->flash('message', __('Permission deleted successfully.'));
    }

    public function delete(Permission $permission): void
    {
        $this->deletePermission($permission->id);
    }

    public function createPermission(): void
    {
        $this->reset(['editingId', 'name']);
        $this->showModal = true;
    }

    public function editPermission(Permission $permission): void
    {
        $this->editingId = $permission->id;
        $this->name = $permission->name;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name']);
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('permissions', 'name')->ignore($this->editingId),
            ],
        ];
    }

    public function savePermission(): void
    {
        $this->validate();

        if ($this->editingId) {
            $permission = Permission::findOrFail($this->editingId);
            $permission->update([
                'name' => $this->name,
            ]);
            session()->flash('message', __('Permission updated successfully.'));
        } else {
            Permission::create([
                'name' => $this->name,
                'guard_name' => 'web',
            ]);
            session()->flash('message', __('Permission created successfully.'));
        }

        $this->closeModal();
    }

    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->withCount('roles')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Permission::count(),
            'total_roles' => \Spatie\Permission\Models\Role::count(),
            'assigned_permissions' => Permission::has('roles')->count(),
            'unassigned_permissions' => Permission::doesntHave('roles')->count(),
        ];

        return view('livewire.admin.permissions.index', [
            'permissions' => $permissions,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Permissions'),
        ]);
    }
}
