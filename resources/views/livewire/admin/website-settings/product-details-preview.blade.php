{{-- Miniature schematic of a product-details variant. Expects $variant (a key from ProductDetailsVariants::keys()). --}}
@switch($variant)
    @case('classic')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            <div class="rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-1">
                <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="grid grid-cols-4 gap-0.5">
                    @for($i = 0; $i < 4; $i++)
                        <div class="aspect-square rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    @endfor
                </div>
            </div>
            <div class="flex flex-col gap-1.5 justify-center">
                <div class="h-1.5 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-2 w-2/3 rounded bg-emerald-500 mt-1"></div>
                <div class="h-1.5 rounded-full bg-white dark:bg-zinc-700 mt-1"></div>
            </div>
        </div>
        @break

    @case('gallery-focus')
        <div class="w-full h-full flex gap-1.5">
            <div class="flex flex-col gap-1 justify-center">
                @for($i = 0; $i < 3; $i++)
                    <div class="w-2 h-2 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                @endfor
            </div>
            <div class="flex-[1.6] rounded bg-white dark:bg-zinc-700 p-1">
                <div class="w-full h-full rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
            <div class="flex-1 flex flex-col gap-1.5 justify-center">
                <div class="h-1.5 w-full rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-2 w-full rounded bg-emerald-500 mt-1"></div>
                <div class="h-1.5 rounded-full bg-white dark:bg-zinc-700"></div>
            </div>
        </div>
        @break

    @case('minimal')
        <div class="w-full h-full flex gap-1.5">
            <div class="w-1/4 rounded bg-white dark:bg-zinc-700 p-1">
                <div class="w-full h-full rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
            <div class="flex-1 flex flex-col gap-1.5 justify-center">
                <div class="h-1.5 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600 border-b border-zinc-300 dark:border-zinc-600 pb-1"></div>
                <div class="h-1.5 w-1/3 rounded bg-zinc-800 dark:bg-white mt-1"></div>
                <div class="h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
        </div>
        @break

    @case('split-sticky')
        <div class="w-full h-full flex flex-col gap-1">
            <div class="flex-1 grid grid-cols-2 gap-1.5">
                <div class="rounded bg-white dark:bg-zinc-700 p-1">
                    <div class="w-full h-full rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                </div>
                <div class="rounded-sm ring-1 ring-emerald-500 bg-white dark:bg-zinc-700 p-1 flex flex-col gap-1 justify-center">
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="h-1.5 rounded bg-emerald-500"></div>
                </div>
            </div>
            <div class="h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
        </div>
        @break

    @case('editorial')
        <div class="w-full h-full flex flex-col items-center gap-1">
            <div class="w-full flex-[1.4] rounded bg-white dark:bg-zinc-700 p-1">
                <div class="w-full h-full rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
            <div class="h-1 w-1/2 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
            <div class="h-1.5 w-1/3 rounded bg-emerald-500"></div>
            <div class="flex gap-1.5 mt-0.5">
                <div class="h-0.5 w-4 rounded-full bg-emerald-500"></div>
                <div class="h-0.5 w-4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
            </div>
        </div>
        @break

    @default
        <div class="w-full h-full flex flex-col items-center justify-center gap-1 text-zinc-400 dark:text-zinc-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M9 3.75V3a1.5 1.5 0 011.5-1.5h3A1.5 1.5 0 0115 3v.75M9 3.75h6"></path>
            </svg>
            <span class="text-[10px] font-medium">{{ __('Preview unavailable') }}</span>
        </div>
        @break
@endswitch
