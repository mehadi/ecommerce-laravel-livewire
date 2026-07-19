@props(['product'])
@if($product->is_featured)
    <span class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold ring-1 ring-amber-600/10 dark:ring-amber-500/20">
        {{ __('Featured') }}
    </span>
@endif
