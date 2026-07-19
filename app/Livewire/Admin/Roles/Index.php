<?php

namespace App\Livewire\Admin\Roles;

use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('manage roles');
    }

    public $search = '';

    public $perPage = 10;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $showModal = false;

    public $editingId = null;

    public $name = '';

    public $editingIsSuperAdmin = false;

    /**
     * @var array<int, string>
     */
    public $selectedPermissions = [];

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

    public function deleteRole($roleId): void
    {
        $role = Role::findOrFail($roleId);
        abort_unless($role->tenant_id === null || $role->tenant_id === Tenancy::id(), 404);

        // Prevent deleting super admin role
        if ($role->name === 'super admin') {
            session()->flash('error', __('Super Admin role cannot be deleted.'));

            return;
        }

        $role->delete();
        session()->flash('message', __('Role deleted successfully.'));
    }

    public function delete(Role $role): void
    {
        $this->deleteRole($role->id);
    }

    public function createRole(): void
    {
        $this->reset(['editingId', 'name', 'selectedPermissions', 'editingIsSuperAdmin']);
        $this->showModal = true;
    }

    public function editRole(Role $role): void
    {
        abort_unless($role->tenant_id === null || $role->tenant_id === Tenancy::id(), 404);

        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->editingIsSuperAdmin = $role->name === 'super admin';
        $this->selectedPermissions = $role->permissions
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name', 'selectedPermissions', 'editingIsSuperAdmin']);
    }

    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = \Spatie\Permission\Models\Permission::pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    public function clearAllPermissions(): void
    {
        $this->selectedPermissions = [];
    }

    public function toggleGroupPermissions(array $groupIds): void
    {
        $groupIds = array_map('strval', $groupIds);
        $selected = $this->selectedPermissions;
        $intersection = array_intersect($selected, $groupIds);

        if (count($intersection) === count($groupIds)) {
            $this->selectedPermissions = array_values(array_diff($selected, $groupIds));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($selected, $groupIds)));
        }
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('roles', 'name')->ignore($this->editingId),
            ],
            'selectedPermissions' => ['array'],
        ];
    }

    public function saveRole(): void
    {
        $this->validate();

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            abort_unless($role->tenant_id === null || $role->tenant_id === Tenancy::id(), 404);

            // Prevent renaming the super admin role — Spatie matches it by name
            // (hasRole('super admin')), so renaming it would silently break every
            // super-admin check in the app.
            if ($role->name === 'super admin' && $this->name !== 'super admin') {
                session()->flash('error', __('The Super Admin role name is protected and cannot be changed.'));

                return;
            }

            $role->update([
                'name' => $this->name,
            ]);

            $permissionIds = array_map('intval', $this->selectedPermissions);
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);

            session()->flash('message', __('Role updated successfully.'));
        } else {
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web',
            ]);

            if (! empty($this->selectedPermissions)) {
                $permissionIds = array_map('intval', $this->selectedPermissions);
                $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();
                $role->syncPermissions($permissions);
            }

            session()->flash('message', __('Role created successfully.'));
        }

        $this->closeModal();
    }

    public function render()
    {
        $roles = Tenancy::roleQuery()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->withCount('permissions')
            ->withCount('users')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Tenancy::roleQuery()->count(),
            'total_permissions' => \Spatie\Permission\Models\Permission::count(),
            'total_users' => \App\Models\User::query()->where('tenant_id', Tenancy::id())->count(),
            'roles_with_users' => Tenancy::roleQuery()->has('users')->count(),
        ];

        $allPermissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();

        // Group permissions by resource type
        $groupedPermissions = $allPermissions->groupBy(function ($permission) {
            // Extract the resource name from permission (e.g., "view products" -> "products")
            $parts = explode(' ', $permission->name);
            if (count($parts) > 1) {
                return $parts[1]; // Return the resource name
            }

            return 'other'; // For permissions like "manage roles"
        });

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
            'stats' => $stats,
            'groupedPermissions' => $groupedPermissions,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Roles'),
        ]);
    }
}
