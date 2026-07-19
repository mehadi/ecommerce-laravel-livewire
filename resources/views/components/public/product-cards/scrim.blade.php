{{-- Scrim Card: full-bleed photo, name/price on a bottom gradient overlay. --}}
@props(['product'])

<article class="group relative overflow-hidden rounded-3xl aspect-[4/5] bg-zinc-100 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] transition-all duration-300 motion-reduce:transition-none hover:-translate-y-1 hover:shadow-[0_12px_32px_-16px_rgb(16_24_40_/_0.25)] motion-reduce:transform-none">
    @if($product->primary_image)
        <img
            src="{{ asset('storage/'.$product->primary_image) }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none"
        >
    @else
        <div class="absolute inset-0 flex items-center justify-center bg-zinc-200 dark:bg-zinc-700">
            <svg class="w-12 h-12 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
    @endif

    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/15 to-transparent" aria-hidden="true"></span>

    <x-public.product-cards._discount-badge :product="$product" />
    <x-public.product-cards._featured-badge :product="$product" />
    <x-public.product-cards._out-of-stock-overlay :product="$product" />

    <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 flex items-end justify-between gap-3">
        <div class="min-w-0">
            @if($product->category)
                <p class="text-[11px] font-semibold uppercase tracking-widest text-white/60 mb-1">
                    {{ $product->category->name }}
                </p>
            @endif
            <h2 class="font-display text-base sm:text-lg font-semibold text-white leading-snug text-balance mb-1.5">
                <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                    {{ $product->name }}
                </a>
            </h2>
            <x-public.product-cards._price :product="$product" class="text-base sm:text-lg" />
        </div>
        <x-public.product-cards._quick-add-button :product="$product" />
    </div>
</article>
