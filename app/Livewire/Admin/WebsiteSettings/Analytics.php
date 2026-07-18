<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Analytics extends Component
{
    public string $facebook_pixel_id = '';

    public string $google_analytics_id = '';

    public string $google_tag_manager_id = '';

    /** Platform-wide defaults, shown as placeholders for fields the store hasn't set. */
    public array $platformDefaults = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $keys = ['facebook_pixel_id', 'google_analytics_id', 'google_tag_manager_id'];

        $settings = Setting::getManyOwn($keys);
        $this->platformDefaults = PlatformSetting::getMany($keys);

        $this->facebook_pixel_id = $settings['facebook_pixel_id'] ?? '';
        $this->google_analytics_id = $settings['google_analytics_id'] ?? '';
        $this->google_tag_manager_id = $settings['google_tag_manager_id'] ?? '';
    }

    /**
     * Update analytics & tracking settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'facebook_pixel_id' => ['nullable', 'string', 'max:255', 'regex:/^\d+$/'],
            'google_analytics_id' => ['nullable', 'string', 'max:255', 'regex:/^G-[A-Z0-9]+$|^UA-\d+-\d+$/'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:255', 'regex:/^GTM-[A-Z0-9]+$/'],
        ], [
            'facebook_pixel_id.regex' => __('The Facebook Pixel ID must contain only numbers.'),
            'google_analytics_id.regex' => __('The Google Analytics ID format is invalid. Use format: G-XXXXXXXXXX or UA-XXXXXX-X'),
            'google_tag_manager_id.regex' => __('The Google Tag Manager ID format is invalid. Use format: GTM-XXXXXXX'),
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
        return view('livewire.admin.website-settings.analytics')
            ->layout('components.layouts.app', [
                'title' => __('Analytics & Tracking'),
            ]);
    }
}
