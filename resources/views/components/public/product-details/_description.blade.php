{{--
    Shared "About This Product" block. Reused by every product-details
    variant except Editorial, which tabs this content instead of stacking it.

    Required: $product.
    Optional: $style = 'default' | 'compact' | 'plain' — controls card chrome.
--}}
@php
    $style = $style ?? 'default';
    $wrapClass = match ($style) {
        'plain' => 'pt-4 border-t border-zinc-200 dark:border-zinc-800',
        'compact' => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-5',
        default => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8',
    };
@endphp

@if($product->description)
    <div class="{{ $wrapClass }}">
        @if($style !== 'plain')
            <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2.5">
                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
                {{ __('About This Product') }}
            </h3>
        @else
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-2">{{ __('About This Product') }}</h3>
        @endif
        <p class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-300 leading-relaxed">{{ $product->description }}</p>
    </div>
@endif
