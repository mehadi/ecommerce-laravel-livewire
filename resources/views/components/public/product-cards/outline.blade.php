{{-- Outline Cards: flat, border-only, no fill or shadow. --}}
@props(['product'])

<article {{ $attributes }} class="group relative flex flex-col rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5 transition-colors duration-200 hover:border-zinc-900 dark:hover:border-white">
    <div class="relative overflow-hidden rounded-xl aspect-square bg-zinc-50 dark:bg-zinc-800/40">
        @if($product->primary_image)
            <img
                src="{{ asset('storage/'.$product->primary_image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500 motion-reduce:transform-none"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        @endif
        <x-public.product-cards._discount-badge :product="$product" />
        <x-public.product-cards._featured-badge :product="$product" />
        <x-public.product-cards._out-of-stock-overlay :product="$product" />
    </div>
    <div class="flex flex-col flex-1 pt-4">
        @if($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">
                {{ $product->category->name }}
            </p>
        @endif
        <h2 class="font-display text-base sm:text-lg font-semibold text-zinc-900 dark:text-white leading-snug transition-colors duration-200 text-balance">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h2>
        <div class="flex items-center justify-between gap-2 mt-auto pt-3">
            <x-public.product-cards._price :product="$product" />
            <x-public.product-cards._quick-add-button :product="$product" />
        </div>
    </div>
</article>
