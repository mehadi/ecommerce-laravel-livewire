<a href="/" class="flex items-center gap-2 sm:gap-2.5 shrink-0 group rounded-full py-1 pr-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
    @if($siteLogo)
        <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" class="h-8 sm:h-9 w-auto rounded-xl transition-transform duration-300 group-hover:scale-[1.04] motion-reduce:transform-none">
    @else
        <span class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-[0.65rem] bg-zinc-900 dark:bg-white shadow-sm transition-transform duration-300 group-hover:scale-[1.04] motion-reduce:transform-none">
            <span class="text-white dark:text-zinc-900 font-display font-extrabold text-base sm:text-lg leading-none">{{ substr($siteName, 0, 1) }}</span>
        </span>
    @endif
    <span class="font-display text-base sm:text-lg font-bold tracking-tight text-zinc-900 dark:text-white whitespace-nowrap">{{ $siteName }}<span aria-hidden="true">.</span></span>
</a>
