<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

class ProductPage extends LandingPage
{
    /**
     * Overrides LandingPage::mount(); the untyped $product parameter receives
     * the {product} route segment while staying signature-compatible.
     */
    public function mount($product = null): void
    {
        $this->productId = Product::where('is_active', true)
            ->findOrFail((int) $product)
            ->id;
    }

    #[Computed]
    public function relatedProducts()
    {
        $query = Product::where('is_active', true)
            ->where('id', '!=', $this->productId)
            ->with(['category', 'productAttributes']);

        $related = (clone $query)
            ->when($this->product?->category_id, fn ($q) => $q->where('category_id', $this->product->category_id))
            ->orderByDesc('is_featured')
            ->orderBy('order')
            ->limit(3)
            ->get();

        if ($related->count() < 3) {
            $related = $related->concat(
                (clone $query)
                    ->whereNotIn('id', $related->pluck('id')->push($this->productId))
                    ->orderByDesc('is_featured')
                    ->orderBy('order')
                    ->limit(3 - $related->count())
                    ->get()
            );
        }

        return $related;
    }

    public function render()
    {
        return view('livewire.product-page')
            ->layout('components.layouts.public', [
                'title' => $this->product->name.' - '.$this->siteName,
                'metaDescription' => Str::limit($this->product->description ?? '', 160),
                'showNavigation' => true,
                'showFooter' => true,
                'showCookieConsent' => true,
            ]);
    }
}
