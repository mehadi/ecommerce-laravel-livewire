{{-- Noir Cards: cards on a near-black chip for a high-contrast, premium
     department look. --}}
<div class="rounded-3xl bg-zinc-950 p-4 sm:p-6 ring-1 ring-white/10">
    <div class="grid {{ $gridColsClass }} gap-4 sm:gap-5">
        @foreach($items as $card)
            @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
            <article class="group relative flex flex-col rounded-2xl bg-zinc-900 ring-1 ring-white/10 overflow-hidden hover:ring-emerald-500/50 hover:-translate-y-0.5 transition-all duration-300 motion-reduce:transition-none motion-reduce:transform-none">
                <div class="relative overflow-hidden aspect-[4/3] bg-zinc-800">
                    @include('components.public.category-grids._image', [
                        'category' => $category,
                        'imgClass' => 'w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-[1.04] transition-all duration-500 motion-reduce:transform-none',
                        'iconClass' => 'w-10 h-10 text-zinc-600',
                        'placeholderClass' => 'bg-gradient-to-br from-zinc-800 to-zinc-900',
                    ])
                </div>
                <div class="p-4 sm:p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-500 mb-1 tabular-nums">
                        {{ trans_choice(':count product|:count products', $productCount, ['count' => $productCount]) }}
                    </p>
                    <h2 class="font-display text-base font-bold text-white leading-snug group-hover:text-emerald-400 transition-colors duration-200 text-balance">
                        <a
                            href="{{ route('category.show', $category->slug) }}"
                            wire:navigate
                            class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900 rounded-2xl after:absolute after:inset-0 after:rounded-2xl"
                        >
                            {{ $category->name }}
                        </a>
                    </h2>
                </div>
            </article>
        @endforeach
    </div>
</div>
