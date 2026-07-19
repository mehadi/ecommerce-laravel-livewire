<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\HeroVariants;
use Livewire\Component;

class Hero extends Component
{
    public string $storefront_hero_variant = HeroVariants::DEFAULT;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->storefront_hero_variant = HeroVariants::resolve(Setting::get('storefront_hero_variant'));
    }

    /**
     * Update the storefront hero style.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'storefront_hero_variant' => ['required', 'in:'.implode(',', HeroVariants::keys())],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Hero style updated successfully.'));
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
