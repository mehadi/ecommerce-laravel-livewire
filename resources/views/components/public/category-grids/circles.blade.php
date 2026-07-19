{{-- Circle Icons: round category images with centered names — a light,
     boutique directory look. --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-x-4 gap-y-8 sm:gap-x-6 sm:gap-y-10">
    @foreach($items as $card)
        @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
        <article wire:key="category-{{ $category->id }}" class="group relative flex flex-col items-center text-center">
            <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden bg-zinc-100 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] group-hover:ring-2 group-hover:ring-emerald-500/60 group-hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.18)] transition-all duration-300 motion-reduce:transition-none mb-4">
                @include('components.public.category-grids._image', [
                    'category' => $category,
                    'imgClass' => 'w-full h-full object-cover group-hover:scale-[1.08] transition-transform duration-500 motion-reduce:transform-none',
                    'iconClass' => 'w-9 h-9 text-emerald-300/70 dark:text-emerald-700/50',
                ])
            </div>
            <h2 class="font-display text-sm font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
                <a
                    href="{{ route('category.show', $category->slug) }}"
                    wire:navigate
                    class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900 rounded-full after:absolute after:inset-0"
                >
                    {{ $category->name }}
                </a>
            </h2>
            <p class="mt-1 text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 tabular-nums">
                {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
            </p>
        </article>
    @endforeach
</div>
