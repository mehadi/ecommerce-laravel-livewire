<?php

namespace App\Observers;

use App\Models\ProductVariationCombination;

class ProductVariationCombinationObserver
{
    /**
     * Handle the ProductVariationCombination "created" event.
     */
    public function created(ProductVariationCombination $productVariationCombination): void
    {
        $this->syncProduct($productVariationCombination);
    }

    /**
     * Handle the ProductVariationCombination "updated" event.
     */
    public function updated(ProductVariationCombination $productVariationCombination): void
    {
        $this->syncProduct($productVariationCombination);
    }

    /**
     * Handle the ProductVariationCombination "deleted" event.
     */
    public function deleted(ProductVariationCombination $productVariationCombination): void
    {
        $this->syncProduct($productVariationCombination);
    }

    /**
     * Sync product price and stock from variations.
     */
    private function syncProduct(ProductVariationCombination $productVariationCombination): void
    {
        if ($productVariationCombination->product) {
            $productVariationCombination->product->syncPriceAndStock();
        }
    }
}
