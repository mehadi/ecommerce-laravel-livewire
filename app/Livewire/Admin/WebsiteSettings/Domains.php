<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Console\Commands\VerifyTenantDomains;
use App\Support\Tenancy;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Domains extends Component
{
    public string $newDomain = '';

    /**
     * Add a new custom domain for the current tenant.
     */
    public function addDomain(): void
    {
        if (! Tenancy::current()?->canAddCustomDomain()) {
            session()->flash('error', __('Your plan does not include custom domains. Upgrade your plan to add one.'));

            return;
        }

        $validated = $this->validate([
            'newDomain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)+$/i',
                Rule::unique('domains', 'domain'),
                // Reject the platform's own central domain(s) and the "{slug}.{central}"
                // subdomain namespace reserved for tenant slugs — either would let this
                // tenant hijack the platform frontend or another tenant's free subdomain
                // (see App\Http\Middleware\ResolveTenant's matching guard).
                function (string $attribute, string $value, \Closure $fail): void {
                    $value = strtolower($value);

                    foreach (config('tenancy.central_domains', []) as $central) {
                        if ($value === $central || str_ends_with($value, '.'.$central)) {
                            $fail(__('This domain is reserved by the platform and cannot be used as a custom domain.'));

                            return;
                        }
                    }
                },
            ],
        ]);

        Tenancy::current()->domains()->create([
            'domain' => strtolower($validated['newDomain']),
            'verified_at' => null,
        ]);

        $this->reset('newDomain');

        session()->flash('message', __('Custom domain added. Point a CNAME record at :target to verify it.', [
            'target' => config('tenancy.domain_verification_target') ?? '—',
        ]));
    }

    /**
     * Re-run the DNS verification check for a single domain belonging to the current tenant.
     */
    public function recheck(int $domainId): void
    {
        $domain = Tenancy::current()->domains()->findOrFail($domainId);

        $target = config('tenancy.domain_verification_target');

        if (! $target) {
            session()->flash('message', __('Domain verification target is not configured yet. Contact support to enable custom domains.'));

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
     * Remove a custom domain belonging to the current tenant.
     */
    public function delete(int $domainId): void
    {
        $domain = Tenancy::current()->domains()->findOrFail($domainId);
        $domain->delete();

        session()->flash('message', __('Custom domain removed.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.domains', [
            'domains' => Tenancy::current()->domains()->latest()->get(),
            'verificationTarget' => config('tenancy.domain_verification_target'),
        ])->layout('components.layouts.app', [
            'title' => __('Custom Domains'),
        ]);
    }
}
