{{-- Compact cart content — dense single-line rows with small thumbnails and inline controls. --}}
<div class="divide-y divide-zinc-100 dark:divide-zinc-800 mb-5">
    @foreach($this->cart as $productId => $item)
        <div wire:key="cart-line-{{ $productId }}" class="flex items-center gap-3 py-3">
            @if($item['image'])
                <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="w-12 h-12 rounded-lg object-cover ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $item['name'] }}</h3>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'], 2) }}</p>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] - 1 }})" wire:loading.attr="disabled" class="w-6 h-6 rounded-md bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-150 cursor-pointer disabled:opacity-60" aria-label="{{ __('Decrease quantity') }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" d="M20 12H4"></path></svg>
                </button>
                <span class="w-6 text-center text-xs font-semibold text-zinc-900 dark:text-white tabular-nums">{{ $item['quantity'] }}</span>
                <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] + 1 }})" wire:loading.attr="disabled" class="w-6 h-6 rounded-md bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-150 cursor-pointer disabled:opacity-60" aria-label="{{ __('Increase quantity') }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
            <button wire:click="removeFromCart('{{ $productId }}')" wire:loading.attr="disabled" class="w-6 h-6 rounded-md text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center transition-colors duration-150 cursor-pointer flex-shrink-0 disabled:opacity-60" aria-label="{{ __('Remove item') }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endforeach
</div>

<div class="space-y-3">
    <div class="flex items-center gap-2">
        <input type="text" wire:model="couponCode" placeholder="{{ __('Coupon code') }}" class="flex-1 min-h-9 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500/25">
        <button wire:click="applyCoupon" wire:loading.attr="disabled" wire:target="applyCoupon" class="min-h-9 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-1.5 rounded-lg text-xs font-semibold hover:opacity-90 transition-opacity cursor-pointer disabled:opacity-60">
            {{ __('Apply') }}
        </button>
    </div>

    @if($this->appliedCoupon)
        <div class="flex justify-between items-center text-xs">
            <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ __('Discount') }} ({{ $this->appliedCoupon->code }})</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartDiscount, 2) }}</span>
        </div>
    @endif

    <div class="flex justify-between text-sm text-zinc-500 dark:text-zinc-400">
        <span>{{ __('Subtotal') }}</span>
        <span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartSubtotal, 2) }}</span>
    </div>
    <div class="flex justify-between items-center pt-2 border-t border-zinc-200 dark:border-zinc-700">
        <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
        <span class="text-lg font-bold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartFinalTotal, 2) }}</span>
    </div>

    <button wire:click="$set('showCheckout', true); $set('showCart', false)" class="w-full min-h-10 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors duration-200 cursor-pointer touch-manipulation">
        {{ __('Proceed to Checkout') }}
    </button>
</div>
