{{--
    Shared quantity stepper, capped by the selected attribute's stock (or
    the plain product's stock). Reused by every product-details variant.

    Required: $product.
    Optional: $style = 'default' | 'compact' | 'plain' — controls card chrome.
--}}
@php
    $style = $style ?? 'default';
    $wrapClass = match ($style) {
        'plain' => '',
        'compact' => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-5',
        default => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8',
    };

    $maxStock = $product->stock;
    if ($product->hasAttributes() && $this->selectedProductAttributeId) {
        $attr = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
        $maxStock = $attr ? $attr->stock : $product->stock;
    }
@endphp

<div class="{{ $wrapClass }}">
    <label for="product-quantity" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('Quantity') }}</label>
    <div class="inline-flex items-center gap-1 bg-white dark:bg-zinc-900 rounded-full ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] p-1">
        <button wire:click="decrementQuantity" type="button" aria-label="{{ __('Decrease quantity') }}" class="w-11 h-11 rounded-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-bold text-lg transition-colors duration-200 flex items-center justify-center cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M20 12H4"></path></svg>
        </button>
        <input id="product-quantity" type="number" inputmode="numeric" wire:model.live="quantity" min="1" max="{{ $maxStock }}" class="w-16 h-11 text-center bg-transparent border-0 font-semibold text-base text-zinc-900 dark:text-white tabular-nums focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
        <button wire:click="incrementQuantity" type="button" aria-label="{{ __('Increase quantity') }}" class="w-11 h-11 rounded-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-bold text-lg transition-colors duration-200 flex items-center justify-center cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"></path></svg>
        </button>
    </div>
</div>
