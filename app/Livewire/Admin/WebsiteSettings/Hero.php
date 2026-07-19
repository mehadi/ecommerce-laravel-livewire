<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\HeroVariants;
use Livewire\Component;

class Hero extends Component
{
    public string $storefront_hero_variant = HeroVariants::DEFAULT;

    public ?string $hero_badge_text = null;

    public ?string $hero_primary_cta_label = null;

    public ?string $hero_primary_cta_url = null;

    public ?string $hero_secondary_cta_label = null;

    public ?string $hero_secondary_cta_url = null;

    public bool $hero_show_stats = true;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->storefront_hero_variant = HeroVariants::resolve(Setting::get('storefront_hero_variant'));

        // Raw tenant values (no platform fallback) so an inherited default isn't
        // shown as — and re-saved as — an explicit tenant override. Empty fields
        // fall back to the stock labels/links on the storefront.
        $settings = Setting::getManyOwn([
            'hero_badge_text',
            'hero_primary_cta_label',
            'hero_primary_cta_url',
            'hero_secondary_cta_label',
            'hero_secondary_cta_url',
            'hero_show_stats',
        ]);

        $this->hero_badge_text = $settings['hero_badge_text'];
        $this->hero_primary_cta_label = $settings['hero_primary_cta_label'];
        $this->hero_primary_cta_url = $settings['hero_primary_cta_url'];
        $this->hero_secondary_cta_label = $settings['hero_secondary_cta_label'];
        $this->hero_secondary_cta_url = $settings['hero_secondary_cta_url'];
        $this->hero_show_stats = ($settings['hero_show_stats'] ?? '1') !== '0';
    }

    /**
     * Update the storefront hero style and content.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'storefront_hero_variant' => ['required', 'in:'.implode(',', HeroVariants::keys())],
            'hero_badge_text' => ['nullable', 'string', 'max:80'],
            'hero_primary_cta_label' => ['nullable', 'string', 'max:40'],
            'hero_primary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_label' => ['nullable', 'string', 'max:40'],
            'hero_secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_show_stats' => ['boolean'],
        ]);

        $validated['hero_show_stats'] = $validated['hero_show_stats'] ? '1' : '0';

        Setting::setMany($validated);

        session()->flash('message', __('Hero settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.hero', [
                'variants' => HeroVariants::all(),
            ])
            ->layout('components.layouts.app', [
                'title' => __('Hero Section'),
            ]);
    }
}
