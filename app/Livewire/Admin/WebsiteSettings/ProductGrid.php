<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\ProductGridVariants;
use Livewire\Component;

class ProductGrid extends Component
{
    public string $storefront_shop_grid_variant = ProductGridVariants::DEFAULT;

    public string $storefront_featured_grid_variant = ProductGridVariants::DEFAULT;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->storefront_shop_grid_variant = ProductGridVariants::resolve(Setting::get('storefront_shop_grid_variant'));
        $this->storefront_featured_grid_variant = ProductGridVariants::resolve(Setting::get('storefront_featured_grid_variant'));
    }

    /**
     * Update the shop/category and featured-products grid styles.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'storefront_shop_grid_variant' => ['required', 'in:'.implode(',', ProductGridVariants::keys())],
            'storefront_featured_grid_variant' => ['required', 'in:'.implode(',', ProductGridVariants::keys())],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Product grid settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.product-grid', [
                'variants' => ProductGridVariants::all(),
            ])
            ->layout('components.layouts.app', [
                'title' => __('Product Grid'),
            ]);
    }
}
