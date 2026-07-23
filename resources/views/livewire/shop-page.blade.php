@push('head')
    {{-- Pagination SEO hints --}}
    @if($this->products->currentPage() < $this->products->lastPage())
        <link rel="next" href="{{ $this->products->nextPageUrl() }}" />
    @endif
    @if($this->products->currentPage() > 1)
        <link rel="prev" href="{{ $this->products->previousPageUrl() }}" />
    @endif

    @php
        $activeCategory = $category !== null ? $this->categories->firstWhere('id', $category) : null;

        $breadcrumbItems = [
            ['name' => __('Home'), 'url' => route('home')],
            ['name' => __('Shop'), 'url' => route('shop')],
        ];

        if ($activeCategory) {
            $breadcrumbItems[] = ['name' => $activeCategory->name, 'url' => url()->current()];
        }

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbItems)->map(function ($item, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            })->values()->all(),
        ];

        if ($this->products->count() > 0) {
            $itemListSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'itemListElement' => $this->products->values()->map(function ($product, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $this->products->firstItem() + $index,
                        'url' => route('product.show', $product),
                    ];
                })->values()->all(),
            ];
        }
    @endphp
    {{--
        Category names are admin-controlled but rendered here for every storefront
        visitor, unescaped, inside a <script> tag. JSON_HEX_TAG escapes < and > (e.g. in a
        name like </script><script>...) so a stored value can never break out of the JSON-LD
        script context and execute as HTML/JS.
    --}}
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    @if(isset($itemListSchema))
        <script type="application/ld+json">
        {!! json_encode($itemListSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
        </script>
    @endif
@endpush

<div
    class="min-h-screen bg-zinc-100 dark:bg-zinc-950"
    x-data
    @open-cart.window="$wire.set('showCart', true)"
>
    {{-- Order Confirmation Modal --}}
    @include('components.public.order-success-modal', [
        'showOrderConfirmation' => $this->showOrderConfirmation,
        'order' => $this->order
    ])

    <section class="relative overflow-hidden pt-2 sm:pt-3 pb-4 sm:pb-5" aria-labelledby="shop-heading">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 pt-[5.75rem] sm:pt-[6.5rem] lg:pt-[6.75rem] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">

                {{-- Breadcrumb --}}
                <nav aria-label="{{ __('Breadcrumb') }}" class="mb-6">
                    <ol class="flex flex-wrap items-center gap-1.5 text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                        <li><a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-900 dark:hover:text-white transition-colors duration-200">{{ __('Home') }}</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="text-zinc-600 dark:text-zinc-300">{{ __('Shop') }}</li>
                    </ol>
                </nav>

                {{-- Header --}}
                <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                        {{ __('Shop') }}
                    </span>
                    <h1 id="shop-heading" class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-tight text-zinc-900 dark:text-white tracking-tight text-balance">
                        {{ __('Our Products') }}
                    </h1>
                    <p class="mt-4 text-sm sm:text-base text-zinc-500 dark:text-zinc-400">
                        {{ __('Browse our full range of natural, premium quality products.') }}
                    </p>
                </div>

                {{-- Toolbar --}}
                <div class="flex flex-col lg:flex-row lg:items-center gap-3 mb-6">
                    <div class="relative flex-1 min-w-0">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg wire:loading.remove wire:target="search" class="w-4.5 h-4.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path>
                            </svg>
                            <svg wire:loading wire:target="search" class="w-4.5 h-4.5 text-emerald-500 animate-spin motion-reduce:animate-none" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <label for="shop-search" class="sr-only">{{ __('Search products') }}</label>
                        <input
                            id="shop-search"
                            type="search"
                            wire:model.live.debounce.400ms="search"
                            placeholder="{{ __('Search products...') }}"
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

                    <div class="flex items-center gap-3">
                        {{-- Mobile filters trigger --}}
                        <button
                            type="button"
                            wire:click="toggleFilters"
                            class="lg:hidden relative flex items-center gap-2 min-h-11 px-5 py-2.5 rounded-full text-sm font-semibold bg-zinc-50 dark:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2] transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M6 9h12M9.75 13.5h4.5M11.25 18h1.5"></path></svg>
                            {{ __('Filters') }}
                            @if($this->activeFilterCount > 0)
                                <span class="flex items-center justify-center min-w-[19px] h-[19px] px-1 rounded-full bg-emerald-600 text-[10px] font-bold text-white tabular-nums">{{ $this->activeFilterCount }}</span>
                            @endif
                        </button>

                        <div class="relative flex-1 lg:flex-none">
                            <label for="shop-sort" class="sr-only">{{ __('Sort by') }}</label>
                            <select
                                id="shop-sort"
                                wire:model.live="sort"
                                class="w-full lg:w-auto min-h-11 appearance-none bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-2xl pl-4 pr-10 py-2.5 text-sm font-medium text-zinc-900 dark:text-white focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 cursor-pointer"
                            >
                                <option value="featured">{{ __('Featured') }}</option>
                                <option value="newest">{{ __('Newest') }}</option>
                                <option value="price_asc">{{ __('Price: Low to High') }}</option>
                                <option value="price_desc">{{ __('Price: High to Low') }}</option>
                            </select>
                            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Results count + display controls --}}
                <div id="product-results" class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400" aria-live="polite" aria-atomic="true">
                        @if($this->products->total() > 0)
                            {{ __('Showing :first–:last of :total products', ['first' => $this->products->firstItem(), 'last' => $this->products->lastItem(), 'total' => $this->products->total()]) }}
                        @else
                            {{ __('No products found') }}
                        @endif
                    </p>

                    <div class="flex items-center gap-3">
                        {{-- Per-page --}}
                        <div class="relative">
                            <label for="shop-per-page" class="sr-only">{{ __('Products per page') }}</label>
                            <select
                                id="shop-per-page"
                                wire:model.live="perPage"
                                class="min-h-9 appearance-none bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-full pl-3.5 pr-8 py-1.5 text-xs font-semibold text-zinc-600 dark:text-zinc-300 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 cursor-pointer"
                            >
                                @foreach(\App\Livewire\ShopPage::PER_PAGE_OPTIONS as $option)
                                    <option value="{{ $option }}">{{ __('Show :count', ['count' => $option]) }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        {{-- Grid columns (desktop only — mobile/tablet keep their fixed responsive layout) --}}
                        <div class="hidden sm:flex items-center gap-1 bg-zinc-50 dark:bg-zinc-800/60 rounded-full ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] p-1" role="group" aria-label="{{ __('Grid columns') }}">
                            @foreach(\App\Livewire\ShopPage::COLUMN_OPTIONS as $option)
                                <button
                                    type="button"
                                    wire:click="$set('columns', {{ $option }})"
                                    aria-pressed="{{ $columns === $option ? 'true' : 'false' }}"
                                    aria-label="{{ __(':count columns', ['count' => $option]) }}"
                                    class="w-7 h-7 rounded-full flex items-center justify-center transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 {{ $columns === $option ? 'bg-emerald-600 text-white shadow-sm' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300' }}"
                                >
                                    <span class="flex items-center gap-0.5" aria-hidden="true">
                                        @for($i = 0; $i < $option; $i++)
                                            <span class="w-1 h-3.5 rounded-full bg-current"></span>
                                        @endfor
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Active filter chips --}}
                @if($search !== '' || $category !== null || $sort !== 'featured' || $minPrice !== '' || $maxPrice !== '' || $inStockOnly || !empty($attributeFilters))
                    <div class="flex flex-wrap items-center gap-2 mb-8 pb-6 border-b border-zinc-900/[0.06] dark:border-white/[0.08]">
                        <span class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Active filters') }}</span>

                        @if($search !== '')
                            <button type="button" wire:click="$set('search', '')" class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                "{{ $search }}"
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif

                        @if($category !== null)
                            @php $activeCategory = $this->categories->firstWhere('id', $category); @endphp
                            @if($activeCategory)
                                <button type="button" wire:click="selectCategory(null)" class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                    {{ $activeCategory->name }}
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        @endif

                        @if($minPrice !== '' || $maxPrice !== '')
                            <button type="button" wire:click="$set('minPrice', ''); $set('maxPrice', '')" class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ $minPrice !== '' ? $minPrice : $this->priceBounds['min'] }} – {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ $maxPrice !== '' ? $maxPrice : $this->priceBounds['max'] }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif

                        @foreach($attributeFilters as $attributeId => $values)
                            @php $attribute = $this->filterableAttributes->firstWhere('id', (int) $attributeId); @endphp
                            @if($attribute)
                                @foreach($values as $value)
                                    <button type="button" wire:key="active-filter-{{ $attributeId }}-{{ $value }}" wire:click="toggleAttributeValue({{ $attributeId }}, @js($value))" class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                        {{ $attribute->name }}: {{ $value }}
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                @endforeach
                            @endif
                        @endforeach

                        @if($inStockOnly)
                            <button type="button" wire:click="$set('inStockOnly', false)" class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                {{ __('In Stock Only') }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif

                        @if($sort !== 'featured')
                            <button type="button" wire:click="$set('sort', 'featured')" class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                {{ match($sort) {
                                    'newest' => __('Newest'),
                                    'price_asc' => __('Price: Low to High'),
                                    'price_desc' => __('Price: High to Low'),
                                    default => $sort,
                                } }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif

                        <button type="button" wire:click="clearFilters" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white underline underline-offset-2 transition-colors duration-200 cursor-pointer ml-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded">
                            {{ __('Clear all') }}
                        </button>
                    </div>
                @endif

                {{-- Sidebar + grid --}}
                <div class="lg:grid lg:grid-cols-[260px_1fr] lg:gap-8 xl:gap-10">
                    {{-- Desktop sidebar --}}
                    <aside class="hidden lg:block">
                        <div class="lg:sticky lg:top-28 bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6">
                            <h2 class="font-display text-lg font-bold text-zinc-900 dark:text-white mb-6">{{ __('Filters') }}</h2>
                            @include('components.public.shop-filters', ['idPrefix' => 'desktop-'])
                        </div>
                    </aside>

                    {{-- Product grid --}}
                    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search, sort, selectCategory, minPrice, maxPrice, inStockOnly, toggleAttributeValue, perPage, gotoPage, nextPage, previousPage, clearFilters" class="transition-opacity duration-200 min-w-0">
                        @if($this->products->count() > 0)
                            <x-public.product-grid :products="$this->products" :columns="$columns" setting-key="storefront_shop_grid_variant" />

                            <div class="mt-10 sm:mt-12">
                                {{ $this->products->links('components.public.pagination') }}
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
                                        {{ __('No products match ":search"', ['search' => $search]) }}
                                    @else
                                        {{ __('No products found') }}
                                    @endif
                                </p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">{{ __('Try adjusting your search or filters.') }}</p>
                                <button
                                    type="button"
                                    wire:click="clearFilters"
                                    class="min-h-11 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 shadow-md shadow-emerald-600/20 hover:shadow-lg cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                                >
                                    {{ __('Clear Filters') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mobile filter drawer --}}
    @if($showFilters)
        <div class="lg:hidden fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center animate-fade-in motion-reduce:animate-none" role="dialog" aria-modal="true" aria-label="{{ __('Filters') }}" wire:click.self="toggleFilters" @keydown.escape.window="$wire.set('showFilters', false)">
            <div x-data x-trap.noscroll="true" class="bg-white dark:bg-zinc-900 rounded-t-[2rem] sm:rounded-3xl max-w-lg w-full max-h-[85vh] overflow-y-auto shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] animate-zoom-in motion-reduce:animate-none">
                <div class="sticky top-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-900/[0.06] dark:border-white/[0.08] px-6 py-4 flex justify-between items-center z-10">
                    <h2 class="font-display text-lg font-bold text-zinc-900 dark:text-white">{{ __('Filters') }}</h2>
                    <button wire:click="toggleFilters" class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ __('Close') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    @include('components.public.shop-filters', ['idPrefix' => 'mobile-'])
                </div>
                <div class="sticky bottom-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-t border-zinc-900/[0.06] dark:border-white/[0.08] p-4 flex gap-3">
                    <button type="button" wire:click="clearFilters" class="flex-1 min-h-11 rounded-full text-sm font-semibold bg-zinc-50 dark:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-zinc-900/[0.15] dark:hover:ring-white/[0.2] transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        {{ __('Clear all') }}
                    </button>
                    <button type="button" wire:click="toggleFilters" class="flex-1 min-h-11 rounded-full text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 hover:shadow-lg transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                        {{ __('Show Results') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Sticky Cart Button --}}
    <x-public.sticky-cart-button :cart="$this->cart" :cartFinalTotal="$this->cartFinalTotal" />

    {{-- Cart Modal --}}
    @include('components.public.cart-modal')

    {{-- Checkout Modal --}}
    @include('components.public.checkout-modal', [
        'showCheckout' => $this->showCheckout,
        'cart' => $this->cart,
        'cartFinalTotal' => $this->cartFinalTotal
    ])
</div>
