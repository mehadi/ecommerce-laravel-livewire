{{-- Classic cart content — image cards, pill quantity stepper, coupon field, totals box. --}}
<div class="space-y-4 mb-6">
    @foreach($this->cart as $productId => $item)
        <div wire:key="cart-line-{{ $productId }}" class="flex items-center gap-3 sm:gap-4 bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] transition-shadow duration-200 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)]">
            @if($item['image'])
                <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <h3 class="text-sm sm:text-base font-semibold text-zinc-900 dark:text-white mb-0.5 truncate">{{ $item['name'] }}</h3>
                <p class="text-base font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'], 2) }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 tabular-nums">{{ __('Quantity') }}: {{ $item['quantity'] }}</p>
            </div>
            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2 sm:gap-3">
                <div class="flex items-center gap-0.5 bg-white dark:bg-zinc-900 rounded-full ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] p-0.5">
                    <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] - 1 }})" wire:loading.attr="disabled" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:opacity-60" aria-label="{{ __('Decrease quantity') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M20 12H4"></path></svg>
                    </button>
                    <span class="w-9 text-center text-sm font-semibold text-zinc-900 dark:text-white tabular-nums">{{ $item['quantity'] }}</span>
                    <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] + 1 }})" wire:loading.attr="disabled" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:opacity-60" aria-label="{{ __('Increase quantity') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
                <button wire:click="removeFromCart('{{ $productId }}')" wire:loading.attr="disabled" class="w-9 h-9 rounded-full bg-red-50 dark:bg-red-900/25 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 flex items-center justify-center transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 disabled:opacity-60" aria-label="{{ __('Remove item') }}">
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
            <span class="font-semibold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartSubtotal, 2) }}</span>
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
                <span class="text-base font-bold text-emerald-600 dark:text-emerald-400 flex-shrink-0 tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartDiscount, 2) }}</span>
            </div>
        @endif

        <div class="flex justify-between items-center pt-3 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
            <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
            <span class="font-display text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartFinalTotal, 2) }}</span>
        </div>
    </div>

    <button wire:click="$set('showCheckout', true); $set('showCart', false)" class="w-full min-h-12 bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        {{ __('Proceed to Checkout') }}
    </button>
</div>
