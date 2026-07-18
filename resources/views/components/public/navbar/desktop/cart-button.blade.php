<button
    @click="$dispatch('open-cart')"
    class="relative flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white dark:bg-zinc-800 ring-1 ring-zinc-900/[0.07] dark:ring-white/[0.09] shadow-[0_1px_3px_0_rgb(16_24_40_/_0.08)] text-zinc-800 dark:text-zinc-100 hover:shadow-md hover:-translate-y-px motion-reduce:transform-none transition-all duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
    aria-label="{{ __('Shopping Cart') }}"
>
    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"></path>
    </svg>
    @if($cartItemCount > 0)
        <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[19px] h-[19px] px-1 rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-zinc-900 tabular-nums">
            {{ $cartItemCount > 99 ? '99+' : $cartItemCount }}
        </span>
    @endif
</button>
