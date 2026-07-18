<?php

namespace App\Livewire\Admin\Roles;

use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public Role $role;

    public string $name = '';

    /**
     * @var array<int, string>
     */
    public array $selectedPermissions = [];

    public bool $isSuperAdmin = false;

    public function mount(Role $role): void
    {
        Gate::authorize('manage roles');
        abort_unless($role->tenant_id === null || $role->tenant_id === Tenancy::id(), 404);

        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
        $this->isSuperAdmin = false; // Allow editing all roles including super admin
    }

    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = Permission::pluck('id')
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
                Rule::unique('roles', 'name')->ignore($this->role->id),
            ],
            'selectedPermissions' => ['array'],
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function update(): void
    {
        $this->validate();

        $this->role->update([
            'name' => $this->name,
        ]);

        $permissionIds = array_map('intval', $this->selectedPermissions);
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $this->role->syncPermissions($permissions);

        session()->flash('message', __('Role updated successfully.'));

        $this->redirect(route('admin.roles.index'));
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get();

        $groupedPermissions = $allPermissions->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);

            return count($parts) > 1 ? $parts[1] : 'other';
        });

        return view('livewire.admin.roles.edit', [
            'groupedPermissions' => $groupedPermissions,
        ])->layout('components.layouts.app', [
            'title' => __('Edit Role'),
        ]);
    }
}
