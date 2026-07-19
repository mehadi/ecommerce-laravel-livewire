{{-- Wide Banners: panoramic two-per-row banners with the photo behind a large
     category name. --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
    @foreach($items as $card)
        @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
        <article class="group relative rounded-3xl overflow-hidden aspect-[21/9] bg-zinc-200 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:shadow-[0_12px_32px_-12px_rgb(16_24_40_/_0.25)] transition-all duration-300 motion-reduce:transition-none">
            @include('components.public.category-grids._image', [
                'category' => $category,
                'imgClass' => 'absolute inset-0 w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-500 motion-reduce:transform-none',
                'iconClass' => 'w-12 h-12 text-emerald-300/70 dark:text-emerald-700/50',
            ])
            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-r from-zinc-950/80 via-zinc-950/35 to-transparent"></div>
            <div class="absolute inset-y-0 left-0 flex flex-col justify-center p-6 sm:p-8 max-w-[75%]">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-emerald-400 mb-1.5 tabular-nums">
                    {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                </p>
                <h2 class="font-display text-xl sm:text-2xl font-bold text-white leading-tight text-balance">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-3xl after:absolute after:inset-0 after:rounded-3xl"
                    >
                        {{ $category->name }}
                    </a>
                </h2>
                <span class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 group-hover:text-white transition-colors duration-200">
                    {{ __('Shop now') }}
                    <svg aria-hidden="true" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300 motion-reduce:transform-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </div>
        </article>
    @endforeach
</div>
