<?php

namespace App\Livewire\Admin\Permissions;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Create extends Component
{
    public function mount(): void
    {
        Gate::authorize('manage permissions');
    }

    public $name = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function save(): void
    {
        $this->validate();

        Permission::create([
            'name' => $this->name,
            'guard_name' => 'web',
        ]);

        session()->flash('message', __('Permission created successfully.'));

        $this->redirect(route('admin.permissions.index'));
    }

    public function render()
    {
        return view('livewire.admin.permissions.create')
            ->layout('components.layouts.app', [
                'title' => __('Create Permission'),
            ]);
    }
}
