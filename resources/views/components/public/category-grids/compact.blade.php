{{-- Compact Grid: small square tiles with tight spacing and more categories
     per row — best for large catalogs. --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
    @foreach($items as $card)
        @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
        <article wire:key="category-{{ $category->id }}" class="group relative flex flex-col rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] overflow-hidden hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 hover:-translate-y-0.5 transition-all duration-300 motion-reduce:transition-none motion-reduce:transform-none">
            <div class="relative overflow-hidden aspect-square bg-white dark:bg-zinc-900">
                @include('components.public.category-grids._image', [
                    'category' => $category,
                    'imgClass' => 'w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-500 motion-reduce:transform-none',
                    'iconClass' => 'w-8 h-8 text-emerald-300/70 dark:text-emerald-700/50',
                ])
            </div>
            <div class="p-3">
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
