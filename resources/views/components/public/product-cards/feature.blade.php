{{-- Feature Grid card: same card used for the large "feature" slot and the
     smaller grid below it, toggled by the $featured prop. --}}
@props(['product', 'featured' => false])

<article class="group relative bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden flex transition-all duration-300 motion-reduce:transition-none hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 motion-reduce:transform-none {{ $featured ? 'flex-col sm:flex-row' : 'flex-col' }}">
    <div class="relative overflow-hidden bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] {{ $featured ? 'aspect-square sm:aspect-auto sm:w-1/2' : 'aspect-square' }}">
        @if($product->primary_image)
            <img
                src="{{ asset('storage/'.$product->primary_image) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="{{ $featured ? 'w-16 h-16' : 'w-10 h-10' }} text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        @endif
        <x-public.product-cards._discount-badge :product="$product" />
        <x-public.product-cards._featured-badge :product="$product" />
        <x-public.product-cards._out-of-stock-overlay :product="$product" />
    </div>
    <div class="flex flex-col flex-1 {{ $featured ? 'p-5 sm:p-8 sm:justify-center gap-3' : 'p-4 sm:p-5' }}">
        @if($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1">
                {{ $product->category->name }}
            </p>
        @endif
        <h2 class="font-display font-semibold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance {{ $featured ? 'text-2xl sm:text-3xl' : 'text-base sm:text-lg' }}">
            <a href="{{ route('product.show', $product) }}" wire:navigate class="focus-visible:outline-none after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h2>
        <div class="flex items-center justify-between gap-2 {{ $featured ? 'mt-4' : 'mt-auto pt-3' }}">
            <x-public.product-cards._price :product="$product" :class="$featured ? 'text-2xl sm:text-3xl' : 'text-lg sm:text-xl'" />
            <x-public.product-cards._quick-add-button :product="$product" :class="$featured ? 'w-11 h-11 sm:w-12 sm:h-12' : null" />
        </div>
    </div>
</article>
