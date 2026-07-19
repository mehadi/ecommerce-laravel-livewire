{{-- Classic Cards: the original categories-page design — image cards with
     product counts and subcategory chips. --}}
<div class="grid {{ $gridColsClass }} gap-5 sm:gap-6">
    @foreach($items as $card)
        @php [$category, $productCount, $subcategories] = [$card['category'], $card['productCount'], $card['subcategories']]; @endphp
        <article wire:key="category-{{ $category->id }}" class="group relative flex flex-col rounded-3xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 transition-all duration-300 motion-reduce:transition-none motion-reduce:transform-none">
            <div class="relative overflow-hidden aspect-[4/3] bg-white dark:bg-zinc-900">
                @include('components.public.category-grids._image', [
                    'category' => $category,
                    'imgClass' => 'w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none',
                    'iconClass' => 'w-12 h-12 text-emerald-300/70 dark:text-emerald-700/50',
                ])
                <span aria-hidden="true" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 dark:bg-zinc-900/80 backdrop-blur-sm ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] flex items-center justify-center text-zinc-500 dark:text-zinc-300 group-hover:bg-emerald-600 dark:group-hover:bg-emerald-600 group-hover:text-white group-hover:ring-0 transition-colors duration-300">
                    <svg class="w-4 h-4 -rotate-45 group-hover:rotate-0 transition-transform duration-300 motion-reduce:transform-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </div>
            <div class="flex flex-col flex-1 p-5 sm:p-6">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1.5 tabular-nums">
                    {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                </p>
                <h2 class="font-display text-lg font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800 rounded-3xl after:absolute after:inset-0 after:rounded-3xl"
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
                                class="inline-flex items-center px-2.5 py-1 rounded-full bg-white dark:bg-zinc-900 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-emerald-600/30 dark:hover:ring-emerald-500/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                            >
                                {{ $subcategory->name }}
                            </a>
                        @endforeach
                        @if($subcategories->count() > 4)
                            <span class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 px-1">
                                {{ __('+:count more', ['count' => $subcategories->count() - 4]) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </article>
    @endforeach
</div>
