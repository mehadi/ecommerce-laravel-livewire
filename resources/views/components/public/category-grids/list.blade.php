{{-- List Rows: single-column horizontal rows — thumbnail left, name and
     subcategories right. Dense and scannable. --}}
<div class="flex flex-col gap-3 sm:gap-4 max-w-3xl mx-auto">
    @foreach($items as $card)
        @php [$category, $productCount, $subcategories] = [$card['category'], $card['productCount'], $card['subcategories']]; @endphp
        <article class="group relative flex items-center gap-4 sm:gap-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-3 sm:p-4 hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] transition-all duration-300 motion-reduce:transition-none">
            <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl overflow-hidden bg-white dark:bg-zinc-900">
                @include('components.public.category-grids._image', [
                    'category' => $category,
                    'imgClass' => 'w-full h-full object-cover group-hover:scale-[1.06] transition-transform duration-500 motion-reduce:transform-none',
                    'iconClass' => 'w-8 h-8 text-emerald-300/70 dark:text-emerald-700/50',
                ])
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-0.5 tabular-nums">
                    {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                </p>
                <h2 class="font-display text-base sm:text-lg font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 truncate">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800 rounded-2xl after:absolute after:inset-0 after:rounded-2xl"
                    >
                        {{ $category->name }}
                    </a>
                </h2>
                @if($subcategories->count() > 0)
                    <div class="hidden sm:flex flex-wrap items-center gap-1.5 mt-2 relative z-10">
                        @foreach($subcategories->take(3) as $subcategory)
                            <a
                                href="{{ route('category.show', $subcategory->slug) }}"
                                wire:navigate
                                class="inline-flex items-center px-2.5 py-1 rounded-full bg-white dark:bg-zinc-900 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-emerald-600/30 dark:hover:ring-emerald-500/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                            >
                                {{ $subcategory->name }}
                            </a>
                        @endforeach
                        @if($subcategories->count() > 3)
                            <span class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 px-1">
                                {{ __('+:count more', ['count' => $subcategories->count() - 3]) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <span aria-hidden="true" class="w-9 h-9 flex-shrink-0 rounded-full bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] flex items-center justify-center text-zinc-400 dark:text-zinc-500 group-hover:bg-emerald-600 group-hover:text-white group-hover:ring-0 transition-colors duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </span>
        </article>
    @endforeach
</div>
