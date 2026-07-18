<?php

namespace App\Observers;

use App\Models\ProductVariation;

class ProductVariationObserver
{
    /**
     * Handle the ProductVariation "deleted" event.
     */
    public function deleted(ProductVariation $productVariation): void
    {
        // When a variation is deleted, its combinations are also deleted (cascade)
        // Sync the product after deletion
        if ($productVariation->product) {
            $productVariation->product->syncPriceAndStock();
        }
    }
}
