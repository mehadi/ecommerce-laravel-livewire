<a href="/" wire:navigate class="flex items-center justify-center gap-2 bg-white dark:bg-zinc-900 rounded-[1.75rem] p-3 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)]">
    @if($siteLogo)
        <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto rounded-xl">
    @else
        <span class="flex items-center justify-center w-8 h-8 rounded-[0.65rem] bg-zinc-900 dark:bg-white shadow-sm">
            <span class="text-white dark:text-zinc-900 font-display font-extrabold text-base leading-none">{{ substr($siteName, 0, 1) }}</span>
        </span>
    @endif
    <span class="font-display text-base font-bold tracking-tight text-zinc-900 dark:text-white whitespace-nowrap">{{ $siteName }}<span aria-hidden="true">.</span></span>
</a>
