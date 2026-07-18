<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Contact extends Component
{
    // Contact Information
    public string $contact_email = '';

    public string $contact_phone = '';

    public string $contact_address = '';

    /** Platform-wide defaults, shown as placeholders for fields the store hasn't set. */
    public array $platformDefaults = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $keys = ['contact_email', 'contact_phone', 'contact_address'];

        $settings = Setting::getManyOwn($keys);
        $this->platformDefaults = PlatformSetting::getMany($keys);

        $this->contact_email = $settings['contact_email'] ?? '';
        $this->contact_phone = $settings['contact_phone'] ?? '';
        $this->contact_address = $settings['contact_address'] ?? '';
    }

    /**
     * Update contact information settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string'],
        ]);

        Setting::setMany($validated);

        // Clear relevant caches
        Cache::forget(Tenancy::cacheKey('landing.sections.hero'));
        Cache::forget(Tenancy::cacheKey('landing.sections.features'));
        Cache::forget(Tenancy::cacheKey('landing.sections.faq'));

        session()->flash('message', __('Website settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.contact')
            ->layout('components.layouts.app', [
                'title' => __('Contact Information'),
            ]);
    }
}
