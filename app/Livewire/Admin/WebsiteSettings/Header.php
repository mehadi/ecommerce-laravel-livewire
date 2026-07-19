<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\NavbarVariants;
use Livewire\Component;

class Header extends Component
{
    public string $storefront_header_variant = NavbarVariants::DEFAULT;

    public function mount(): void
    {
        $this->storefront_header_variant = NavbarVariants::resolve(Setting::get('storefront_header_variant'));
    }

    public function update(): void
    {
        $validated = $this->validate([
            'storefront_header_variant' => ['required', 'in:'.implode(',', NavbarVariants::keys())],
        ]);

        Setting::setMany($validated);
        session()->flash('message', __('Header settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.header', ['variants' => NavbarVariants::all()])
            ->layout('components.layouts.app', ['title' => __('Header')]);
    }
}
