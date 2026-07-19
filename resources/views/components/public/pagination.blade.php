@if($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center gap-1.5 flex-wrap">
        @if($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="{{ __('Previous page') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-300 dark:text-zinc-600 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </span>
        @else
            <button
                type="button"
                x-on:click="$wire.previousPage().then(() => document.getElementById('product-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                wire:loading.attr="disabled"
                rel="prev"
                aria-label="{{ __('Previous page') }}"
                class="w-10 h-10 rounded-full flex items-center justify-center bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2] transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
        @endif

        @foreach($elements as $element)
            @if(is_string($element))
                <span aria-disabled="true" class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-zinc-400 dark:text-zinc-500">{{ $element }}</span>
            @endif

            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span aria-current="page" class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold bg-emerald-600 text-white shadow-md shadow-emerald-600/20 tabular-nums">
                            {{ $page }}
                        </span>
                    @else
                        <button
                            type="button"
                            x-on:click="$wire.gotoPage({{ $page }}).then(() => document.getElementById('product-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                            wire:loading.attr="disabled"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2] transition-all duration-200 cursor-pointer touch-manipulation tabular-nums focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                        >
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if($paginator->hasMorePages())
            <button
                type="button"
                x-on:click="$wire.nextPage().then(() => document.getElementById('product-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                wire:loading.attr="disabled"
                rel="next"
                aria-label="{{ __('Next page') }}"
                class="w-10 h-10 rounded-full flex items-center justify-center bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2] transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
            </button>
        @else
            <span aria-disabled="true" aria-label="{{ __('Next page') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-300 dark:text-zinc-600 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
            </span>
        @endif
    </nav>
@endif
