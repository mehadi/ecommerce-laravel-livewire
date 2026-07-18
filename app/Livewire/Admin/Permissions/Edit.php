<?php

namespace App\Livewire\Admin\Permissions;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Edit extends Component
{
    public Permission $permission;

    public $name = '';

    public function mount(Permission $permission): void
    {
        Gate::authorize('manage permissions');
        $this->permission = $permission;
        $this->name = $permission->name;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name,'.$this->permission->id,
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function update(): void
    {
        $this->validate();

        $this->permission->update([
            'name' => $this->name,
        ]);

        session()->flash('message', __('Permission updated successfully.'));

        $this->redirect(route('admin.permissions.index'));
    }

    public function render()
    {
        return view('livewire.admin.permissions.edit')
            ->layout('components.layouts.app', [
                'title' => __('Edit Permission'),
            ]);
    }
}
