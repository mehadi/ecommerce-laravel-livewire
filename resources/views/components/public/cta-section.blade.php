@props(['product'])

<section
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    class="py-4 sm:py-5"
>
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div
            class="relative overflow-hidden rounded-[2rem] bg-white dark:bg-zinc-900 p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] transition-all duration-700 motion-reduce:transition-none"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
        >
            {{-- Decorative layers --}}
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.12),transparent_50%)]"></div>
            <div class="pointer-events-none absolute -top-20 -right-20 w-72 h-72 bg-lime-300/15 rounded-full blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-16 w-72 h-72 bg-teal-300/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-2xl mx-auto text-center">
                <x-public.eyebrow-badge :text="__('Limited Stock')" class="mb-5" />
                <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-tight text-zinc-900 dark:text-white mb-4 sm:mb-5 tracking-tight text-balance">
                    {{ __('Ready to Experience Premium Quality?') }}
                </h2>
                <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 mb-8 sm:mb-10 leading-relaxed max-w-xl mx-auto">
                    {{ __('Order now and enjoy free delivery, secure payment, and 30-day money-back guarantee') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart" class="group btn-tenant-primary text-white px-8 sm:px-10 py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900" {{ !$product || !$product->isInStock() ? 'disabled' : '' }}>
                        <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <svg wire:loading wire:target="addToCart" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        {{ __('Add to Cart') }}
                    </button>
                    <button wire:click="buyNow" wire:loading.attr="disabled" wire:target="buyNow" class="bg-amber-400 text-zinc-900 px-8 sm:px-10 py-4 rounded-full text-base sm:text-lg font-bold hover:bg-amber-300 transition-all duration-300 shadow-md shadow-amber-500/25 hover:shadow-lg hover:shadow-amber-500/30 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900" {{ !$product || !$product->isInStock() ? 'disabled' : '' }}>
                        <svg wire:loading.remove wire:target="buyNow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <svg wire:loading wire:target="buyNow" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        {{ __('Buy Now') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

