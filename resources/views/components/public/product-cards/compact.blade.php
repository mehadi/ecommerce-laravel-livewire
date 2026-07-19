{{-- Compact Grid: tighter spacing and smaller cards for dense catalogs. --}}
@props(['product'])

<article {{ $attributes }} class="group relative bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-2.5 sm:p-3 flex flex-col transition-all duration-200 motion-reduce:transition-none hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:-translate-y-0.5 motion-reduce:transform-none">
    <div class="relative overflow-hidden rounded-xl aspect-square bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
        @if($product->primary_image)
            <img
                src="{{ asset('storage/'.$product->primary_image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-8 h-8 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        @endif
        <x-public.product-cards._discount-badge :product="$product" />
        <x-public.product-cards._out-of-stock-overlay :product="$product" />
    </div>
    <div class="flex flex-col flex-1 pt-2.5 sm:pt-3">
        <h2 class="font-display text-xs sm:text-sm font-semibold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance line-clamp-2">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h2>
        <div class="flex items-center justify-between gap-1.5 mt-2">
            <x-public.product-cards._price :product="$product" class="text-sm sm:text-base" />
            <x-public.product-cards._quick-add-button :product="$product" class="w-7 h-7 sm:w-8 sm:h-8" />
        </div>
    </div>
</article>
