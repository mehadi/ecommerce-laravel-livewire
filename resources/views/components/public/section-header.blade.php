@props([
    'eyebrow',
    'title',
    'viewAllUrl' => null,
    // sr-only suffix so the desktop link's accessible name says what it goes to
    // (e.g. "View All Products") without changing the visible "View All" text.
    'viewAllContext' => null,
    'color' => 'emerald',
])

@php
    $eyebrowColors = [
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ring-emerald-600/10 dark:ring-emerald-500/20',
        'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ring-amber-600/10 dark:ring-amber-500/20',
    ];
@endphp

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 sm:mb-12">
    <div class="max-w-2xl">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $eyebrowColors[$color] ?? $eyebrowColors['emerald'] }} text-xs font-semibold uppercase tracking-widest ring-1 mb-4">
            {{ $eyebrow }}
        </span>
        <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
            {{ $title }}
        </h2>
    </div>
    @if($viewAllUrl)
        <a
            href="{{ $viewAllUrl }}"
            wire:navigate
            class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors duration-200"
        >
            {{ __('View All') }}
            @if($viewAllContext)
                <span class="sr-only">{{ $viewAllContext }}</span>
            @endif
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </a>
    @endif
</div>
