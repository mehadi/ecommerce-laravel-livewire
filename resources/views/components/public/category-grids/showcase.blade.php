{{-- Showcase: the first category renders as a large feature banner, the rest
     in a compact grid below it. --}}
@php
    $featured = $items->first();
    $rest = $items->skip(1);
@endphp

<div class="flex flex-col gap-5 sm:gap-6">
    @if($featured)
        @php [$category, $productCount, $subcategories] = [$featured['category'], $featured['productCount'], $featured['subcategories']]; @endphp
        <article class="group relative rounded-3xl overflow-hidden aspect-[16/9] sm:aspect-[21/9] bg-zinc-200 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:shadow-[0_12px_32px_-12px_rgb(16_24_40_/_0.25)] transition-all duration-300 motion-reduce:transition-none">
            @include('components.public.category-grids._image', [
                'category' => $category,
                'imgClass' => 'absolute inset-0 w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none',
                'iconClass' => 'w-16 h-16 text-emerald-300/70 dark:text-emerald-700/50',
            ])
            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-zinc-950/85 via-zinc-950/30 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-6 sm:p-8">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-emerald-400 mb-1.5 tabular-nums">
                    {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                </p>
                <h2 class="font-display text-2xl sm:text-3xl font-bold text-white leading-tight text-balance">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-3xl after:absolute after:inset-0 after:rounded-3xl"
                    >
                        {{ $category->name }}
                    </a>
                </h2>
                @if($subcategories->count() > 0)
                    <div class="flex flex-wrap items-center gap-1.5 mt-3 relative z-10">
                        @foreach($subcategories->take(4) as $subcategory)
                            <a
                                href="{{ route('category.show', $subcategory->slug) }}"
                                wire:navigate
                                class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-sm text-[11px] font-semibold text-white ring-1 ring-white/20 hover:bg-emerald-600 hover:ring-emerald-600 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400"
                            >
                                {{ $subcategory->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    @endif

    @if($rest->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @foreach($rest as $card)
                @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
                <article class="group relative flex flex-col rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:-translate-y-0.5 transition-all duration-300 motion-reduce:transition-none motion-reduce:transform-none">
                    <div class="relative overflow-hidden aspect-[4/3] bg-white dark:bg-zinc-900">
                        @include('components.public.category-grids._image', [
                            'category' => $category,
                            'imgClass' => 'w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-500 motion-reduce:transform-none',
                            'iconClass' => 'w-9 h-9 text-emerald-300/70 dark:text-emerald-700/50',
                        ])
                    </div>
                    <div class="p-4">
                        <h2 class="font-display text-sm font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 truncate">
                            <a
                                href="{{ route('category.show', $category->slug) }}"
                                wire:navigate
                                class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800 rounded-2xl after:absolute after:inset-0 after:rounded-2xl"
                            >
                                {{ $category->name }}
                            </a>
                        </h2>
                        <p class="mt-0.5 text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 tabular-nums">
                            {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
