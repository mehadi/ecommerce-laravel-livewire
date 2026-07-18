<div
    x-data="{
        moveHighlight(direction) {
            const items = Array.from(this.$refs.list?.querySelectorAll('[data-result]') || []);
            if (!items.length) return;
            let idx = items.indexOf(document.activeElement);
            idx = idx === -1 ? (direction > 0 ? 0 : items.length - 1) : (idx + direction + items.length) % items.length;
            items[idx].focus();
        },
    }"
    @keydown.down.prevent="moveHighlight(1)"
    @keydown.up.prevent="moveHighlight(-1)"
    @keydown.escape="$event.target.blur()"
    class="group relative {{ $variant === 'mobile' ? 'w-full' : 'hidden md:flex items-center flex-1 max-w-xs lg:max-w-sm ml-1' }}"
>
    <form
        role="search"
        action="{{ route('shop') }}"
        method="GET"
        class="relative flex items-center w-full"
    >
        <label for="nav-search-{{ $variant }}" class="sr-only">{{ __('Search products') }}</label>
        <input
            id="nav-search-{{ $variant }}"
            type="search"
            name="search"
            autocomplete="off"
            wire:model.live.debounce.300ms="query"
            placeholder="{{ __('Search products...') }}"
            class="w-full {{ $variant === 'mobile' ? 'h-11' : 'h-10 lg:h-11' }} rounded-full bg-zinc-100 dark:bg-white/[0.07] border-0 pl-4 pr-12 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-900/15 dark:focus:ring-white/25 transition-shadow duration-200"
        >
        <button
            type="submit"
            class="absolute right-1 flex items-center justify-center {{ $variant === 'mobile' ? 'w-9 h-9' : 'w-8 h-8 lg:w-9 lg:h-9' }} rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-sm hover:scale-105 motion-reduce:transform-none transition-transform duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white focus-visible:ring-offset-2"
            aria-label="{{ __('Search') }}"
        >
            <svg wire:loading.remove wire:target="query" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"></path>
            </svg>
            <svg wire:loading wire:target="query" class="w-4 h-4 animate-spin motion-reduce:animate-none" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </button>
    </form>

    {{--
        Visibility is driven purely by CSS :focus-within (via the `group` /
        `group-focus-within` pair) rather than Alpine client state, because
        Livewire's DOM morph after each AJAX search re-render can reset
        in-memory Alpine state — :focus-within is recomputed fresh from the
        live DOM every time, so it can never get "stuck" closed or open.
    --}}
    <div
        class="hidden group-focus-within:block absolute z-50 top-full left-0 right-0 mt-2 w-full sm:min-w-[22rem] bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] overflow-hidden"
    >
        @if(trim($query) === '' && $this->results->isNotEmpty())
            <div class="px-4 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                {{ __('Featured products') }}
            </div>
        @endif

        <div x-ref="list" class="max-h-96 overflow-y-auto p-1.5">
            @forelse($this->results as $product)
                <a
                    href="{{ route('product.show', $product) }}"
                    data-result
                    wire:key="nav-search-{{ $variant }}-result-{{ $product->id }}"
                    class="flex items-center gap-3 px-2.5 py-2 rounded-xl text-left transition-colors duration-150 hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none"
                >
                    <span class="shrink-0 w-11 h-11 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.06]">
                        @if($product->primary_image)
                            <img src="{{ asset('storage/'.$product->primary_image) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <span class="w-full h-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </span>
                        @endif
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $product->name }}</span>
                        @if($product->category)
                            <span class="block text-xs text-zinc-400 dark:text-zinc-500 truncate">{{ $product->category->name }}</span>
                        @endif
                    </span>
                    <span class="shrink-0 text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                        ৳{{ number_format($product->price, 2) }}
                    </span>
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('No products found for ":query"', ['query' => $query]) }}</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ __('Try a different search term.') }}</p>
                </div>
            @endforelse
        </div>

        @if(trim($query) !== '' && $this->results->isNotEmpty())
            <a
                href="{{ route('shop', ['search' => $query]) }}"
                data-result
                class="flex items-center justify-center gap-1.5 px-4 py-3 text-sm font-semibold text-zinc-700 dark:text-zinc-200 bg-zinc-50 dark:bg-zinc-800/60 hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none border-t border-zinc-100 dark:border-zinc-800 transition-colors duration-150"
            >
                {{ __('View all results for ":query"', ['query' => $query]) }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        @endif
    </div>
</div>
