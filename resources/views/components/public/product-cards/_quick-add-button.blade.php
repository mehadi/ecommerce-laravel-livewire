@props(['product', 'class' => null])

@php
    $buttonSize = $class ?: 'w-9 h-9 sm:w-10 sm:h-10';
@endphp

@if(!$product->hasAttributes() && $product->isInStock())
    <button
        type="button"
        wire:click="quickAddToCart({{ $product->id }})"
        wire:loading.attr="disabled"
        wire:target="quickAddToCart({{ $product->id }})"
        aria-label="{{ __('Add :name to cart', ['name' => $product->name]) }}"
        class="relative z-10 flex-shrink-0 {{ $buttonSize }} rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all duration-200 shadow-sm shadow-emerald-600/20 hover:shadow-md cursor-pointer touch-manipulation disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800"
    >
        <svg wire:loading.remove wire:target="quickAddToCart({{ $product->id }})" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
        </svg>
        <svg wire:loading wire:target="quickAddToCart({{ $product->id }})" class="w-4 h-4 sm:w-4.5 sm:h-4.5 animate-spin motion-reduce:animate-none" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </button>
@endif
