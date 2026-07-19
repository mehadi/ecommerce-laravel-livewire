<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\ProductDetailsVariants;
use Livewire\Component;

class ProductDetails extends Component
{
    public string $storefront_product_details_variant = ProductDetailsVariants::DEFAULT;

    public function mount(): void
    {
        $this->storefront_product_details_variant = ProductDetailsVariants::resolve(Setting::get('storefront_product_details_variant'));
    }

    public function update(): void
    {
        $validated = $this->validate([
            'storefront_product_details_variant' => ['required', 'in:'.implode(',', ProductDetailsVariants::keys())],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Product details settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.product-details', [
            'variants' => ProductDetailsVariants::all(),
        ])
            ->layout('components.layouts.app', [
                'title' => __('Product Details Page'),
            ]);
    }
}
