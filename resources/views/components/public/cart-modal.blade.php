@php
    use App\Models\Setting;
    use App\Support\CartVariants;

    $cartIsPanel = Setting::get('cart_display_mode', 'modal') === 'panel';
    $cartVariant = CartVariants::resolve(Setting::get('storefront_cart_variant'));
@endphp

@if($this->showCart)
    <div
        x-data="{ show: @entangle('showCart') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 flex motion-reduce:transition-none {{ $cartIsPanel ? 'justify-end' : 'items-center justify-center p-3 sm:p-4' }}"
        @click.self="$wire.set('showCart', false)"
        @keydown.escape.window="$wire.set('showCart', false)"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Your Shopping Cart') }}"
    >
        <div
            x-trap.noscroll="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="{{ $cartIsPanel ? 'translate-x-full' : 'opacity-0 scale-95' }}"
            x-transition:enter-end="{{ $cartIsPanel ? 'translate-x-0' : 'opacity-100 scale-100' }}"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="{{ $cartIsPanel ? 'translate-x-0' : 'opacity-100 scale-100' }}"
            x-transition:leave-end="{{ $cartIsPanel ? 'translate-x-full' : 'opacity-0 scale-95' }}"
            class="bg-white dark:bg-zinc-900 overflow-y-auto shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] motion-reduce:transition-none {{ $cartIsPanel ? 'h-full w-full sm:max-w-md' : 'rounded-3xl max-w-2xl w-full max-h-[92vh]' }}"
        >
            <div class="sticky top-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-900/[0.06] dark:border-white/[0.08] px-6 sm:px-8 py-4 flex justify-between items-center z-10">
                <h2 class="font-display text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </span>
                    <span class="hidden sm:inline">{{ __('Your Shopping Cart') }}</span>
                    <span class="sm:hidden">{{ __('Cart') }}</span>
                </h2>
                <button wire:click="$set('showCart', false)" class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ __('Close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 sm:p-8">
                @if(session('error'))
                    <div class="mb-5 flex items-start gap-2.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 ring-1 ring-red-600/10 dark:ring-red-500/20 rounded-2xl px-4 py-3 text-sm font-medium" role="alert">
                        <svg class="w-4.5 h-4.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('success'))
                    <div class="mb-5 flex items-start gap-2.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 rounded-2xl px-4 py-3 text-sm font-medium" role="status">
                        <svg class="w-4.5 h-4.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(empty($this->cart))
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-9 h-9 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="font-display text-lg font-semibold text-zinc-900 dark:text-white mb-1.5">{{ __('Your Cart is Empty') }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Start adding products to your cart') }}</p>
                    </div>
                @else
                    @include('components.public.carts.'.$cartVariant)
                @endif
            </div>
        </div>
    </div>
@endif
