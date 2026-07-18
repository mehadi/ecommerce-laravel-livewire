@php
    use App\Models\Setting;

    $cartIsPanel = Setting::get('cart_display_mode', 'modal') === 'panel';
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
                    <div class="space-y-4 mb-6">
                        @foreach($this->cart as $productId => $item)
                            <div class="flex items-center gap-3 sm:gap-4 bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] transition-shadow duration-200 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)]">
                                @if($item['image'])
                                    <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] flex-shrink-0">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-zinc-900 dark:text-white mb-0.5 truncate">{{ $item['name'] }}</h3>
                                    <p class="text-base font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">৳{{ number_format($item['price'], 2) }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 tabular-nums">{{ __('Quantity') }}: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2 sm:gap-3">
                                    <div class="flex items-center gap-0.5 bg-white dark:bg-zinc-900 rounded-full ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] p-0.5">
                                        <button wire:click="updateCartQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ __('Decrease quantity') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M20 12H4"></path></svg>
                                        </button>
                                        <span class="w-9 text-center text-sm font-semibold text-zinc-900 dark:text-white tabular-nums">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateCartQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ __('Increase quantity') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                    <button wire:click="removeFromCart({{ $productId }})" class="w-9 h-9 rounded-full bg-red-50 dark:bg-red-900/25 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 flex items-center justify-center transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500" aria-label="{{ __('Remove item') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-5">
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8 space-y-4">
                            <div class="flex justify-between text-sm sm:text-base">
                                <span class="text-zinc-600 dark:text-zinc-300 font-medium">{{ __('Subtotal') }}</span>
                                <span class="font-semibold text-zinc-900 dark:text-white tabular-nums">৳{{ number_format($this->cartSubtotal, 2) }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
                                <label for="cart-coupon" class="sr-only">{{ __('Enter coupon code') }}</label>
                                <input id="cart-coupon" type="text" wire:model="couponCode" placeholder="{{ __('Enter coupon code') }}" class="flex-1 min-h-11 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-2.5 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200">
                                <button wire:click="applyCoupon" wire:loading.attr="disabled" wire:target="applyCoupon" class="min-h-11 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 shadow-md shadow-emerald-600/20 hover:shadow-lg whitespace-nowrap cursor-pointer touch-manipulation disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                                    {{ __('Apply') }}
                                </button>
                            </div>

                            @if($this->appliedCoupon)
                                <div class="flex justify-between items-center bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl px-4 py-3 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-emerald-700 dark:text-emerald-300 font-semibold truncate">{{ __('Discount') }} ({{ $this->appliedCoupon->code }})</span>
                                    </div>
                                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400 flex-shrink-0 tabular-nums">-৳{{ number_format($this->cartDiscount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center pt-3 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
                                <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
                                <span class="font-display text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">৳{{ number_format($this->cartFinalTotal, 2) }}</span>
                            </div>
                        </div>

                        <button wire:click="$set('showCheckout', true); $set('showCart', false)" class="w-full min-h-12 bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            {{ __('Proceed to Checkout') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
