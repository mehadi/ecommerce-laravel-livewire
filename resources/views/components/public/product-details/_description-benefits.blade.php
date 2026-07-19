{{--
    Shared "About This Product" + "Key Benefits" blocks. Reused by every
    product-details variant except Editorial, which tabs this content
    instead of stacking it.

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
    $gapClass = $style === 'plain' ? 'space-y-4' : 'space-y-5';
@endphp

<div class="{{ $gapClass }}">
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

    <div class="{{ $wrapClass }}">
        @if($style !== 'plain')
            <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2.5">
                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 ring-1 ring-amber-600/10 dark:ring-amber-500/20">
                    <svg class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </span>
                {{ __('Key Benefits') }}
            </h3>
        @else
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-2">{{ __('Key Benefits') }}</h3>
        @endif
        @if($product->benefits)
            <div class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-300 leading-relaxed space-y-2">
                {!! nl2br(e($product->benefits)) !!}
            </div>
        @else
            <ul class="space-y-3 text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-300">
                @foreach([__('100% Natural & Organic'), __('No Artificial Additives'), __('Rich in Nutrients & Vitamins')] as $benefit)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $benefit }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
