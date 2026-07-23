@push('head')
    @php
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => __('Categories'), 'item' => route('categories.index')],
            ],
        ];

        $categoryListStart = $this->categories->firstItem() ?? 1;

        $categoryItemListSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $this->categories->values()->map(function ($card, $index) use ($categoryListStart) {
                return [
                    '@type' => 'ListItem',
                    'position' => $categoryListStart + $index,
                    'name' => $card['category']->name,
                    'url' => route('category.show', $card['category']->slug),
                ];
            })->all(),
        ];
    @endphp
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode($categoryItemListSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

<div class="min-h-screen bg-zinc-100 dark:bg-zinc-950">
    <section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5" aria-labelledby="categories-heading">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[6.75rem] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">

                {{-- Breadcrumb --}}
                <nav aria-label="{{ __('Breadcrumb') }}" class="mb-6">
                    <ol class="flex flex-wrap items-center gap-1.5 text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                        <li><a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-900 dark:hover:text-white transition-colors duration-200">{{ __('Home') }}</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="text-zinc-600 dark:text-zinc-300">{{ __('Categories') }}</li>
                    </ol>
                </nav>

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-10 sm:mb-12">
                    <div class="max-w-2xl">
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                                {{ __('Categories') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08]">
                                {{ trans_choice(':count category|:count categories', $this->categories->total(), ['count' => $this->categories->total()]) }}
                            </span>
                        </div>
                        <h1 id="categories-heading" class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-tight text-zinc-900 dark:text-white tracking-tight text-balance">
                            {{ __('Shop by Category') }}
                        </h1>
                        <p class="mt-4 text-sm sm:text-base text-zinc-500 dark:text-zinc-400">
                            {{ __('Browse every department to find exactly what you need.') }}
                        </p>
                    </div>

                    <div class="relative w-full sm:w-72 flex-shrink-0">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg wire:loading.remove wire:target="search" class="w-4.5 h-4.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path>
                            </svg>
                            <svg wire:loading wire:target="search" class="w-4.5 h-4.5 text-emerald-500 animate-spin motion-reduce:animate-none" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <label for="categories-search" class="sr-only">{{ __('Search categories') }}</label>
                        <input
                            id="categories-search"
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Search categories...') }}"
                            class="w-full min-h-11 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-full pl-11 pr-11 py-2.5 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200"
                        >
                        @if($search !== '')
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 flex items-center justify-center text-zinc-600 dark:text-zinc-300 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                                aria-label="{{ __('Clear search') }}"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Results count + display controls --}}
                <div id="product-results" class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        @if($this->categories->total() > 0)
                            {{ __('Showing :first–:last of :total categories', ['first' => $this->categories->firstItem(), 'last' => $this->categories->lastItem(), 'total' => $this->categories->total()]) }}
                        @else
                            {{ __('No categories found') }}
                        @endif
                    </p>

                    <div class="flex items-center gap-3">
                        {{-- Per-page --}}
                        <div class="relative">
                            <label for="categories-per-page" class="sr-only">{{ __('Categories per page') }}</label>
                            <select
                                id="categories-per-page"
                                wire:model.live="perPage"
                                class="min-h-9 appearance-none bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-full pl-3.5 pr-8 py-1.5 text-xs font-semibold text-zinc-600 dark:text-zinc-300 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 cursor-pointer"
                            >
                                @foreach(\App\Livewire\CategoriesPage::perPageOptions() as $option)
                                    <option value="{{ $option }}">{{ __('Show :count', ['count' => $option]) }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        {{-- Grid columns (desktop only, and only for variants that render a uniform grid) --}}
                        @if(\App\Support\CategoryGridVariants::supportsColumns(\App\Models\Setting::get('storefront_category_grid_variant')))
                        <div class="hidden sm:flex items-center gap-1 bg-zinc-50 dark:bg-zinc-800/60 rounded-full ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] p-1" role="group" aria-label="{{ __('Grid columns') }}">
                            @foreach(\App\Livewire\CategoriesPage::columnOptions() as $option)
                                <button
                                    type="button"
                                    wire:click="$set('columns', {{ $option }})"
                                    aria-pressed="{{ $columns === $option ? 'true' : 'false' }}"
                                    aria-label="{{ __(':count columns', ['count' => $option]) }}"
                                    class="min-w-7 h-7 px-1.5 rounded-full flex items-center justify-center text-xs font-bold tabular-nums transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ $columns === $option ? 'bg-emerald-600 text-white shadow-sm' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300' }}"
                                >
                                    {{ $option }}
                                </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Category grid --}}
                <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search, columns, perPage, gotoPage, nextPage, previousPage" class="transition-opacity duration-200">
                @if($this->categories->count() > 0)
                    <x-public.category-grid :cards="$this->categories" :grid-cols-class="$this->gridColsClass" />

                    <div class="mt-10 sm:mt-12">
                        {{ $this->categories->links('components.public.pagination') }}
                    </div>
                @else
                    {{-- Empty state --}}
                    <div class="text-center py-16 sm:py-20">
                        <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-7 h-7 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path>
                            </svg>
                        </div>
                        <p class="font-display text-lg font-semibold text-zinc-900 dark:text-white mb-1.5">
                            @if($search !== '')
                                {{ __('No categories match ":search"', ['search' => $search]) }}
                            @else
                                {{ __('No categories found') }}
                            @endif
                        </p>
                        @if($search !== '')
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">{{ __('Try a different search term.') }}</p>
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                class="min-h-11 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 shadow-md shadow-emerald-600/20 hover:shadow-lg cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                            >
                                {{ __('Clear Search') }}
                            </button>
                        @endif
                    </div>
                @endif
                </div>
            </div>
        </div>
    </section>
</div>
