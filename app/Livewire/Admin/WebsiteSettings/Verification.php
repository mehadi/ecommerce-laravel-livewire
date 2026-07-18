<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Verification extends Component
{
    // Site Verification
    public string $google_verification_code = '';

    public string $bing_verification_code = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $settings = Setting::getMany([
            'google_verification_code',
            'bing_verification_code',
        ]);

        $this->google_verification_code = $settings['google_verification_code'] ?? '';
        $this->bing_verification_code = $settings['bing_verification_code'] ?? '';
    }

    /**
     * Update website settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'google_verification_code' => ['nullable', 'string', 'max:255'],
            'bing_verification_code' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::setMany($validated);

        // Clear relevant caches
        Cache::forget('landing.sections.hero');
        Cache::forget('landing.sections.features');
        Cache::forget('landing.sections.faq');

        session()->flash('message', __('Website settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.verification')
            ->layout('components.layouts.app', [
                'title' => __('Site Verification'),
            ]);
    }
}
