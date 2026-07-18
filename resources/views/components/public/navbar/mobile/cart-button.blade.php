<button
    @click="$dispatch('open-cart'); mobileMenuOpen = false"
    class="w-full relative px-4 py-3 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-md shadow-zinc-900/20 dark:shadow-black/30 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors duration-200 flex items-center justify-center gap-2 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white focus-visible:ring-offset-2"
    aria-label="{{ __('Shopping Cart') }}"
>
    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"></path>
    </svg>
    <span class="text-sm font-bold">{{ __('Cart') }}</span>
    @if($cartItemCount > 0)
        <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-[10px] font-bold text-white tabular-nums">
            {{ $cartItemCount > 99 ? '99+' : $cartItemCount }}
        </span>
    @endif
</button>
