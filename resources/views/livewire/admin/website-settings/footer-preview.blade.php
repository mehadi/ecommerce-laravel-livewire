{{-- Miniature schematic of a footer variant. Expects $variant (a key from FooterVariants::keys()). --}}
@switch($variant)
    @case('classic')
        <div class="w-full h-full rounded bg-white dark:bg-zinc-700 p-2 flex flex-col justify-between">
            <div class="grid grid-cols-4 gap-2 flex-1">
                <div class="flex flex-col gap-1">
                    <div class="w-3 h-3 rounded bg-emerald-500"></div>
                    <div class="h-1 w-full rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="flex gap-0.5 mt-0.5">
                        @for($i = 0; $i < 3; $i++)
                            <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                        @endfor
                    </div>
                </div>
                @for($col = 0; $col < 2; $col++)
                    <div class="flex flex-col gap-1">
                        <div class="h-1 w-2/3 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                        @for($i = 0; $i < 3; $i++)
                            <div class="h-1 w-3/4 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                        @endfor
                    </div>
                @endfor
                <div class="flex flex-col gap-1">
                    <div class="h-1 w-2/3 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                    @for($i = 0; $i < 2; $i++)
                        <div class="flex items-center gap-1">
                            <div class="w-2 h-2 rounded-full bg-emerald-200 dark:bg-emerald-800"></div>
                            <div class="h-1 flex-1 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="border-t border-zinc-200 dark:border-zinc-600 pt-1 flex justify-between items-center">
                <div class="h-1 w-1/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-1 w-1/5 rounded-full bg-emerald-400"></div>
            </div>
        </div>
        @break

    @case('centered')
        <div class="w-full h-full rounded bg-white dark:bg-zinc-700 p-2 flex flex-col items-center justify-between">
            <div class="flex flex-col items-center gap-1 flex-1 justify-center w-full">
                <div class="w-3.5 h-3.5 rounded bg-emerald-500"></div>
                <div class="h-1 w-1/4 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                <div class="h-1 w-2/5 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="flex gap-1 mt-0.5">
                    @for($i = 0; $i < 4; $i++)
                        <div class="h-1 w-4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    @endfor
                </div>
                <div class="flex gap-0.5 mt-0.5">
                    @for($i = 0; $i < 4; $i++)
                        <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    @endfor
                </div>
            </div>
            <div class="border-t border-zinc-200 dark:border-zinc-600 pt-1 w-full flex justify-center">
                <div class="h-1 w-1/3 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
            </div>
        </div>
        @break

    @case('noir')
        <div class="w-full h-full rounded bg-zinc-950 p-2 flex flex-col justify-between border-t-2 border-emerald-500">
            <div class="grid grid-cols-4 gap-2 flex-1">
                <div class="flex flex-col gap-1">
                    <div class="w-3 h-3 rounded bg-emerald-500"></div>
                    <div class="h-1 w-full rounded-full bg-zinc-700"></div>
                    <div class="flex gap-0.5 mt-0.5">
                        @for($i = 0; $i < 3; $i++)
                            <div class="w-1.5 h-1.5 rounded-full bg-zinc-700"></div>
                        @endfor
                    </div>
                </div>
                @for($col = 0; $col < 3; $col++)
                    <div class="flex flex-col gap-1">
                        <div class="h-1 w-2/3 rounded-full bg-emerald-500/70"></div>
                        @for($i = 0; $i < 3; $i++)
                            <div class="h-1 w-3/4 rounded-full bg-zinc-700"></div>
                        @endfor
                    </div>
                @endfor
            </div>
            <div class="border-t border-white/10 pt-1 flex justify-between items-center">
                <div class="h-1 w-1/4 rounded-full bg-zinc-600"></div>
                <div class="h-1 w-1/5 rounded-full bg-emerald-500/70"></div>
            </div>
        </div>
        @break

    @case('mega')
        <div class="w-full h-full rounded bg-zinc-100 dark:bg-zinc-800 p-2 flex flex-col gap-1.5">
            <div class="rounded bg-gradient-to-br from-emerald-500 to-green-600 p-1.5 flex items-center justify-between flex-[1.1]">
                <div class="flex flex-col gap-0.5 w-1/2">
                    <div class="h-1.5 w-full rounded-full bg-white/90"></div>
                    <div class="h-1 w-3/4 rounded-full bg-white/50"></div>
                </div>
                <div class="h-2.5 w-8 rounded-full bg-white"></div>
            </div>
            <div class="grid grid-cols-4 gap-1.5 flex-1">
                <div class="flex flex-col gap-0.5">
                    <div class="w-2.5 h-2.5 rounded bg-emerald-500"></div>
                    <div class="h-1 w-full rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                </div>
                @for($col = 0; $col < 3; $col++)
                    <div class="flex flex-col gap-0.5">
                        <div class="h-1 w-2/3 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                        <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                        <div class="h-1 w-1/2 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                    </div>
                @endfor
            </div>
            <div class="border-t border-zinc-200 dark:border-zinc-600 pt-1 flex justify-between items-center">
                <div class="h-1 w-1/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="flex gap-1">
                    @for($i = 0; $i < 3; $i++)
                        <div class="h-1 w-4 rounded-full bg-emerald-400"></div>
                    @endfor
                </div>
            </div>
        </div>
        @break

    @case('editorial')
        <div class="w-full h-full rounded bg-white dark:bg-zinc-700 p-2 flex flex-col justify-between">
            <div class="grid grid-cols-3 gap-2">
                <div class="flex flex-col gap-1">
                    <div class="h-1.5 w-full rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                    <div class="h-1.5 w-2/3 rounded-full bg-zinc-400 dark:bg-zinc-400"></div>
                </div>
                <div class="flex flex-col gap-1 items-center">
                    @for($i = 0; $i < 3; $i++)
                        <div class="h-1 w-2/3 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                    @endfor
                </div>
                <div class="flex flex-col gap-1 items-end">
                    @for($i = 0; $i < 2; $i++)
                        <div class="h-1 w-3/4 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                    @endfor
                </div>
            </div>
            <div class="border-y border-zinc-200 dark:border-zinc-600 py-1 my-1 flex items-center">
                <div class="h-3.5 w-4/5 rounded bg-zinc-800 dark:bg-zinc-300"></div>
            </div>
            <div class="flex justify-between items-center">
                <div class="h-1 w-1/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-1 w-1/5 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
        </div>
        @break
@endswitch
