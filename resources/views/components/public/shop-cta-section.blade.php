<section
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    class="py-4 sm:py-5"
    aria-label="{{ __('Browse the shop') }}"
>
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div
            class="relative overflow-hidden rounded-[2rem] bg-white dark:bg-zinc-900 p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] transition-all duration-700 motion-reduce:transition-none"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
        >
            {{-- Decorative layers --}}
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.12),transparent_50%)]"></div>
            <div class="pointer-events-none absolute -top-20 -right-20 w-72 h-72 bg-lime-300/15 rounded-full blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-16 w-72 h-72 bg-teal-300/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-2xl mx-auto text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-5">
                    {{ __('Start Shopping') }}
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-tight text-zinc-900 dark:text-white mb-4 sm:mb-5 tracking-tight text-balance">
                    {{ __('Discover Something You\'ll Love') }}
                </h2>
                <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 mb-8 sm:mb-10 leading-relaxed max-w-xl mx-auto">
                    {{ __('Explore our full collection with free delivery, secure payment, and easy returns') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a
                        href="{{ route('shop') }}"
                        wire:navigate
                        class="group btn-tenant-primary text-white px-8 sm:px-10 py-4 rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"></path>
                        </svg>
                        {{ __('Browse Shop') }}
                    </a>
                    <a
                        href="{{ route('categories.index') }}"
                        wire:navigate
                        class="bg-amber-400 text-zinc-900 px-8 sm:px-10 py-4 rounded-full text-base sm:text-lg font-bold hover:bg-amber-300 transition-all duration-300 shadow-md shadow-amber-500/25 hover:shadow-lg hover:shadow-amber-500/30 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path>
                        </svg>
                        {{ __('Browse Categories') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
