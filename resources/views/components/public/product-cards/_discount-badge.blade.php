@props(['product'])
@if($product->hasDiscount())
    <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold ring-1 ring-red-600/10 dark:ring-red-500/20 tabular-nums">
        -{{ round($product->discountPercentage()) }}%
    </span>
@endif
