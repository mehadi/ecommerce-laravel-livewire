{{--
    Shared in-stock/out-of-stock pill for products WITHOUT attributes
    (attribute-based stock is shown inside _attribute-picker instead).

    Required: $product (hasAttributes() === false).
--}}
<div class="flex flex-wrap items-center gap-2.5">
    @if($product->isInStock())
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs sm:text-sm font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse motion-reduce:animate-none"></span>
            {{ __('In Stock') }}
        </span>
    @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full text-xs sm:text-sm font-semibold ring-1 ring-red-600/10 dark:ring-red-500/20">
            {{ __('Out of Stock') }}
        </span>
    @endif
    <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 tabular-nums">({{ $product->stock }} {{ __('available') }})</span>
</div>
