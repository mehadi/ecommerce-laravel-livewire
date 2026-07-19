{{-- Masonry Grid: natural image height (no forced aspect ratio) so cards
     stack at varying heights within their CSS column. --}}
@props(['product'])

<article class="group relative bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-3 sm:p-4 flex flex-col transition-all duration-300 motion-reduce:transition-none hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)]">
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
        @if($product->primary_image)
            <img
                src="{{ asset('storage/'.$product->primary_image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-auto object-cover group-hover:scale-[1.03] transition-transform duration-500 motion-reduce:transform-none"
            >
        @else
            <div class="w-full aspect-square flex items-center justify-center">
                <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        @endif
        <x-public.product-cards._discount-badge :product="$product" />
        <x-public.product-cards._featured-badge :product="$product" />
        <x-public.product-cards._out-of-stock-overlay :product="$product" />
    </div>
    <div class="flex flex-col flex-1 pt-3">
        @if($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">
                {{ $product->category->name }}
            </p>
        @endif
        <h2 class="font-display text-sm sm:text-base font-semibold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h2>
        <div class="flex items-center justify-between gap-2 mt-2">
            <x-public.product-cards._price :product="$product" class="text-base sm:text-lg" />
            <x-public.product-cards._quick-add-button :product="$product" class="w-8 h-8 sm:w-9 sm:h-9" />
        </div>
    </div>
</article>
