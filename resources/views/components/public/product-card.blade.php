@props(['product'])

@php
    $minPrice = $product->getSyncedPrice();
    $maxPrice = $product->getMaxPrice();
@endphp

<article
    class="group relative bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-4 sm:p-5 flex flex-col transition-all duration-300 motion-reduce:transition-none hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 motion-reduce:transform-none"
>
    <div class="relative overflow-hidden rounded-2xl aspect-square bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
        @if($product->primary_image)
            <img
                src="{{ asset('storage/'.$product->primary_image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        @endif
        @if($product->hasDiscount())
            <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold ring-1 ring-red-600/10 dark:ring-red-500/20 tabular-nums">
                -{{ round($product->discountPercentage()) }}%
            </span>
        @endif
        @if($product->is_featured)
            <span class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold ring-1 ring-amber-600/10 dark:ring-amber-500/20">
                {{ __('Featured') }}
            </span>
        @endif
        @unless($product->isInStock())
            <span class="absolute inset-0 bg-white/60 dark:bg-zinc-900/60 flex items-center justify-center">
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-zinc-900/90 text-white text-xs font-bold uppercase tracking-wider">
                    {{ __('Out of Stock') }}
                </span>
            </span>
        @endunless
    </div>
    <div class="flex flex-col flex-1 pt-4 sm:pt-5">
        @if($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">
                {{ $product->category->name }}
            </p>
        @endif
        <h2 class="font-display text-base sm:text-lg font-semibold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0 after:rounded-3xl">
                {{ $product->name }}
            </a>
        </h2>
        <div class="flex items-center justify-between gap-2 mt-auto pt-3">
            <div class="flex items-baseline gap-2 min-w-0">
                <span class="text-lg sm:text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                    ৳{{ number_format($minPrice, 2) }}@if($product->hasAttributes() && $maxPrice > $minPrice)<span class="text-sm font-semibold"> – ৳{{ number_format($maxPrice, 2) }}</span>@endif
                </span>
                @if(!$product->hasAttributes() && $product->hasDiscount())
                    <span class="text-sm text-zinc-400 dark:text-zinc-500 line-through tabular-nums">৳{{ number_format($product->compare_at_price, 2) }}</span>
                @endif
            </div>
            @if(!$product->hasAttributes() && $product->isInStock())
                <button
                    type="button"
                    wire:click="quickAddToCart({{ $product->id }})"
                    wire:loading.attr="disabled"
                    wire:target="quickAddToCart({{ $product->id }})"
                    aria-label="{{ __('Add :name to cart', ['name' => $product->name]) }}"
                    class="relative z-10 flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all duration-200 shadow-sm shadow-emerald-600/20 hover:shadow-md cursor-pointer touch-manipulation disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800"
                >
                    <svg wire:loading.remove wire:target="quickAddToCart({{ $product->id }})" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    <svg wire:loading wire:target="quickAddToCart({{ $product->id }})" class="w-4 h-4 sm:w-4.5 sm:h-4.5 animate-spin motion-reduce:animate-none" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</article>
