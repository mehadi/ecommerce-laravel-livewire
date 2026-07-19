{{-- Sidebar-dark cart content — high-contrast dark totals panel, built for a slide-in drawer. --}}
<div class="space-y-3 mb-6">
    @foreach($this->cart as $productId => $item)
        <div wire:key="cart-line-{{ $productId }}" class="flex items-center gap-3 py-2">
            @if($item['image'])
                <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="w-14 h-14 rounded-lg object-cover ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $item['name'] }}</h3>
                <div class="flex items-center gap-2 mt-1">
                    <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 rounded-md px-1">
                        <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] - 1 }})" wire:loading.attr="disabled" class="w-5 h-5 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer disabled:opacity-60" aria-label="{{ __('Decrease quantity') }}">&minus;</button>
                        <span class="text-xs font-semibold text-zinc-900 dark:text-white tabular-nums w-4 text-center">{{ $item['quantity'] }}</span>
                        <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] + 1 }})" wire:loading.attr="disabled" class="w-5 h-5 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer disabled:opacity-60" aria-label="{{ __('Increase quantity') }}">+</button>
                    </div>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'], 2) }}</span>
                </div>
            </div>
            <button wire:click="removeFromCart('{{ $productId }}')" wire:loading.attr="disabled" class="text-zinc-400 hover:text-red-600 dark:hover:text-red-400 cursor-pointer flex-shrink-0 disabled:opacity-60" aria-label="{{ __('Remove item') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endforeach
</div>

<div class="bg-zinc-950 dark:bg-black rounded-3xl p-6 sm:p-7 space-y-4 ring-1 ring-white/10">
    <div class="flex items-center gap-2">
        <input type="text" wire:model="couponCode" placeholder="{{ __('Coupon code') }}" class="flex-1 min-h-10 bg-zinc-900 dark:bg-zinc-800 border border-white/10 rounded-full px-4 py-2 text-sm text-white placeholder:text-zinc-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
        <button wire:click="applyCoupon" wire:loading.attr="disabled" wire:target="applyCoupon" class="min-h-10 bg-white text-zinc-950 px-5 py-2 rounded-full text-sm font-semibold hover:bg-zinc-200 transition-colors cursor-pointer disabled:opacity-60">
            {{ __('Apply') }}
        </button>
    </div>

    <div class="space-y-2 text-sm">
        <div class="flex justify-between text-zinc-400">
            <span>{{ __('Subtotal') }}</span>
            <span class="tabular-nums text-zinc-200">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartSubtotal, 2) }}</span>
        </div>
        @if($this->appliedCoupon)
            <div class="flex justify-between text-emerald-400">
                <span>{{ __('Discount') }} ({{ $this->appliedCoupon->code }})</span>
                <span class="tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartDiscount, 2) }}</span>
            </div>
        @endif
    </div>

    <div class="flex justify-between items-center pt-3 border-t border-white/10">
        <span class="text-base font-semibold text-white">{{ __('Total') }}</span>
        <span class="text-2xl font-bold text-emerald-400 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartFinalTotal, 2) }}</span>
    </div>

    <button wire:click="$set('showCheckout', true); $set('showCart', false)" class="w-full min-h-12 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 py-3.5 rounded-full text-base font-bold transition-colors duration-200 cursor-pointer touch-manipulation flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        {{ __('Proceed to Checkout') }}
    </button>
</div>
