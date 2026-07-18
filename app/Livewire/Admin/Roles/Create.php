<?php

namespace App\Livewire\Admin\Roles;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public function mount(): void
    {
        Gate::authorize('manage roles');
    }

    public $name = '';

    public $selectedPermissions = [];

    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = Permission::pluck('id')->map(fn ($id) => (string) $id)->toArray();
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
            // All selected, deselect all
            $this->selectedPermissions = array_values(array_diff($selected, $groupIds));
        } else {
            // Not all selected, select all
            $this->selectedPermissions = array_values(array_unique(array_merge($selected, $groupIds)));
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'selectedPermissions' => 'array',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function save(): void
    {
        $this->validate();

        $role = Role::create([
            'name' => $this->name,
            'guard_name' => 'web',
        ]);

        if (! empty($this->selectedPermissions)) {
            $permissionIds = array_map('intval', $this->selectedPermissions);
            $permissions = Permission::whereIn('id', $permissionIds)->get();

            $role->syncPermissions($permissions);
        }

        session()->flash('message', __('Role created successfully.'));

        $this->redirect(route('admin.roles.index'));
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get();

        // Group permissions by resource type
        $groupedPermissions = $allPermissions->groupBy(function ($permission) {
            // Extract the resource name from permission (e.g., "view products" -> "products")
            $parts = explode(' ', $permission->name);
            if (count($parts) > 1) {
                return $parts[1]; // Return the resource name
            }

            return 'other'; // For permissions like "manage roles"
        });

        return view('livewire.admin.roles.create', [
            'groupedPermissions' => $groupedPermissions,
        ])->layout('components.layouts.app', [
            'title' => __('Create Role'),
        ]);
    }
}
