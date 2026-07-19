{{-- Detailed cart content — larger imagery, variant chips, and a per-line subtotal. --}}
<div class="space-y-4 mb-6">
    @foreach($this->cart as $productId => $item)
        @php
            $lineSubtotal = $item['price'] * $item['quantity'];
            $attributeData = $item['attribute_data'] ?? [];
        @endphp
        <div wire:key="cart-line-{{ $productId }}" class="flex gap-4 sm:gap-5 bg-white dark:bg-zinc-800/60 rounded-2xl p-5 sm:p-6 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.06]">
            @if($item['image'])
                <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0 flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-sm sm:text-base font-semibold text-zinc-900 dark:text-white">{{ $item['name'] }}</h3>
                    <button wire:click="removeFromCart('{{ $productId }}')" wire:loading.attr="disabled" class="text-zinc-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-150 cursor-pointer flex-shrink-0 disabled:opacity-60" aria-label="{{ __('Remove item') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                @if(! empty($attributeData))
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach($attributeData as $attrKey => $attrValue)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-700 text-[11px] font-medium text-zinc-600 dark:text-zinc-300">{{ $attrKey }}: {{ $attrValue }}</span>
                        @endforeach
                    </div>
                @endif

                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'], 2) }} {{ __('each') }}</p>

                <div class="mt-auto pt-3 flex items-center justify-between">
                    <div class="flex items-center gap-0.5 bg-zinc-50 dark:bg-zinc-900 rounded-full ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] p-0.5">
                        <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] - 1 }})" wire:loading.attr="disabled" class="w-7 h-7 rounded-full hover:bg-white dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer disabled:opacity-60" aria-label="{{ __('Decrease quantity') }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M20 12H4"></path></svg>
                        </button>
                        <span class="w-8 text-center text-sm font-semibold text-zinc-900 dark:text-white tabular-nums">{{ $item['quantity'] }}</span>
                        <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] + 1 }})" wire:loading.attr="disabled" class="w-7 h-7 rounded-full hover:bg-white dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer disabled:opacity-60" aria-label="{{ __('Increase quantity') }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>
                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($lineSubtotal, 2) }}</span>
                </div>
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
            <input type="text" wire:model="couponCode" placeholder="{{ __('Enter coupon code') }}" class="flex-1 min-h-11 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full px-5 py-2.5 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200">
            <button wire:click="applyCoupon" wire:loading.attr="disabled" wire:target="applyCoupon" class="min-h-11 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 shadow-md shadow-emerald-600/20 hover:shadow-lg whitespace-nowrap cursor-pointer touch-manipulation disabled:opacity-60">
                {{ __('Apply') }}
            </button>
        </div>

        @if($this->appliedCoupon)
            <div class="flex justify-between items-center bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl px-4 py-3 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                <span class="text-sm text-emerald-700 dark:text-emerald-300 font-semibold truncate">{{ __('Discount') }} ({{ $this->appliedCoupon->code }})</span>
                <span class="text-base font-bold text-emerald-600 dark:text-emerald-400 flex-shrink-0 tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartDiscount, 2) }}</span>
            </div>
        @endif

        <div class="flex justify-between items-center pt-3 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
            <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
            <span class="font-display text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartFinalTotal, 2) }}</span>
        </div>
    </div>

    <button wire:click="$set('showCheckout', true); $set('showCart', false)" class="w-full min-h-12 bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation">
        {{ __('Proceed to Checkout') }}
    </button>
</div>
