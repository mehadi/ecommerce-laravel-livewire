<div class="hidden sm:flex items-center h-10 sm:h-11 rounded-full bg-zinc-100 dark:bg-white/[0.07] p-1">
    <a href="{{ route('change-language', 'en') }}" wire:navigate class="flex items-center h-full px-3 sm:px-3.5 rounded-full text-xs font-bold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ app()->getLocale() === 'en' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
        {{ __('EN') }}
    </a>
    <a href="{{ route('change-language', 'bn') }}" wire:navigate class="flex items-center h-full px-3 sm:px-3.5 rounded-full text-xs font-bold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ app()->getLocale() === 'bn' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
        {{ __('BN') }}
    </a>
</div>
