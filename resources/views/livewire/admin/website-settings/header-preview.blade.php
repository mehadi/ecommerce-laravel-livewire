{{-- Miniature schematic of a header variant. Expects $variant (a key from NavbarVariants::keys()). --}}
@switch($variant)
    @case('classic')
        <div class="w-full h-full rounded bg-zinc-100 dark:bg-zinc-800 p-2 flex flex-col justify-center gap-2">
            <div class="rounded-full bg-white dark:bg-zinc-700 shadow-sm h-5 flex items-center gap-1.5 px-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-violet-500 shrink-0"></div>
                <div class="flex-1 h-1 rounded-full bg-zinc-200 dark:bg-zinc-600 mx-1"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-zinc-200 dark:bg-zinc-600 shrink-0"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-zinc-300 dark:bg-zinc-500 shrink-0"></div>
            </div>
        </div>
        @break

    @case('minimal')
        <div class="w-full h-full rounded bg-white dark:bg-zinc-700 p-2 flex flex-col justify-center gap-1.5">
            <div class="border-b border-zinc-200 dark:border-zinc-600 pb-1.5 flex items-center justify-between">
                <div class="w-3 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="flex gap-1.5">
                    @for($i = 0; $i < 3; $i++)
                        <div class="h-1 w-3 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
                    @endfor
                </div>
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            </div>
        </div>
        @break

    @case('centered')
        <div class="w-full h-full rounded bg-white dark:bg-zinc-700 p-2 flex flex-col justify-center gap-1.5">
            <div class="flex justify-between">
                <div class="h-0.5 w-4 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
                <div class="h-0.5 w-4 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
            </div>
            <div class="border-y border-zinc-100 dark:border-zinc-600 py-1.5 flex items-center justify-between">
                <div class="h-1 w-3 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="w-3.5 h-1.5 rounded bg-violet-500"></div>
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            </div>
        </div>
        @break

    @case('transparent')
        <div class="w-full h-full rounded bg-gradient-to-b from-zinc-700 to-zinc-400 dark:from-zinc-900 dark:to-zinc-600 p-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-2.5 h-2.5 rounded bg-white/90"></div>
                <div class="flex gap-1.5">
                    @for($i = 0; $i < 3; $i++)
                        <div class="h-1 w-3 rounded-full bg-white/70"></div>
                    @endfor
                </div>
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-white/70"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white/70"></div>
                </div>
            </div>
            <div class="h-1 w-1/3 rounded-full bg-white/40"></div>
        </div>
        @break

    @case('bold')
        <div class="w-full h-full rounded bg-zinc-950 p-2 flex flex-col gap-1.5 border-t-2 border-violet-500">
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded bg-violet-500 shrink-0"></div>
                <div class="flex-1 h-2 rounded-full bg-white/10"></div>
                <div class="w-3 h-2 rounded-full bg-violet-500 shrink-0"></div>
            </div>
            <div class="flex gap-1.5 border-t border-white/10 pt-1.5">
                @for($i = 0; $i < 4; $i++)
                    <div class="h-1 w-3 rounded-full bg-white/30"></div>
                @endfor
            </div>
        </div>
        @break
@endswitch
