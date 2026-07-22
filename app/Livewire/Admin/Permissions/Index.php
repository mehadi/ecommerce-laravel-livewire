<?php

namespace App\Livewire\Admin\Permissions;

use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class Index extends Component
{
    use WithPagination;

    /**
     * Permission names checked via hasPermissionTo()/checkPermissionTo() inside
     * Gate::define() closures in AppServiceProvider::boot(). Spatie resolves
     * those checks by name against the shared `permissions` table, so renaming
     * or deleting one of these rows would silently break the corresponding
     * gate for every tenant. Keep this list in sync with AppServiceProvider.
     *
     * @var array<int, string>
     */
    private const RESERVED_PERMISSION_NAMES = [
        'view products',
        'create products',
        'edit products',
        'delete products',
        'view inventory',
        'adjust stock',
        'manage inventory settings',
        'view users',
        'access pos',
        'process pos sales',
        'apply pos discounts',
        'hold pos sales',
        'void pos sale line',
        'open pos shift',
        'close pos shift',
        'manage cash drawer',
        'void pos sale',
        'process pos refunds',
        'force close pos shift',
        'view pos reports',
        'manage pos registers',
        'manage pos settings',
    ];

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
        // The Permission catalog is shared across every tenant (no tenant_id
        // column), so only a true super admin may mutate it — a tenant-level
        // 'admin' deleting a row here would silently break authorization for
        // every other tenant that relies on it.
        abort_unless(auth()->user()->hasRole('super admin'), 403);

        $permission = Permission::findOrFail($permissionId);

        if (in_array($permission->name, self::RESERVED_PERMISSION_NAMES, true)) {
            session()->flash('error', __('This permission is used directly by the application code and cannot be deleted.'));

            return;
        }

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
        // Only a true super admin may mutate the globally-shared Permission
        // catalog (see deletePermission() for why).
        abort_unless(auth()->user()->hasRole('super admin'), 403);

        $this->validate();

        if ($this->editingId) {
            $permission = Permission::findOrFail($this->editingId);

            if (in_array($permission->name, self::RESERVED_PERMISSION_NAMES, true) && $permission->name !== $this->name) {
                session()->flash('error', __('This permission is used directly by the application code and its name cannot be changed.'));

                return;
            }

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
            'total_roles' => Tenancy::roleQuery()->count(),
            'assigned_permissions' => Permission::has('roles')->count(),
            'unassigned_permissions' => Permission::doesntHave('roles')->count(),
        ];

        return view('livewire.admin.permissions.index', [
            'permissions' => $permissions,
            'stats' => $stats,
            'isSuperAdmin' => auth()->user()->hasRole('super admin'),
        ])->layout('components.layouts.app', [
            'title' => __('Manage Permissions'),
        ]);
    }
}
