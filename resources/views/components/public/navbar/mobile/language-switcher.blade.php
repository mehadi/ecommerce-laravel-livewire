<div class="sm:hidden flex items-center justify-center w-full rounded-full bg-white dark:bg-zinc-900 p-1 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)]">
    <a href="{{ route('change-language', 'en') }}" wire:navigate class="flex-1 flex items-center justify-center px-4 py-2.5 rounded-full text-xs font-bold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ app()->getLocale() === 'en' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900' : 'text-zinc-500 dark:text-zinc-400' }}">
        {{ __('EN') }}
    </a>
    <a href="{{ route('change-language', 'bn') }}" wire:navigate class="flex-1 flex items-center justify-center px-4 py-2.5 rounded-full text-xs font-bold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ app()->getLocale() === 'bn' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900' : 'text-zinc-500 dark:text-zinc-400' }}">
        {{ __('BN') }}
    </a>
</div>
