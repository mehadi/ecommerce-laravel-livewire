<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Analytics extends Component
{
    public string $facebook_pixel_id = '';

    public string $google_analytics_id = '';

    public string $google_tag_manager_id = '';

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'facebook_pixel_id',
            'google_analytics_id',
            'google_tag_manager_id',
        ]);

        $this->facebook_pixel_id = $settings['facebook_pixel_id'] ?? '';
        $this->google_analytics_id = $settings['google_analytics_id'] ?? '';
        $this->google_tag_manager_id = $settings['google_tag_manager_id'] ?? '';
    }

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

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.website-defaults.analytics')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
