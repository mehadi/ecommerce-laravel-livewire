<?php

namespace App\Livewire\Platform\Tenants;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\PermissionRegistrar;

class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('access platform');
    }

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public bool $showModal = false;

    public string $name = '';

    public string $slug = '';

    public string $owner_name = '';

    public string $owner_email = '';

    public string $owner_password = '';

    public string $owner_password_confirmation = '';

    public $plan_id = '';

    public string $status = 'active';

    public ?string $trial_ends_at = null;

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

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:tenants,slug'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8', 'confirmed'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'status' => ['required', 'in:active,suspended,cancelled'],
            'trial_ends_at' => ['nullable', 'date'],
        ];
    }

    public function updatedName($value): void
    {
        if (! $this->slug) {
            $this->slug = Str::slug($value);
        }
    }

    public function createTenant(): void
    {
        Gate::authorize('access platform');

        $this->reset(['name', 'slug', 'owner_name', 'owner_email', 'owner_password', 'owner_password_confirmation', 'plan_id', 'trial_ends_at']);
        $this->status = 'active';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     * Creates the tenant, its owner user, and — since roles are per-tenant
     * (spatie/laravel-permission teams mode, see Tenancy::roleQuery()) — seeds
     * that tenant's own role set before assigning "admin" to the owner.
     */
    public function storeTenant(): void
    {
        Gate::authorize('access platform');

        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'plan_id' => $validated['plan_id'] ?: null,
                'status' => $validated['status'],
                'trial_ends_at' => $validated['trial_ends_at'] ?: null,
            ]);

            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]);

            $registrar = app(PermissionRegistrar::class);
            $previousTeamId = $registrar->getPermissionsTeamId();

            $registrar->setPermissionsTeamId($tenant->id);
            (new RolesPermissionsSeeder)->run();
            $owner->assignRole('admin');

            $registrar->setPermissionsTeamId($previousTeamId);

            $tenant->update(['owner_user_id' => $owner->id]);
        });

        session()->flash('message', __('Tenant created successfully.'));
        $this->showModal = false;
    }

    /**
     * Deleting a tenant cascades to all of its data (products, orders, domains,
     * etc. all FK-cascade on tenant_id). Users only nullOnDelete their tenant_id
     * though, which would otherwise leave the owner looking like platform staff
     * (Gate::define('access platform') keys off tenant_id === null) — so tenant
     * users are purged explicitly first. Restricted to already-cancelled tenants
     * so this can't be used as a shortcut around suspending/cancelling first.
     */
    public function deleteTenant(int $tenantId): void
    {
        Gate::authorize('access platform');

        $tenant = Tenant::findOrFail($tenantId);

        if ($tenant->status !== 'cancelled') {
            session()->flash('error', __('Cancel this tenant before deleting it — set its status to Cancelled from the tenant page first.'));

            return;
        }

        DB::transaction(function () use ($tenant) {
            User::where('tenant_id', $tenant->id)->delete();
            $tenant->delete();
        });

        session()->flash('message', __('Tenant deleted permanently.'));
    }

    public function render()
    {
        $tenants = Tenant::query()
            ->with('plan')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'trial' => Tenant::where('trial_ends_at', '>', now())->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
        ];

        return view('livewire.platform.tenants.index', [
            'tenants' => $tenants,
            'stats' => $stats,
            'plans' => Plan::orderBy('sort_order')->get(),
        ])->layout('components.layouts.app', [
            'title' => __('Tenants'),
        ]);
    }
}
