<?php

namespace App\Livewire\Platform\UpgradeRequests;

use App\Livewire\Platform\Concerns\HandlesUpgradeRequests;
use App\Models\Tenant;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use HandlesUpgradeRequests, WithPagination;

    public function mount(): void
    {
        Gate::authorize('access platform');
    }

    public function approve(int $tenantId): void
    {
        Gate::authorize('access platform');

        $tenant = Tenant::whereNotNull('upgrade_requested_at')->findOrFail($tenantId);
        $this->approveTenantUpgrade($tenant);

        session()->flash('message', __('Upgrade approved for :name.', ['name' => $tenant->name]));
    }

    public function reject(int $tenantId): void
    {
        Gate::authorize('access platform');

        $tenant = Tenant::whereNotNull('upgrade_requested_at')->findOrFail($tenantId);
        $this->rejectTenantUpgrade($tenant);

        session()->flash('message', __('Upgrade request rejected for :name.', ['name' => $tenant->name]));
    }

    public function render()
    {
        $requests = Tenant::whereNotNull('upgrade_requested_at')
            ->with(['plan', 'desiredPlan', 'owner'])
            ->orderBy('upgrade_requested_at')
            ->paginate(15);

        return view('livewire.platform.upgrade-requests.index', [
            'requests' => $requests,
        ])->layout('components.layouts.app', [
            'title' => __('Upgrade Requests'),
        ]);
    }
}
