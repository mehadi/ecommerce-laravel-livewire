@props(['mode'])

<div class="relative h-16 w-full overflow-hidden rounded-md bg-neutral-100 ring-1 ring-neutral-200 dark:bg-white/5 dark:ring-white/10">
    <div class="absolute inset-0 bg-neutral-900/10 dark:bg-black/40"></div>

    @if ($mode === 'panel')
        <div class="absolute inset-y-0 right-0 w-5 bg-white shadow-sm dark:bg-zinc-700"></div>
    @else
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="h-9 w-14 rounded bg-white shadow-sm dark:bg-zinc-700"></div>
        </div>
    @endif
</div>
