{{-- Split Cards: horizontal cards split in half — image on the left, name and
     subcategories on the right. --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
    @foreach($items as $card)
        @php [$category, $productCount, $subcategories] = [$card['category'], $card['productCount'], $card['subcategories']]; @endphp
        <article class="group relative flex rounded-3xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] transition-all duration-300 motion-reduce:transition-none">
            <div class="relative w-2/5 flex-shrink-0 overflow-hidden bg-white dark:bg-zinc-900 min-h-36">
                @include('components.public.category-grids._image', [
                    'category' => $category,
                    'imgClass' => 'absolute inset-0 w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-500 motion-reduce:transform-none',
                    'iconClass' => 'w-9 h-9 text-emerald-300/70 dark:text-emerald-700/50',
                ])
            </div>
            <div class="flex flex-col justify-center flex-1 p-5 min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-1 tabular-nums">
                    {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                </p>
                <h2 class="font-display text-base sm:text-lg font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800 rounded-3xl after:absolute after:inset-0 after:rounded-3xl"
                    >
                        {{ $category->name }}
                    </a>
                </h2>
                @if($subcategories->count() > 0)
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed line-clamp-2 relative z-10">
                        {{ $subcategories->take(4)->map(fn ($subcategory) => $subcategory->name)->implode(' · ') }}@if($subcategories->count() > 4) {{ __('+:count more', ['count' => $subcategories->count() - 4]) }}@endif
                    </p>
                @endif
                <span class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                    {{ __('Browse') }}
                    <svg aria-hidden="true" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300 motion-reduce:transform-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </div>
        </article>
    @endforeach
</div>
