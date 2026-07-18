<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Social extends Component
{
    // Social Media Links
    public string $social_facebook = '';

    public string $social_instagram = '';

    public string $social_twitter = '';

    public string $social_linkedin = '';

    public string $social_youtube = '';

    // Additional Social Media
    public string $social_tiktok = '';

    public string $social_pinterest = '';

    public string $social_whatsapp = '';

    /** Platform-wide defaults, shown as placeholders for fields the store hasn't set. */
    public array $platformDefaults = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $keys = [
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_linkedin',
            'social_youtube',
            'social_tiktok',
            'social_pinterest',
            'social_whatsapp',
        ];

        $settings = Setting::getManyOwn($keys);
        $this->platformDefaults = PlatformSetting::getMany($keys);

        $this->social_facebook = $settings['social_facebook'] ?? '';
        $this->social_instagram = $settings['social_instagram'] ?? '';
        $this->social_twitter = $settings['social_twitter'] ?? '';
        $this->social_linkedin = $settings['social_linkedin'] ?? '';
        $this->social_youtube = $settings['social_youtube'] ?? '';
        $this->social_tiktok = $settings['social_tiktok'] ?? '';
        $this->social_pinterest = $settings['social_pinterest'] ?? '';
        $this->social_whatsapp = $settings['social_whatsapp'] ?? '';
    }

    /**
     * Update social media settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_pinterest' => ['nullable', 'url', 'max:255'],
            'social_whatsapp' => ['nullable', 'string', 'max:255'],
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
        return view('livewire.admin.website-settings.social')
            ->layout('components.layouts.app', [
                'title' => __('Social Media'),
            ]);
    }
}
