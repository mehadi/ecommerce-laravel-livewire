<section
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    class="pt-8 sm:pt-10 pb-4 sm:pb-5"
    aria-label="{{ __('Why shop with us') }}"
>
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-8">
            @php
                $badges = [
                    [
                        'title' => __('Premium Quality'),
                        'subtitle' => __('100% Natural'),
                        'color' => 'emerald',
                        'delay' => '',
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    ],
                    [
                        'title' => __('Satisfaction'),
                        'subtitle' => __('30-Day Guarantee'),
                        'color' => 'amber',
                        'delay' => 'delay-75',
                        'icon' => 'M5 13l4 4L19 7',
                    ],
                    [
                        'title' => __('Fast Delivery'),
                        'subtitle' => __('2-3 Business Days'),
                        'color' => 'teal',
                        'delay' => 'delay-150',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'title' => __('Secure Payment'),
                        'subtitle' => __('COD Available'),
                        'color' => 'rose',
                        'delay' => 'delay-200',
                        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ];
                $badgeColors = [
                    'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 ring-emerald-600/10 dark:ring-emerald-500/20',
                    'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 ring-amber-600/10 dark:ring-amber-500/20',
                    'teal' => 'bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 ring-teal-600/10 dark:ring-teal-500/20',
                    'rose' => 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 ring-rose-600/10 dark:ring-rose-500/20',
                ];
            @endphp

            @foreach($badges as $badge)
                <div
                    class="flex flex-col items-center text-center gap-3 transition-all duration-700 {{ $badge['delay'] }} motion-reduce:transition-none"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
                    <div class="w-12 h-12 rounded-full flex items-center justify-center ring-1 {{ $badgeColors[$badge['color']] }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $badge['icon'] }}"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $badge['title'] }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $badge['subtitle'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </div>
</section>
