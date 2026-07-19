{{--
    Shared "Place Order" submit button + security trust badge. Reused by
    every checkout variant.

    Optional: $style = 'default' | 'compact' | 'plain' — controls button chrome.
--}}
@php
    $style = $style ?? 'default';
    $buttonClass = match ($style) {
        'plain' => 'w-full min-h-12 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 py-3.5 text-sm font-semibold tracking-wide uppercase hover:opacity-90 transition-opacity duration-200 cursor-pointer touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2.5',
        'compact' => 'w-full min-h-10 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-200 cursor-pointer touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2',
        default => 'w-full min-h-12 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0',
    };
@endphp

<button
    type="submit"
    wire:loading.attr="disabled"
    class="{{ $buttonClass }}"
>
    <span wire:loading.remove wire:target="placeOrder" class="flex items-center gap-2.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        {{ __('Place Order') }}
    </span>
    <span wire:loading wire:target="placeOrder" class="flex items-center gap-2.5">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ __('Processing...') }}
    </span>
</button>

@if($style !== 'plain')
    <div class="flex items-center justify-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 pt-1">
        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ __('Your information is secure and encrypted') }}</span>
    </div>
@else
    <p class="text-center text-xs text-zinc-400 dark:text-zinc-500 pt-1">{{ __('Your information is secure and encrypted') }}</p>
@endif
