{{--
    Shared price display for products WITHOUT attributes: price, compare-at
    price, and the discount badge. Reused by every product-details variant.

    Required: $product (hasAttributes() === false).
    Optional: $style = 'default' | 'compact' | 'plain' — controls card chrome.
--}}
@php
    $style = $style ?? 'default';
    $wrapClass = match ($style) {
        'plain' => '',
        'compact' => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-5',
        default => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8',
    };
@endphp

<div class="{{ $wrapClass }}">
    <div class="flex flex-wrap items-end gap-x-4 gap-y-2">
        <div>
            @if($style !== 'plain')
                <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mb-1 block">{{ __('Price') }}</span>
            @endif
            <span class="font-display text-4xl sm:text-5xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums tracking-tight">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($product->price, 2) }}</span>
        </div>
        @if($product->hasDiscount())
            <div class="flex items-center gap-2 pb-1">
                <span class="text-lg sm:text-xl text-zinc-400 dark:text-zinc-500 line-through tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($product->compare_at_price, 2) }}</span>
                <span class="inline-flex items-center bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-red-600/10 dark:ring-red-500/20 tabular-nums">
                    -{{ $product->discountPercentage() }}%
                </span>
            </div>
        @endif
    </div>
</div>
