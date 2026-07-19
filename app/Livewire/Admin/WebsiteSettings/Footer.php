<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\FooterVariants;
use Livewire\Component;

class Footer extends Component
{
    public string $storefront_footer_variant = FooterVariants::DEFAULT;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->storefront_footer_variant = FooterVariants::resolve(Setting::get('storefront_footer_variant'));
    }

    /**
     * Update the storefront footer style.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'storefront_footer_variant' => ['required', 'in:'.implode(',', FooterVariants::keys())],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Footer settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.footer', [
                'variants' => FooterVariants::all(),
            ])
            ->layout('components.layouts.app', [
                'title' => __('Footer'),
            ]);
    }
}
