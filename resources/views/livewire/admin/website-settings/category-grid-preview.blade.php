{{-- Miniature schematic of a category-grid variant. Expects $variant (a key from CategoryGridVariants::keys()). --}}
@switch($variant)
    @case('cards')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-1">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="flex gap-0.5">
                        <div class="h-1 w-3 rounded-full bg-emerald-500"></div>
                        <div class="h-1 w-3 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                    </div>
                </div>
            @endfor
        </div>
        @break

    @case('overlay')
        <div class="w-full h-full grid grid-cols-3 gap-1.5">
            @for($i = 0; $i < 3; $i++)
                <div class="relative rounded overflow-hidden bg-zinc-300 dark:bg-zinc-600">
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 to-transparent"></div>
                    <div class="absolute bottom-1 left-1 right-1 flex flex-col gap-0.5">
                        <div class="h-1 w-1/2 rounded-full bg-white/60"></div>
                        <div class="h-1 w-3/4 rounded-full bg-white/90"></div>
                    </div>
                </div>
            @endfor
        </div>
        @break

    @case('minimal')
        <div class="w-full h-full grid grid-cols-3 gap-x-1.5 gap-y-2">
            @for($i = 0; $i < 3; $i++)
                <div class="flex flex-col gap-1 pb-1 border-b border-zinc-300 dark:border-zinc-600">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600 aspect-square"></div>
                    <div class="h-1 w-2/3 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('circles')
        <div class="w-full h-full grid grid-cols-4 gap-1.5 place-items-center">
            @for($i = 0; $i < 8; $i++)
                <div class="flex flex-col items-center gap-0.5">
                    <div class="w-5 h-5 rounded-full bg-zinc-300 dark:bg-zinc-600 ring-1 ring-zinc-400/40 dark:ring-zinc-500/40"></div>
                    <div class="h-0.5 w-4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
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
                        <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                    </div>
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('banner')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="relative rounded overflow-hidden bg-zinc-300 dark:bg-zinc-600">
                    <div class="absolute inset-0 bg-gradient-to-r from-zinc-900/80 to-transparent"></div>
                    <div class="absolute inset-y-0 left-1 flex flex-col justify-center gap-0.5 w-1/2">
                        <div class="h-1 w-1/2 rounded-full bg-emerald-400"></div>
                        <div class="h-1 w-full rounded-full bg-white/90"></div>
                    </div>
                </div>
            @endfor
        </div>
        @break

    @case('compact')
        <div class="w-full h-full grid grid-cols-4 gap-1">
            @for($i = 0; $i < 8; $i++)
                <div class="rounded bg-white dark:bg-zinc-700 p-0.5 flex flex-col gap-0.5">
                    <div class="flex-1 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('showcase')
        <div class="w-full h-full flex flex-col gap-1.5">
            <div class="flex-[1.4] relative rounded overflow-hidden bg-zinc-300 dark:bg-zinc-600">
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 to-transparent"></div>
                <div class="absolute bottom-1 left-1 flex flex-col gap-0.5 w-1/2">
                    <div class="h-1 w-1/3 rounded-full bg-emerald-400"></div>
                    <div class="h-1.5 w-3/4 rounded-full bg-white/90"></div>
                </div>
            </div>
            <div class="flex-1 grid grid-cols-4 gap-1">
                @for($i = 0; $i < 4; $i++)
                    <div class="rounded-sm bg-white dark:bg-zinc-700"></div>
                @endfor
            </div>
        </div>
        @break

    @case('split')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded bg-white dark:bg-zinc-700 flex overflow-hidden">
                    <div class="w-2/5 bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="flex-1 p-1 flex flex-col justify-center gap-0.5">
                        <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                        <div class="h-1 w-1/2 rounded-full bg-emerald-500"></div>
                    </div>
                </div>
            @endfor
        </div>
        @break

    @case('noir')
        <div class="w-full h-full rounded bg-zinc-950 p-1.5 grid grid-cols-3 gap-1.5">
            @for($i = 0; $i < 3; $i++)
                <div class="rounded bg-zinc-900 p-1 flex flex-col gap-1 ring-1 ring-white/10">
                    <div class="flex-1 rounded-sm bg-zinc-700"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break
@endswitch
