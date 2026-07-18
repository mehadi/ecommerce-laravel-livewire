<?php

namespace App\Livewire\Platform\Tenants;

use App\Console\Commands\VerifyTenantDomains;
use App\Livewire\Platform\Concerns\HandlesUpgradeRequests;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantBillingEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    use HandlesUpgradeRequests;

    public Tenant $tenant;

    public string $name = '';

    public string $slug = '';

    public ?int $plan_id = null;

    public string $status = 'active';

    public ?string $trial_ends_at = null;

    public string $payment_amount = '';

    public string $payment_note = '';

    public string $suspendReason = '';

    public string $newDomain = '';

    public function mount(Tenant $tenant): void
    {
        Gate::authorize('access platform');

        $this->tenant = $tenant;
        $this->name = $tenant->name;
        $this->slug = $tenant->slug;
        $this->plan_id = $tenant->plan_id;
        $this->status = $tenant->status;
        $this->trial_ends_at = $tenant->trial_ends_at?->format('Y-m-d');
    }

    public function updateDetails(): void
    {
        Gate::authorize('access platform');

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:tenants,slug,'.$this->tenant->id],
        ]);

        $this->tenant->update($validated);
        $this->tenant->refresh();

        session()->flash('message', __('Tenant details updated successfully.'));
    }

    /**
     * See Index::deleteTenant() for why users are purged before the tenant
     * itself is deleted (users.tenant_id only nullOnDelete's, which would
     * otherwise leave the owner looking like platform staff).
     */
    public function deleteTenant()
    {
        Gate::authorize('access platform');

        if ($this->tenant->status !== 'cancelled') {
            session()->flash('error', __('Cancel this tenant before deleting it.'));

            return;
        }

        DB::transaction(function () {
            User::where('tenant_id', $this->tenant->id)->delete();
            $this->tenant->delete();
        });

        session()->flash('message', __('Tenant deleted permanently.'));

        return $this->redirect(route('platform.tenants.index'), navigate: true);
    }

    public function updateSubscription(): void
    {
        $validated = $this->validate([
            'plan_id' => ['nullable', 'exists:plans,id'],
            'status' => ['required', 'in:active,suspended,cancelled'],
            'trial_ends_at' => ['nullable', 'date'],
        ]);

        $planChanged = $this->tenant->plan_id !== $validated['plan_id'];
        $statusChanged = $this->tenant->status !== $validated['status'];

        $this->tenant->update($validated);

        if ($planChanged) {
            $this->logEvent('plan_changed', note: 'Plan changed to '.($this->tenant->fresh()->plan?->name ?? 'none').'.');
        }

        if ($statusChanged) {
            $this->logEvent('status_changed', note: 'Status changed to '.$validated['status'].'.');
        }

        $this->tenant->refresh();

        session()->flash('message', __('Subscription updated successfully.'));
    }

    public function approveUpgrade(): void
    {
        Gate::authorize('access platform');
        abort_unless($this->tenant->upgrade_requested_at, 404);

        $this->approveTenantUpgrade($this->tenant);

        $this->plan_id = $this->tenant->plan_id;
        $this->tenant->refresh();

        session()->flash('message', __('Upgrade approved.'));
    }

    public function rejectUpgrade(): void
    {
        Gate::authorize('access platform');
        abort_unless($this->tenant->upgrade_requested_at, 404);

        $this->rejectTenantUpgrade($this->tenant);
        $this->tenant->refresh();

        session()->flash('message', __('Upgrade request rejected.'));
    }

    public function suspend(): void
    {
        Gate::authorize('access platform');

        $validated = $this->validate([
            'suspendReason' => ['required', 'string', 'max:1000'],
        ], [], ['suspendReason' => __('reason')]);

        $this->tenant->update(['status' => 'suspended']);
        $this->logEvent('suspended', note: $validated['suspendReason']);
        $this->reset('suspendReason');
        $this->status = 'suspended';
        $this->tenant->refresh();

        session()->flash('message', __('Tenant suspended.'));
    }

    public function reactivate(): void
    {
        Gate::authorize('access platform');

        $this->tenant->update(['status' => 'active']);
        $this->logEvent('reactivated');
        $this->status = 'active';
        $this->tenant->refresh();

        session()->flash('message', __('Tenant reactivated.'));
    }

    /**
     * Add a custom domain on the tenant's behalf. Unlike the tenant-facing
     * Admin\WebsiteSettings\Domains::addDomain(), this doesn't gate on
     * canAddCustomDomain() — platform staff can exceed the plan's domain
     * limit here since they're acting as an override, not a self-service add.
     */
    public function addDomain(): void
    {
        Gate::authorize('access platform');

        $validated = $this->validate([
            'newDomain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)+$/i',
                Rule::unique('domains', 'domain'),
            ],
        ]);

        $this->tenant->domains()->create([
            'domain' => strtolower($validated['newDomain']),
            'verified_at' => null,
        ]);

        $this->reset('newDomain');

        session()->flash('message', __('Custom domain added. Point a CNAME record at :target to verify it.', [
            'target' => config('tenancy.domain_verification_target') ?? '—',
        ]));
    }

    /**
     * Re-run the DNS verification check for a single domain belonging to this tenant.
     */
    public function recheckDomain(int $domainId): void
    {
        Gate::authorize('access platform');

        $domain = $this->tenant->domains()->findOrFail($domainId);

        $target = config('tenancy.domain_verification_target');

        if (! $target) {
            session()->flash('message', __('Domain verification target is not configured yet.'));

            return;
        }

        if ($domain->verified_at !== null) {
            session()->flash('message', __(':domain is already verified.', ['domain' => $domain->domain]));

            return;
        }

        $verified = app(VerifyTenantDomains::class)->pointsAtPlatform($domain->domain, $target);

        if ($verified) {
            $domain->update(['verified_at' => now()]);
            session()->flash('message', __(':domain is verified.', ['domain' => $domain->domain]));
        } else {
            session()->flash('message', __(':domain is not verified yet. Add a CNAME record pointing to :target and try again.', [
                'domain' => $domain->domain,
                'target' => $target,
            ]));
        }
    }

    /**
     * Remove a custom domain belonging to this tenant.
     */
    public function deleteDomain(int $domainId): void
    {
        Gate::authorize('access platform');

        $this->tenant->domains()->findOrFail($domainId)->delete();

        session()->flash('message', __('Custom domain removed.'));
    }

    public function impersonate()
    {
        Gate::authorize('impersonate tenants');

        abort_if(session()->has('impersonator_id'), 403, 'Nested impersonation is not allowed.');

        $owner = $this->tenant->owner;
        abort_unless($owner, 404, __('This tenant has no owner user to impersonate.'));

        // Session cookies here are host-only (SESSION_DOMAIN=null), so logging
        // in on this (central) domain would never carry over to the tenant's
        // own domain. Instead, hand off via a short-lived signed link that the
        // tenant's own domain verifies and acts on in App\Http\Controllers\
        // ImpersonationController::enter() — that's where the session/audit
        // log for this actually gets created, once we're on the right host.
        $signedPath = URL::temporarySignedRoute(
            'impersonation.enter',
            now()->addMinutes(2),
            ['impersonator' => auth()->id(), 'user' => $owner->id],
            absolute: false
        );

        return $this->redirect($this->tenant->primaryUrl().$signedPath, navigate: false);
    }

    public function recordPayment(): void
    {
        $validated = $this->validate([
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'payment_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->logEvent('payment_recorded', amount: (float) $validated['payment_amount'], note: $validated['payment_note'] ?: null);

        $this->reset('payment_amount', 'payment_note');

        session()->flash('message', __('Payment recorded successfully.'));
    }

    protected function logEvent(string $type, ?float $amount = null, ?string $note = null): void
    {
        TenantBillingEvent::create([
            'tenant_id' => $this->tenant->id,
            'recorded_by' => auth()->id(),
            'type' => $type,
            'amount' => $amount,
            'note' => $note,
        ]);
    }

    public function render()
    {
        $usage = [
            'products' => [
                'used' => Product::where('tenant_id', $this->tenant->id)->count(),
                'limit' => $this->tenant->plan?->max_products,
            ],
            'admin_users' => [
                'used' => User::where('tenant_id', $this->tenant->id)->count(),
                'limit' => $this->tenant->plan?->max_admin_users,
            ],
            'custom_domains' => [
                'used' => $this->tenant->domains()->count(),
                'limit' => $this->tenant->plan?->max_custom_domains,
            ],
        ];

        return view('livewire.platform.tenants.show', [
            'plans' => Plan::orderBy('sort_order')->get(),
            'usage' => $usage,
            'billingEvents' => $this->tenant->billingEvents()->with('recordedBy')->paginate(10),
            'domains' => $this->tenant->domains()->latest()->get(),
            'verificationTarget' => config('tenancy.domain_verification_target'),
        ])->layout('components.layouts.app', [
            'title' => $this->tenant->name,
        ]);
    }
}
