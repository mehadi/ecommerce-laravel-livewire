{{-- Minimal cart content — text-only rows, no images or card backgrounds. --}}
<div class="space-y-0 mb-6">
    @foreach($this->cart as $productId => $item)
        <div wire:key="cart-line-{{ $productId }}" class="flex items-center justify-between gap-4 py-4 border-b border-zinc-200 dark:border-zinc-800">
            <div class="min-w-0">
                <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $item['name'] }}</p>
                <div class="mt-1.5 flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                    <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] - 1 }})" wire:loading.attr="disabled" class="hover:text-zinc-900 dark:hover:text-white cursor-pointer disabled:opacity-60" aria-label="{{ __('Decrease quantity') }}">&minus;</button>
                    <span class="tabular-nums">{{ __('Qty') }} {{ $item['quantity'] }}</span>
                    <button wire:click="updateCartQuantity('{{ $productId }}', {{ $item['quantity'] + 1 }})" wire:loading.attr="disabled" class="hover:text-zinc-900 dark:hover:text-white cursor-pointer disabled:opacity-60" aria-label="{{ __('Increase quantity') }}">+</button>
                    <button wire:click="removeFromCart('{{ $productId }}')" wire:loading.attr="disabled" class="underline hover:text-red-600 dark:hover:text-red-400 cursor-pointer disabled:opacity-60">{{ __('Remove') }}</button>
                </div>
            </div>
            <span class="text-sm font-medium text-zinc-900 dark:text-white tabular-nums flex-shrink-0">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($item['price'], 2) }}</span>
        </div>
    @endforeach
</div>

<div class="space-y-3">
    <div class="flex items-center gap-3">
        <input type="text" wire:model="couponCode" placeholder="{{ __('Have a coupon code?') }}" class="flex-1 bg-transparent border-0 border-b border-zinc-300 dark:border-zinc-700 px-0 py-1.5 text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-white focus:outline-none focus:ring-0">
        <button wire:click="applyCoupon" wire:loading.attr="disabled" wire:target="applyCoupon" class="text-sm font-medium text-zinc-900 dark:text-white underline hover:no-underline cursor-pointer disabled:opacity-60">
            {{ __('Apply') }}
        </button>
    </div>

    @if($this->appliedCoupon)
        <div class="flex justify-between text-sm text-zinc-500 dark:text-zinc-400">
            <span>{{ __('Discount') }} ({{ $this->appliedCoupon->code }})</span>
            <span class="tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartDiscount, 2) }}</span>
        </div>
    @endif

    <div class="flex justify-between text-sm text-zinc-500 dark:text-zinc-400">
        <span>{{ __('Subtotal') }}</span>
        <span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartSubtotal, 2) }}</span>
    </div>
    <div class="flex justify-between items-baseline pt-3 border-t border-zinc-200 dark:border-zinc-800">
        <span class="text-base font-medium text-zinc-900 dark:text-white">{{ __('Total') }}</span>
        <span class="text-xl font-semibold text-zinc-900 dark:text-white tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->cartFinalTotal, 2) }}</span>
    </div>

    <button wire:click="$set('showCheckout', true); $set('showCart', false)" class="w-full min-h-12 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 py-3.5 text-sm font-semibold tracking-wide uppercase hover:opacity-90 transition-opacity duration-200 cursor-pointer touch-manipulation">
        {{ __('Proceed to Checkout') }}
    </button>
</div>
