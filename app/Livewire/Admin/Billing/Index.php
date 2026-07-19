<?php

namespace App\Livewire\Admin\Billing;

use App\Models\Plan;
use App\Models\Product;
use App\Models\User;
use App\Notifications\UpgradeRequestSubmitted;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Index extends Component
{
    public ?int $desiredPlanId = null;

    public function mount(): void
    {
        Gate::authorize('access admin');
    }

    public function requestUpgrade(): void
    {
        $tenant = Tenancy::current();

        $validated = $this->validate([
            'desiredPlanId' => ['required', 'exists:plans,id'],
        ], [], ['desiredPlanId' => __('desired plan')]);

        $tenant->update([
            'upgrade_requested_at' => now(),
            'desired_plan_id' => $validated['desiredPlanId'],
        ]);

        Notification::send(
            User::whereNull('tenant_id')->get(),
            new UpgradeRequestSubmitted($tenant->fresh())
        );

        session()->flash('message', __('Upgrade request sent — our team will follow up shortly.'));
    }

    public function render()
    {
        $tenant = Tenancy::current();

        $plans = Plan::where('id', '!=', $tenant->plan_id)->orderBy('sort_order')->get();

        $usage = [
            'products' => [
                'used' => Product::where('tenant_id', $tenant->id)->count(),
                'limit' => $tenant->plan?->max_products,
            ],
            'admin_users' => [
                'used' => User::where('tenant_id', $tenant->id)->count(),
                'limit' => $tenant->plan?->max_admin_users,
            ],
            'custom_domains' => [
                'used' => $tenant->domains()->count(),
                'limit' => $tenant->plan?->max_custom_domains,
            ],
        ];

        return view('livewire.admin.billing.index', [
            'tenant' => $tenant,
            'usage' => $usage,
            'plans' => $plans,
        ])->layout('components.layouts.app', [
            'title' => __('Billing'),
        ]);
    }
}
