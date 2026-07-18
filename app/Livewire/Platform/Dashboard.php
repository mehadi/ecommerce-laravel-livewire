<?php

namespace App\Livewire\Platform;

use App\Models\Tenant;
use App\Models\TenantBillingEvent;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        Gate::authorize('access platform');
    }

    public function render()
    {
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'trial' => Tenant::where('trial_ends_at', '>', now())->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
            'upgrade_requests' => Tenant::whereNotNull('upgrade_requested_at')->count(),
        ];

        $recentEvents = TenantBillingEvent::with(['tenant', 'recordedBy'])->latest()->limit(10)->get();

        return view('livewire.platform.dashboard', [
            'stats' => $stats,
            'recentEvents' => $recentEvents,
        ])->layout('components.layouts.app', [
            'title' => __('Platform Dashboard'),
        ]);
    }
}
