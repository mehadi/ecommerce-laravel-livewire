{{-- Minimal Tiles: no card background or border — just the image, a name and
     a thin divider underneath. --}}
<div class="grid {{ $gridColsClass }} gap-x-5 gap-y-8 sm:gap-x-6 sm:gap-y-10">
    @foreach($items as $card)
        @php [$category, $productCount] = [$card['category'], $card['productCount']]; @endphp
        <article wire:key="category-{{ $category->id }}" class="group relative flex flex-col">
            <div class="relative overflow-hidden rounded-2xl aspect-square bg-zinc-100 dark:bg-zinc-800/60 mb-4">
                @include('components.public.category-grids._image', [
                    'category' => $category,
                    'imgClass' => 'w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none',
                ])
            </div>
            <div class="flex items-baseline justify-between gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800 group-hover:border-emerald-600/40 dark:group-hover:border-emerald-500/40 transition-colors duration-300">
                <h2 class="font-display text-base font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200 text-balance">
                    <a
                        href="{{ route('category.show', $category->slug) }}"
                        wire:navigate
                        class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900 rounded-lg after:absolute after:inset-0"
                    >
                        {{ $category->name }}
                    </a>
                </h2>
                <p class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 tabular-nums whitespace-nowrap">
                    {{ $productCount }}
                </p>
            </div>
        </article>
    @endforeach
</div>
