{{-- Photo Overlay: full-bleed category photo with the name and product count
     on a bottom gradient scrim. --}}
<div class="grid {{ $gridColsClass }} gap-5 sm:gap-6">
    @foreach($items as $card)
        @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
        <article class="group relative rounded-3xl overflow-hidden aspect-[4/5] bg-zinc-200 dark:bg-zinc-800 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:shadow-[0_12px_32px_-12px_rgb(16_24_40_/_0.25)] transition-all duration-300 motion-reduce:transition-none">
            @include('components.public.category-grids._image', [
                'category' => $category,
                'imgClass' => 'absolute inset-0 w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-500 motion-reduce:transform-none',
                'iconClass' => 'w-14 h-14 text-emerald-300/70 dark:text-emerald-700/50',
            ])
            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-zinc-950/85 via-zinc-950/25 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-white/70 mb-1 tabular-nums">
                    {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                </p>
                <h2 class="font-display text-xl font-bold text-white leading-snug text-balance flex items-center gap-2">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 rounded-3xl after:absolute after:inset-0 after:rounded-3xl"
                    >
                        {{ $category->name }}
                    </a>
                    <svg aria-hidden="true" class="w-4 h-4 text-emerald-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 motion-reduce:transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </h2>
            </div>
        </article>
    @endforeach
</div>
