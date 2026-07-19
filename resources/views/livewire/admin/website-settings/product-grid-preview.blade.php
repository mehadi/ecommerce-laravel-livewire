{{-- Miniature schematic of a product-grid variant. Expects $variant (a key from ProductGridVariants::keys()). --}}
@switch($variant)
    @case('grid')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-1">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="h-1 w-1/2 rounded-full bg-emerald-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('minimal')
        <div class="w-full h-full grid grid-cols-2 gap-x-1.5 gap-y-2">
            @for($i = 0; $i < 4; $i++)
                <div class="flex flex-col gap-1 pb-1 border-b border-zinc-300 dark:border-zinc-600">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600 aspect-square"></div>
                    <div class="h-1 w-2/3 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('list')
        <div class="w-full h-full flex flex-col gap-1.5">
            @for($i = 0; $i < 3; $i++)
                <div class="flex-1 rounded bg-white dark:bg-zinc-700 p-1 flex items-center gap-1.5">
                    <div class="h-full aspect-square rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="flex-1 flex flex-col gap-1">
                        <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                        <div class="h-1 w-1/2 rounded-full bg-emerald-500"></div>
                    </div>
                </div>
            @endfor
        </div>
        @break

    @case('compact')
        <div class="w-full h-full grid grid-cols-3 gap-1">
            @for($i = 0; $i < 6; $i++)
                <div class="rounded bg-white dark:bg-zinc-700 p-0.5 flex flex-col gap-0.5">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('scrim')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="relative rounded overflow-hidden bg-zinc-300 dark:bg-zinc-600">
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 to-transparent"></div>
                    <div class="absolute bottom-1 left-1 right-1 flex flex-col gap-0.5">
                        <div class="h-1 w-2/3 rounded-full bg-white/80"></div>
                        <div class="h-1 w-1/2 rounded-full bg-emerald-400"></div>
                    </div>
                </div>
            @endfor
        </div>
        @break

    @case('feature')
        <div class="w-full h-full flex flex-col gap-1.5">
            <div class="flex-[1.4] rounded bg-white dark:bg-zinc-700 p-1.5 flex gap-1.5">
                <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="flex-1 flex flex-col justify-center gap-1">
                    <div class="h-1.5 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1.5 w-8 rounded-full bg-emerald-500 mt-0.5"></div>
                </div>
            </div>
            <div class="flex-1 grid grid-cols-3 gap-1">
                @for($i = 0; $i < 3; $i++)
                    <div class="rounded-sm bg-white dark:bg-zinc-700"></div>
                @endfor
            </div>
        </div>
        @break

    @case('masonry')
        <div class="w-full h-full grid grid-cols-3 gap-1 items-start">
            <div class="h-8 rounded bg-white dark:bg-zinc-700"></div>
            <div class="h-12 rounded bg-white dark:bg-zinc-700"></div>
            <div class="h-6 rounded bg-white dark:bg-zinc-700"></div>
            <div class="h-6 rounded bg-white dark:bg-zinc-700"></div>
            <div class="h-8 rounded bg-white dark:bg-zinc-700"></div>
            <div class="h-10 rounded bg-white dark:bg-zinc-700"></div>
        </div>
        @break

    @case('outline')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded border border-zinc-400 dark:border-zinc-500 p-1 flex flex-col gap-1">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('noir')
        <div class="w-full h-full rounded bg-zinc-950 p-1.5 grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded bg-zinc-900 p-1 flex flex-col gap-1 ring-1 ring-white/10">
                    <div class="flex-1 rounded-sm bg-zinc-700"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('promo')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="relative rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-1">
                    @if($i === 0)
                        <div class="absolute top-0.5 left-0.5 h-1.5 w-4 rounded-sm bg-red-600"></div>
                    @endif
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1.5 w-1/2 rounded-full bg-emerald-500"></div>
                </div>
            @endfor
        </div>
        @break
@endswitch
