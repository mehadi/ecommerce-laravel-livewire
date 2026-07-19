@props([
    'cardKey',
    'card',
    'isCustomizing' => false,
    'empty' => false,
])

<div
    data-card-key="{{ $cardKey }}"
    data-dashboard-chart-card="{{ $cardKey }}"
    wire:key="chart-card-{{ $cardKey }}"
    @class([
        'dashboard-chart-card group relative rounded-2xl border bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-zinc-900',
        'border-zinc-200 dark:border-zinc-800' => ! $isCustomizing,
        'cursor-grab border-blue-300 ring-2 ring-blue-200/50 hover:border-blue-400 hover:ring-blue-300/50 dark:border-blue-700 dark:ring-blue-800/30' => $isCustomizing,
    ])
>
    @if($isCustomizing)
        <div class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white shadow-lg">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </div>
        <div class="mb-3 flex items-center justify-between border-b border-zinc-200/50 pb-3 dark:border-zinc-700/50">
            <div class="flex items-center gap-2 text-xs font-medium text-blue-600 dark:text-blue-400">
                <svg class="h-4 w-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="font-semibold">{{ __('Drag to reorder') }}</span>
            </div>
            <button
                type="button"
                wire:click.stop="toggleCardVisibility('{{ $cardKey }}')"
                class="no-drag rounded-lg p-1.5 text-zinc-500 transition-all hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                title="{{ __('Toggle Visibility') }}"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
    @endif

    <div class="mb-5 flex items-center gap-2.5">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 ring-1 ring-inset ring-black/5 dark:bg-white/5 dark:text-zinc-400 dark:ring-white/10">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </span>
        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $card['title'] }}</h3>
    </div>
    @if($empty)
        <div class="flex h-64 flex-col items-center justify-center gap-2 text-zinc-400 dark:text-zinc-500">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-sm font-medium">{{ __('No data for this period') }}</p>
        </div>
    @else
        <div class="h-64">
            <canvas id="{{ $cardKey }}Chart"></canvas>
        </div>
    @endif
</div>

