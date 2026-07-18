<?php

namespace App\Livewire\Platform\WebsiteDefaults;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Appearance extends Component
{
    public string $frontend_text_size = 'medium';

    public int $frontend_text_size_custom = 100;

    public const TEXT_SIZE_PRESETS = [
        'xs' => 80,
        'sm' => 90,
        'medium' => 100,
        'lg' => 112.5,
        'xl' => 125,
        'xxl' => 137.5,
    ];

    public string $frontend_content_width = 'medium';

    public int $frontend_content_width_custom = 1152;

    public string $theme_primary_color = '#059669';

    public string $theme_secondary_color = '#0f172a';

    public const CONTENT_WIDTH_PRESETS = [
        'narrow' => 960,
        'medium' => 1152,
        'wide' => 1280,
        'xl' => 1440,
        'xxl' => 1600,
        'full' => 1920,
    ];

    public function mount(): void
    {
        Gate::authorize('access platform');

        $settings = PlatformSetting::getMany([
            'frontend_text_size',
            'frontend_text_size_custom',
            'frontend_content_width',
            'frontend_content_width_custom',
            'theme_primary_color',
            'theme_secondary_color',
        ]);

        $this->frontend_text_size = $settings['frontend_text_size'] ?? 'medium';
        $this->frontend_text_size_custom = isset($settings['frontend_text_size_custom'])
            ? (int) $settings['frontend_text_size_custom']
            : 100;
        $this->frontend_content_width = $settings['frontend_content_width'] ?? 'medium';
        $this->frontend_content_width_custom = isset($settings['frontend_content_width_custom'])
            ? (int) $settings['frontend_content_width_custom']
            : 1152;
        $this->theme_primary_color = $settings['theme_primary_color'] ?? '#059669';
        $this->theme_secondary_color = $settings['theme_secondary_color'] ?? '#0f172a';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'frontend_text_size' => ['required', 'in:xs,sm,medium,lg,xl,xxl,custom'],
            'frontend_text_size_custom' => ['required_if:frontend_text_size,custom', 'integer', 'min:50', 'max:200'],
            'frontend_content_width' => ['required', 'in:narrow,medium,wide,xl,xxl,full,custom'],
            'frontend_content_width_custom' => ['required_if:frontend_content_width,custom', 'integer', 'min:960', 'max:1920'],
            'theme_primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        PlatformSetting::setMany($validated);

        session()->flash('message', __('Platform website defaults updated successfully.'));
    }

    public function render()
    {
        return view('livewire.platform.website-defaults.appearance')
            ->layout('components.layouts.app', [
                'title' => __('Website Defaults'),
            ]);
    }
}
