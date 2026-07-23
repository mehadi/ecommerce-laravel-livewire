@props(['cart', 'cartFinalTotal'])

@if(!empty($cart))
    <div
        x-data
        x-init="$nextTick(() => $refs.stickyCartSpacer.style.height = $refs.stickyCartBar.offsetHeight + 'px')"
    >
        {{-- In-flow spacer reserving the fixed bar's height so it doesn't permanently
             overlap footer/related-products content on short pages. --}}
        <div x-ref="stickyCartSpacer" aria-hidden="true"></div>
        <div
            x-ref="stickyCartBar"
            class="fixed bottom-0 left-0 right-0 z-40 animate-slide-up motion-reduce:animate-none pb-[env(safe-area-inset-bottom)]">
            <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="relative mb-4 bg-white/85 dark:bg-zinc-900/85 backdrop-blur-xl rounded-3xl sm:rounded-full ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_16px_40px_-16px_rgb(16_24_40_/_0.20)] px-5 sm:px-6 lg:px-8 py-3">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 px-4 py-2 rounded-full">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-semibold text-sm text-emerald-800 dark:text-emerald-300 tabular-nums">{{ count($cart) }} {{ __('Items') }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] uppercase tracking-wider font-medium text-zinc-500 dark:text-zinc-400 block">{{ __('Total') }}</span>
                            <span class="font-display text-xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($cartFinalTotal, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2.5 w-full sm:w-auto">
                        <button wire:click="$set('showCart', true)" class="flex-1 sm:flex-none min-h-11 bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] text-zinc-800 dark:text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                            {{ __('View Cart') }}
                        </button>
                        <button wire:click="$set('showCheckout', true)" class="flex-1 sm:flex-none min-h-11 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-2.5 rounded-full font-semibold text-sm transition-all duration-200 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                            {{ __('Checkout') }}
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endif
