<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Appearance extends Component
{
    public string $frontend_text_size = 'medium';

    public int $frontend_text_size_custom = 100;

    /** Named text-size presets mapped to a root font-size percentage. */
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

    /** Named content-width presets mapped to a max-width in pixels. */
    public const CONTENT_WIDTH_PRESETS = [
        'narrow' => 960,
        'medium' => 1152,
        'wide' => 1280,
        'xl' => 1440,
        'xxl' => 1600,
        'full' => 1920,
    ];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $settings = Setting::getMany([
            'frontend_text_size',
            'frontend_text_size_custom',
            'frontend_content_width',
            'frontend_content_width_custom',
        ]);

        $this->frontend_text_size = $settings['frontend_text_size'] ?? 'medium';
        $this->frontend_text_size_custom = isset($settings['frontend_text_size_custom'])
            ? (int) $settings['frontend_text_size_custom']
            : 100;
        $this->frontend_content_width = $settings['frontend_content_width'] ?? 'medium';
        $this->frontend_content_width_custom = isset($settings['frontend_content_width_custom'])
            ? (int) $settings['frontend_content_width_custom']
            : 1152;
    }

    /**
     * Update website settings.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'frontend_text_size' => ['required', 'in:xs,sm,medium,lg,xl,xxl,custom'],
            'frontend_text_size_custom' => ['required_if:frontend_text_size,custom', 'integer', 'min:50', 'max:200'],
            'frontend_content_width' => ['required', 'in:narrow,medium,wide,xl,xxl,full,custom'],
            'frontend_content_width_custom' => ['required_if:frontend_content_width,custom', 'integer', 'min:960', 'max:1920'],
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
        return view('livewire.admin.website-settings.appearance')
            ->layout('components.layouts.app', [
                'title' => __('Appearance'),
            ]);
    }
}
