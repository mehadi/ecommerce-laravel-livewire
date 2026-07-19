{{-- Promo Grid: bigger discount ribbon, price-forward layout, built for sales. --}}
@props(['product'])

@php
    $currency = \App\Models\Setting::get('currency_symbol', '৳');
    $savedAmount = $product->hasDiscount() && ! $product->hasAttributes()
        ? $product->getSyncedCompareAtPrice() - $product->getSyncedPrice()
        : null;
@endphp

<article class="group relative bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-4 sm:p-5 flex flex-col transition-all duration-300 motion-reduce:transition-none hover:ring-red-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 motion-reduce:transform-none">
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
                <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        @endif
        @if($product->hasDiscount())
            <span class="absolute top-3 left-3 inline-flex items-center px-3 py-1.5 rounded-xl bg-red-600 text-white text-sm font-extrabold shadow-md tabular-nums">
                -{{ round($product->discountPercentage()) }}%
            </span>
        @endif
        <x-public.product-cards._featured-badge :product="$product" />
        <x-public.product-cards._out-of-stock-overlay :product="$product" />
    </div>
    <div class="flex flex-col flex-1 pt-4 sm:pt-5">
        @if($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">
                {{ $product->category->name }}
            </p>
        @endif
        <h2 class="font-display text-base sm:text-lg font-semibold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h2>
        <div class="mt-auto pt-3">
            <x-public.product-cards._price :product="$product" class="text-xl sm:text-2xl" />
            <div class="flex items-center justify-between gap-2 mt-2">
                @if($savedAmount)
                    <span class="text-xs font-bold text-red-600 dark:text-red-400">
                        {{ __('Save :amount', ['amount' => $currency.number_format($savedAmount, 2)]) }}
                    </span>
                @else
                    <span></span>
                @endif
                <x-public.product-cards._quick-add-button :product="$product" />
            </div>
        </div>
    </div>
</article>
