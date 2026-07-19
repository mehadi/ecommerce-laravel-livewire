@props(['product', 'class' => 'text-lg sm:text-xl'])

@php
    $minPrice = $product->getSyncedPrice();
    $maxPrice = $product->getMaxPrice();
    $currency = \App\Models\Setting::get('currency_symbol', '৳');
@endphp

<div class="flex items-baseline gap-2 min-w-0">
    <span class="{{ $class }} font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
        {{ $currency }}{{ number_format($minPrice, 2) }}@if($product->hasAttributes() && $maxPrice > $minPrice)<span class="text-sm font-semibold"> – {{ $currency }}{{ number_format($maxPrice, 2) }}</span>@endif
    </span>
    @if(!$product->hasAttributes() && $product->hasDiscount())
        <span class="text-sm text-zinc-400 dark:text-zinc-500 line-through tabular-nums">{{ $currency }}{{ number_format($product->compare_at_price, 2) }}</span>
    @endif
</div>
