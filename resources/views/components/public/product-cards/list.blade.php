{{-- List View: horizontal row, image left, details right. --}}
@props(['product'])

<article class="group relative flex items-center gap-4 sm:gap-5 bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-3 sm:p-4 transition-all duration-300 motion-reduce:transition-none hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)]">
    <div class="relative shrink-0 w-20 h-20 sm:w-28 sm:h-28 overflow-hidden rounded-xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
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
        <x-public.product-cards._out-of-stock-overlay :product="$product" />
    </div>

    <div class="flex-1 min-w-0 flex flex-col gap-1">
        <div class="flex items-center gap-2">
            @if($product->category)
                <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    {{ $product->category->name }}
                </p>
            @endif
            @if($product->hasDiscount())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-[10px] font-bold ring-1 ring-red-600/10 dark:ring-red-500/20 tabular-nums">
                    -{{ round($product->discountPercentage()) }}%
                </span>
            @endif
            @if($product->is_featured)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold ring-1 ring-amber-600/10 dark:ring-amber-500/20">
                    {{ __('Featured') }}
                </span>
            @endif
        </div>
        <h2 class="font-display text-base sm:text-lg font-semibold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h2>
        <x-public.product-cards._price :product="$product" class="text-base sm:text-lg" />
    </div>

    <x-public.product-cards._quick-add-button :product="$product" />
</article>
