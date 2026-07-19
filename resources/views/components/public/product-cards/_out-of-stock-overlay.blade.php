@props(['product'])
@unless($product->isInStock())
    <span class="absolute inset-0 bg-white/60 dark:bg-zinc-900/60 flex items-center justify-center">
        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-zinc-900/90 text-white text-xs font-bold uppercase tracking-wider">
            {{ __('Out of Stock') }}
        </span>
    </span>
@endunless
