{{-- Miniature schematic of a cart-content variant. Expects $variant (a key from CartVariants::keys()). --}}
@switch($variant)
    @case('classic')
        <div class="w-full h-full flex flex-col gap-1.5">
            @for($i = 0; $i < 2; $i++)
                <div class="flex-1 rounded bg-white dark:bg-zinc-700 p-1 flex items-center gap-1">
                    <div class="h-full aspect-square rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="flex-1 flex flex-col gap-0.5">
                        <div class="h-1 w-2/3 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                        <div class="h-1 w-1/3 rounded-full bg-emerald-500"></div>
                    </div>
                </div>
            @endfor
            <div class="rounded bg-emerald-500 h-2"></div>
        </div>
        @break

    @case('compact')
        <div class="w-full h-full flex flex-col gap-1">
            @for($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-1 pb-0.5 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="w-2 h-2 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                    <div class="h-0.5 flex-1 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                </div>
            @endfor
            <div class="rounded bg-zinc-800 dark:bg-white h-1.5 mt-0.5"></div>
        </div>
        @break

    @case('minimal')
        <div class="w-full h-full flex flex-col gap-1.5">
            @for($i = 0; $i < 3; $i++)
                <div class="flex justify-between items-center pb-1 border-b border-zinc-300 dark:border-zinc-600">
                    <div class="h-1 w-1/2 rounded-full bg-zinc-400 dark:bg-zinc-500"></div>
                    <div class="h-1 w-1/6 rounded-full bg-zinc-400 dark:bg-zinc-500"></div>
                </div>
            @endfor
        </div>
        @break

    @case('detailed')
        <div class="w-full h-full flex flex-col gap-1.5">
            <div class="flex-1 rounded bg-white dark:bg-zinc-700 p-1 flex gap-1">
                <div class="h-full aspect-square rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="flex-1 flex flex-col gap-0.5 justify-center">
                    <div class="h-1 w-3/4 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                    <div class="flex gap-0.5">
                        <div class="h-1 w-3 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
                        <div class="h-1 w-3 rounded-full bg-zinc-200 dark:bg-zinc-500"></div>
                    </div>
                </div>
            </div>
            <div class="rounded bg-emerald-500 h-2"></div>
        </div>
        @break

    @case('sidebar')
        <div class="w-full h-full flex flex-col gap-1.5">
            <div class="flex items-center gap-1">
                <div class="w-2.5 h-2.5 rounded-sm bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-1 flex-1 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
            </div>
            <div class="flex-1 rounded bg-zinc-950 p-1.5 flex flex-col gap-1 justify-end">
                <div class="h-1 w-2/3 rounded-full bg-zinc-600"></div>
                <div class="h-1.5 rounded bg-emerald-500"></div>
            </div>
        </div>
        @break

    @default
        <div class="w-full h-full flex items-center justify-center">
            <div class="h-2 w-2/3 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
        </div>
@endswitch
