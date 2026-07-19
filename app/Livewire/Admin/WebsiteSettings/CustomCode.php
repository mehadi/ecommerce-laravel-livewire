<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CustomCode extends Component
{
    public string $custom_header_code = '';

    public string $custom_footer_code = '';

    /**
     * Mount the component. Restricted to super admin (not admin/manager, who
     * otherwise pass the blanket 'access admin' gate) because this field
     * executes raw, unescaped HTML/JS on every storefront page — a strictly
     * larger blast radius than any other website setting, including the
     * ability for a lower-trust staff account to ride a super admin's
     * session when they browse the storefront (same-origin, SESSION_DOMAIN
     * is unscoped).
     */
    public function mount(): void
    {
        Gate::authorize('access admin');
        abort_unless(auth()->user()->hasRole('super admin'), 403);

        $settings = Setting::getManyOwn(['custom_header_code', 'custom_footer_code']);

        $this->custom_header_code = $settings['custom_header_code'] ?? '';
        $this->custom_footer_code = $settings['custom_footer_code'] ?? '';
    }

    /**
     * Update custom code settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'custom_header_code' => ['nullable', 'string', 'max:20000'],
            'custom_footer_code' => ['nullable', 'string', 'max:20000'],
        ]);

        // No schema-backed audit trail exists for tenant settings changes, so this
        // is the only forensic record of who injected what code and when — keep it
        // even though it duplicates the Setting row, since Setting::setMany() only
        // stores current state and overwrites history on every save.
        Log::warning('storefront.custom_code.updated', [
            'tenant_id' => Tenancy::id(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'custom_header_code' => $validated['custom_header_code'],
            'custom_footer_code' => $validated['custom_footer_code'],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Website settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.custom-code')
            ->layout('components.layouts.app', [
                'title' => __('Custom Code'),
            ]);
    }
}
