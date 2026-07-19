{{--
    Shared Add to Cart / Buy Now buttons, disabled until a valid
    product/attribute combination with stock is selected. Reused by every
    product-details variant.

    Required: $product.
    Optional: $style = 'default' | 'compact' | 'plain' — controls button chrome.
--}}
@php
    $style = $style ?? 'default';
    $buttonPad = $style === 'compact' ? 'px-6 py-3 text-sm' : 'px-8 py-4 sm:py-[18px] text-base sm:text-lg';

    $isDisabled = false;
    if ($product->hasAttributes()) {
        $isDisabled = ! $this->selectedProductAttributeId || empty($this->selectedAttributeValues);
        if ($this->selectedProductAttributeId) {
            $attr = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
            $isDisabled = ! $attr || $attr->stock <= 0 || ! $attr->is_active;
        }
    } else {
        $isDisabled = ! $product->isInStock();
    }
@endphp

<div>
    <div class="flex flex-col sm:flex-row gap-3">
        <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart" class="group w-full sm:flex-1 bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 {{ $buttonPad }} rounded-full font-bold transition-all duration-300 ring-1 ring-emerald-600/30 dark:ring-emerald-400/30 hover:ring-emerald-600 dark:hover:ring-emerald-400 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900" {{ $isDisabled ? 'disabled' : '' }}>
            <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <svg wire:loading wire:target="addToCart" class="w-5 h-5 sm:w-6 sm:h-6 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            {{ __('Add to Cart') }}
        </button>
        <button wire:click="buyNow" wire:loading.attr="disabled" wire:target="buyNow" class="group w-full sm:flex-1 btn-tenant-primary text-white {{ $buttonPad }} rounded-full font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900" {{ $isDisabled ? 'disabled' : '' }}>
            <svg wire:loading.remove wire:target="buyNow" class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300 group-hover:translate-x-0.5 motion-reduce:transform-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <svg wire:loading wire:target="buyNow" class="w-5 h-5 sm:w-6 sm:h-6 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            {{ __('Buy Now') }}
        </button>
    </div>
    @if($product->hasAttributes() && empty($this->selectedAttributeValues))
        <p class="mt-3 text-sm text-amber-700 dark:text-amber-400 text-center flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ __('Please select all attributes to continue') }}
        </p>
    @endif
</div>
