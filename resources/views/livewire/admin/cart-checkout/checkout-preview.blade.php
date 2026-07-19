{{-- Miniature schematic of a checkout-content variant. Expects $variant (a key from CheckoutVariants::keys()). --}}
@switch($variant)
    @case('classic')
        <div class="w-full h-full flex flex-col gap-1.5">
            <div class="flex-1 rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-0.5 justify-center">
                <div class="h-1 w-1/2 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-1 w-3/4 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
            <div class="flex-[1.4] rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-0.5 justify-center">
                <div class="h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-1.5 w-full rounded bg-emerald-500 mt-0.5"></div>
            </div>
        </div>
        @break

    @case('split')
        <div class="w-full h-full grid grid-cols-2 gap-1.5">
            <div class="rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-0.5 justify-center">
                <div class="h-1 w-2/3 rounded-full bg-zinc-300 dark:bg-zinc-500"></div>
                <div class="h-1 w-3/4 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-1 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            </div>
            <div class="rounded bg-white dark:bg-zinc-700 p-1 flex flex-col gap-0.5 justify-center">
                <div class="h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                <div class="h-1.5 w-full rounded bg-emerald-500 mt-0.5"></div>
            </div>
        </div>
        @break

    @case('compact')
        <div class="w-full h-full flex flex-col gap-1">
            <div class="h-2.5 rounded bg-white dark:bg-zinc-700"></div>
            @for($i = 0; $i < 3; $i++)
                <div class="h-1 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
            @endfor
            <div class="h-1.5 rounded bg-emerald-500 mt-0.5"></div>
        </div>
        @break

    @case('minimal')
        <div class="w-full h-full flex flex-col gap-1.5">
            @for($i = 0; $i < 4; $i++)
                <div class="h-1 rounded-full bg-zinc-300 dark:bg-zinc-500 border-b border-zinc-200 dark:border-zinc-700 pb-1"></div>
            @endfor
            <div class="h-1.5 rounded bg-zinc-800 dark:bg-white mt-0.5"></div>
        </div>
        @break

    @case('steps')
        <div class="w-full h-full flex flex-col gap-1">
            @for($i = 1; $i <= 3; $i++)
                <div class="flex items-center gap-1">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></div>
                    <div class="h-1 flex-1 rounded-full bg-zinc-200 dark:bg-zinc-600"></div>
                </div>
            @endfor
        </div>
        @break

    @default
        <div class="w-full h-full flex items-center justify-center">
            <div class="h-2 w-2/3 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
        </div>
@endswitch
