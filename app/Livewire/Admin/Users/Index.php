<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('view users');
    }

    public $search = '';

    public $perPage = 10;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $filterRole = '';

    public $showModal = false;

    public $editingId = null;

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    /**
     * @var array<int, string>
     */
    public $selectedRoles = [];

    protected $queryString = ['search', 'perPage', 'sortField', 'sortDirection', 'filterRole'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
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

    public function openModal($id = null): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'password_confirmation', 'selectedRoles']);

        if ($id) {
            Gate::authorize('edit users');

            $user = User::where('tenant_id', Tenancy::id())->findOrFail($id);

            // Prevent editing super admin unless current user is super admin
            if ($user->hasRole('super admin') && ! auth()->user()->hasRole('super admin')) {
                session()->flash('error', __('You cannot edit super admin users.'));

                return;
            }

            $this->editingId = $id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selectedRoles = $user->roles->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            Gate::authorize('create users');
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name', 'email', 'password', 'password_confirmation', 'selectedRoles']);
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'selectedRoles' => ['array'],
        ];

        if ($this->editingId) {
            // Password is optional when editing
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } else {
            // Password is required when creating
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function save(): void
    {
        if ($this->editingId) {
            Gate::authorize('edit users');
        } else {
            Gate::authorize('create users');
        }

        if (! $this->editingId && ! Tenancy::current()?->canAddAdminUser()) {
            session()->flash('error', __('Your plan\'s admin user limit has been reached. Upgrade your plan to add more.'));

            return;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'tenant_id' => Tenancy::id(),
        ];

        // Only update password if provided
        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            $user = User::where('tenant_id', Tenancy::id())->findOrFail($this->editingId);

            // Prevent editing super admin unless current user is super admin
            if ($user->hasRole('super admin') && ! auth()->user()->hasRole('super admin')) {
                session()->flash('error', __('You cannot edit super admin users.'));

                return;
            }

            unset($data['tenant_id']); // never move an existing user to a different tenant here

            $user->update($data);

            // Sync roles
            if (! empty($this->selectedRoles)) {
                $roleIds = array_map('intval', $this->selectedRoles);
                $roles = Tenancy::roleQuery()->whereIn('id', $roleIds)->get();

                // Prevent removing super admin role from super admin users
                if ($user->hasRole('super admin') && ! in_array('super admin', $roles->pluck('name')->toArray())) {
                    session()->flash('error', __('Cannot remove super admin role from super admin user.'));

                    return;
                }

                $user->syncRoles($roles);
            } else {
                // Prevent removing all roles from super admin
                if ($user->hasRole('super admin')) {
                    session()->flash('error', __('Cannot remove all roles from super admin user.'));

                    return;
                }

                $user->syncRoles([]);
            }

            session()->flash('message', __('User updated successfully.'));
        } else {
            $user = User::create($data);

            // Assign roles
            if (! empty($this->selectedRoles)) {
                $roleIds = array_map('intval', $this->selectedRoles);
                $roles = Tenancy::roleQuery()->whereIn('id', $roleIds)->get();
                $user->assignRole($roles);
            }

            session()->flash('message', __('User created successfully.'));
        }

        $this->closeModal();
    }

    public function delete($userId): void
    {
        Gate::authorize('delete users');

        $user = User::where('tenant_id', Tenancy::id())->findOrFail($userId);

        // Prevent deleting super admin
        if ($user->hasRole('super admin')) {
            session()->flash('error', __('Super admin users cannot be deleted.'));

            return;
        }

        // Prevent users from deleting themselves
        if ($user->id === auth()->id()) {
            session()->flash('error', __('You cannot delete your own account.'));

            return;
        }

        $user->delete();

        session()->flash('message', __('User deleted successfully.'));
    }

    public function render()
    {
        $users = User::query()
            ->where('tenant_id', Tenancy::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterRole, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('roles.id', $this->filterRole);
                });
            })
            ->with('roles')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $tenantUsers = User::query()->where('tenant_id', Tenancy::id());

        $stats = [
            'total' => (clone $tenantUsers)->count(),
            'verified' => (clone $tenantUsers)->whereNotNull('email_verified_at')->count(),
            'unverified' => (clone $tenantUsers)->whereNull('email_verified_at')->count(),
            'with_roles' => (clone $tenantUsers)->has('roles')->count(),
        ];

        $roles = Tenancy::roleQuery()->orderBy('name')->get();

        return view('livewire.admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'roles' => $roles,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Users'),
        ]);
    }
}
