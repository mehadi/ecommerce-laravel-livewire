{{-- Miniature schematic of a hero variant. Expects $variant (a key from HeroVariants::keys()). --}}
@switch($variant)
    @case('bento')
        <div class="w-full h-full flex gap-1">
            <div class="flex-[3] flex flex-col gap-1 min-w-0">
                <div class="flex-[2] rounded-md bg-white dark:bg-zinc-700 p-2 flex gap-2">
                    <div class="flex-1 flex flex-col gap-1.5 justify-center min-w-0">
                        <div class="h-1.5 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                        <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                        <div class="h-2 w-8 rounded-full bg-emerald-500"></div>
                    </div>
                    <div class="w-1/3 rounded bg-zinc-200 dark:bg-zinc-600"></div>
                </div>
                <div class="flex-1 flex gap-1">
                    <div class="flex-1 rounded-md bg-white dark:bg-zinc-700"></div>
                    <div class="flex-1 rounded-md bg-white dark:bg-zinc-700"></div>
                    <div class="flex-[1.5] rounded-md bg-white dark:bg-zinc-700"></div>
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-1">
                <div class="flex-1 rounded-md bg-white dark:bg-zinc-700"></div>
                <div class="flex-1 rounded-md bg-white dark:bg-zinc-700"></div>
                <div class="flex-[1.5] rounded-md bg-zinc-300 dark:bg-zinc-600"></div>
            </div>
        </div>
        @break

    @case('classic')
        <div class="w-full h-full flex items-center gap-3">
            <div class="flex-1 flex flex-col gap-1.5 min-w-0">
                <div class="h-1 w-8 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-2 w-full rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="h-2 w-3/4 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="h-1 w-2/3 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="flex gap-1 mt-0.5">
                    <div class="h-2.5 w-9 rounded-full bg-emerald-500"></div>
                    <div class="h-2.5 w-9 rounded-full bg-white dark:bg-zinc-600 border border-zinc-300 dark:border-zinc-500"></div>
                </div>
            </div>
            <div class="w-2/5 self-stretch rounded-lg bg-white dark:bg-zinc-700 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
        </div>
        @break

    @case('centered')
        <div class="w-full h-full flex flex-col items-center gap-1.5">
            <div class="h-1 w-10 rounded-full bg-zinc-300 dark:bg-zinc-500 mt-1"></div>
            <div class="h-2 w-3/4 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
            <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            <div class="h-2.5 w-10 rounded-full bg-emerald-500"></div>
            <div class="flex-1 w-4/5 rounded-lg bg-white dark:bg-zinc-700 mt-1 flex items-center justify-center">
                <div class="w-7 h-7 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
        </div>
        @break

    @case('gradient')
        <div class="w-full h-full rounded-lg bg-gradient-to-br from-slate-800 via-zinc-900 to-black p-2.5 flex items-center gap-2 overflow-hidden">
            <div class="flex-1 flex flex-col gap-1.5 min-w-0">
                <div class="h-1 w-8 rounded-full bg-white/30"></div>
                <div class="h-2 w-full rounded-full bg-white/70"></div>
                <div class="h-2 w-2/3 rounded-full bg-white/70"></div>
                <div class="h-2.5 w-9 rounded-full bg-emerald-500 mt-0.5"></div>
            </div>
            <div class="relative w-1/3 self-stretch flex items-center justify-center">
                <div class="absolute w-12 h-12 rounded-full bg-emerald-500/40 blur-md"></div>
                <div class="relative w-7 h-7 rounded-full bg-white/20 ring-1 ring-white/30"></div>
            </div>
        </div>
        @break

    @case('overlay')
        <div class="w-full h-full rounded-lg bg-gradient-to-t from-zinc-800 via-zinc-500 to-zinc-400 dark:from-black dark:via-zinc-700 dark:to-zinc-600 p-2.5 flex flex-col justify-end gap-1.5">
            <div class="h-1 w-8 rounded-full bg-white/40"></div>
            <div class="h-2 w-2/3 rounded-full bg-white/90"></div>
            <div class="h-1 w-1/2 rounded-full bg-white/50"></div>
            <div class="h-2.5 w-9 rounded-full bg-emerald-500"></div>
        </div>
        @break

    @case('split')
        <div class="w-full h-full flex rounded-lg overflow-hidden">
            <div class="flex-1 bg-slate-800 p-2.5 flex flex-col justify-center gap-1.5">
                <div class="h-1 w-8 rounded-full bg-white/30"></div>
                <div class="h-2 w-full rounded-full bg-white/70"></div>
                <div class="h-2 w-2/3 rounded-full bg-white/70"></div>
                <div class="h-2.5 w-9 rounded-full bg-emerald-500 mt-0.5"></div>
            </div>
            <div class="flex-1 bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-zinc-400/60 dark:bg-zinc-500/60"></div>
            </div>
        </div>
        @break

    @case('showcase')
        <div class="w-full h-full rounded-lg bg-white dark:bg-zinc-700 p-2.5 flex flex-col items-center gap-1.5">
            <div class="h-2 w-2/3 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
            <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
            <div class="relative flex-1 w-full flex items-end justify-center pb-1">
                <div class="absolute bottom-0 w-14 h-14 rounded-full bg-zinc-100 dark:bg-zinc-600"></div>
                <div class="relative w-6 h-9 rounded bg-zinc-300 dark:bg-zinc-500 mb-2"></div>
                <div class="absolute left-1 top-1 h-3 w-8 rounded bg-zinc-100 dark:bg-zinc-600 shadow-sm"></div>
                <div class="absolute right-1 top-4 h-3 w-8 rounded bg-zinc-100 dark:bg-zinc-600 shadow-sm"></div>
            </div>
            <div class="h-2.5 w-10 rounded-full bg-emerald-500"></div>
        </div>
        @break

    @case('editorial')
        <div class="w-full h-full rounded-lg bg-white dark:bg-zinc-700 p-2.5 flex flex-col gap-1.5">
            <div class="flex items-center gap-1.5">
                <div class="h-1 w-8 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-500"></div>
            </div>
            <div class="h-3 w-full rounded bg-zinc-800 dark:bg-zinc-300"></div>
            <div class="h-3 w-3/4 rounded bg-zinc-800 dark:bg-zinc-300"></div>
            <div class="flex-1 flex gap-2 pt-1 border-t border-zinc-200 dark:border-zinc-500">
                <div class="flex-1 flex flex-col gap-1">
                    <div class="h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
                    <div class="h-1 w-3/4 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
                    <div class="h-1.5 w-8 rounded-full bg-emerald-500 mt-auto mb-0.5"></div>
                </div>
                <div class="w-1/2 rounded bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
        </div>
        @break

    @case('collage')
        <div class="w-full h-full flex items-center gap-3">
            <div class="flex-1 flex flex-col gap-1.5 min-w-0">
                <div class="h-1 w-8 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-2 w-full rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="h-2 w-2/3 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="h-2.5 w-9 rounded-full bg-emerald-500 mt-0.5"></div>
            </div>
            <div class="relative w-2/5 self-stretch">
                <div class="absolute top-1 left-0 w-8 h-10 rounded-md bg-white dark:bg-zinc-600 shadow -rotate-6"></div>
                <div class="absolute bottom-1 right-0 w-8 h-10 rounded-md bg-white dark:bg-zinc-600 shadow rotate-6"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-12 rounded-md bg-zinc-300 dark:bg-zinc-500 shadow-md rotate-2"></div>
            </div>
        </div>
        @break

    @case('spotlight')
        <div class="relative w-full h-full rounded-lg bg-zinc-950 p-2.5 flex flex-col items-center gap-1.5 overflow-hidden">
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-20 h-20 rounded-full bg-emerald-500/25 blur-lg"></div>
            <div class="relative h-2 w-2/3 rounded-full bg-white/80"></div>
            <div class="relative h-1 w-1/2 rounded-full bg-white/40"></div>
            <div class="relative flex-1 flex items-center justify-center">
                <div class="absolute w-10 h-10 rounded-full bg-emerald-500/30 blur-md"></div>
                <div class="relative w-5 h-7 rounded bg-white/25 ring-1 ring-white/30"></div>
            </div>
            <div class="relative h-2.5 w-10 rounded-full bg-emerald-500"></div>
        </div>
        @break
@endswitch
