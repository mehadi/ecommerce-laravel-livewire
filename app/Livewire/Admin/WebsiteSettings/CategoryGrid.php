<?php

namespace App\Livewire\Admin\WebsiteSettings;

use App\Models\Setting;
use App\Support\CategoryGridVariants;
use Livewire\Component;

class CategoryGrid extends Component
{
    public string $storefront_category_grid_variant = CategoryGridVariants::DEFAULT;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->storefront_category_grid_variant = CategoryGridVariants::resolve(Setting::get('storefront_category_grid_variant'));
    }

    /**
     * Update the categories-page listing style.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'storefront_category_grid_variant' => ['required', 'in:'.implode(',', CategoryGridVariants::keys())],
        ]);

        Setting::setMany($validated);

        session()->flash('message', __('Category grid settings updated successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.website-settings.category-grid', [
                'variants' => CategoryGridVariants::all(),
            ])
            ->layout('components.layouts.app', [
                'title' => __('Category Grid'),
            ]);
    }
}
