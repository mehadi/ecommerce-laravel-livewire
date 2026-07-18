<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Social extends Component
{
    public string $social_facebook = '';

    public string $social_instagram = '';

    public string $social_twitter = '';

    public string $social_linkedin = '';

    public string $social_youtube = '';

    public string $social_tiktok = '';

    public string $social_pinterest = '';

    public string $social_whatsapp = '';

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_linkedin',
            'social_youtube',
            'social_tiktok',
            'social_pinterest',
            'social_whatsapp',
        ]);

        $this->social_facebook = $settings['social_facebook'] ?? '';
        $this->social_instagram = $settings['social_instagram'] ?? '';
        $this->social_twitter = $settings['social_twitter'] ?? '';
        $this->social_linkedin = $settings['social_linkedin'] ?? '';
        $this->social_youtube = $settings['social_youtube'] ?? '';
        $this->social_tiktok = $settings['social_tiktok'] ?? '';
        $this->social_pinterest = $settings['social_pinterest'] ?? '';
        $this->social_whatsapp = $settings['social_whatsapp'] ?? '';
    }

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

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.website-defaults.social')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
