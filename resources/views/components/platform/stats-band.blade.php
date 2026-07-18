@php
    $stats = [
        [
            'title' => __('Free to start'),
            'subtitle' => __('No cost on Starter'),
            'color' => 'emerald',
            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'title' => __('Custom domains'),
            'subtitle' => __('Bring your own brand'),
            'color' => 'blue',
            'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
        ],
        [
            'title' => __('Role-based access'),
            'subtitle' => __('Invite your whole team'),
            'color' => 'amber',
            'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 2a4 4 0 10-8 0',
        ],
        [
            'title' => __('Built-in analytics'),
            'subtitle' => __('Know what sells'),
            'color' => 'rose',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
    ];
    $statColors = [
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 ring-emerald-600/10 dark:ring-emerald-500/20',
        'blue' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 ring-blue-600/10 dark:ring-blue-500/20',
        'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 ring-amber-600/10 dark:ring-amber-500/20',
        'rose' => 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 ring-rose-600/10 dark:ring-rose-500/20',
    ];
@endphp

<section
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    class="pt-8 sm:pt-10 pb-4 sm:pb-5"
    aria-label="{{ __('What you get') }}"
>
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-8">
            @foreach($stats as $index => $stat)
                <div
                    class="flex flex-col items-center text-center gap-3 transition-all duration-700 motion-reduce:transition-none"
                    style="transition-delay: {{ $index * 75 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
                    <div class="w-12 h-12 rounded-full flex items-center justify-center ring-1 {{ $statColors[$stat['color']] }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $stat['title'] }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $stat['subtitle'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </div>
</section>
