@props([
    'cardKey',
    'card',
    'isCustomizing' => false,
    'value' => null,
    'subtitle' => null,
])

@php
    $variant = $card['style'] ?? 'default';

    $colorClasses = [
        'green' => ['bg' => 'bg-green-100 dark:bg-green-900/20', 'text' => 'text-green-600 dark:text-green-400'],
        'blue' => ['bg' => 'bg-blue-100 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900/20', 'text' => 'text-purple-600 dark:text-purple-400'],
        'indigo' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/20', 'text' => 'text-indigo-600 dark:text-indigo-400'],
        'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
        'amber' => ['bg' => 'bg-amber-100 dark:bg-amber-900/20', 'text' => 'text-amber-600 dark:text-amber-400'],
        'cyan' => ['bg' => 'bg-cyan-100 dark:bg-cyan-900/20', 'text' => 'text-cyan-600 dark:text-cyan-400'],
        'red' => ['bg' => 'bg-red-100 dark:bg-red-900/20', 'text' => 'text-red-600 dark:text-red-400'],
    ];

    $icons = [
        'currency' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'shopping' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
        'package' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
        'trending' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
        'payment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    ];

    $color = $colorClasses[$card['color'] ?? 'blue'] ?? $colorClasses['blue'];
    $iconPath = $icons[$card['icon'] ?? 'chart'] ?? $icons['chart'];

    $isGrowthMetric = $cardKey === 'revenue_growth';
    $valueClass = $isGrowthMetric
        ? ($value >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400')
        : 'text-neutral-900 dark:text-white';

    $formattedValue = $isGrowthMetric
        ? ($value >= 0 ? '+' : '').number_format($value, 1).'%'
        : (in_array($cardKey, ['total_revenue', 'average_order_value', 'outstanding_payments', 'average_customer_value', 'total_discounts'], true)
            ? '৳'.number_format((float) $value, 2)
            : ($cardKey === 'cancellation_rate'
                ? number_format((float) $value, 1).'%'
                : number_format((float) $value, $cardKey === 'repeat_customer_rate' ? 1 : 0)));

    $paddingClass = $variant === 'minimal' ? 'p-4' : 'p-6';
    $controlSpacing = $variant === 'minimal' ? 'mb-2 pb-2' : 'mb-3 pb-3';
    $dragIconSize = $variant === 'minimal' ? 'h-3.5 w-3.5' : 'h-4 w-4';
@endphp

<div
    data-card-key="{{ $cardKey }}"
    wire:key="metric-card-{{ $cardKey }}"
    @class([
        "dashboard-metric-card group relative rounded-xl border bg-white shadow-sm transition-all duration-200 dark:bg-zinc-900 {$paddingClass}",
        'border-neutral-200 dark:border-neutral-700' => ! $isCustomizing,
        'cursor-grab border-blue-300 ring-2 ring-blue-200/50 hover:border-blue-400 hover:ring-blue-300/50 dark:border-blue-700 dark:ring-blue-800/30' => $isCustomizing,
    ])
>
    @if($isCustomizing)
        <div class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white shadow-lg">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </div>

        <div class="{{ $controlSpacing }} flex items-center justify-between border-b border-zinc-200/50 dark:border-zinc-700/50">
            <div class="flex items-center gap-1.5 text-xs font-medium text-blue-600 dark:text-blue-400">
                <svg class="{{ $dragIconSize }} animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-xs font-semibold">{{ __('Drag to reorder') }}</span>
            </div>
            <button
                type="button"
                wire:click.stop="toggleCardVisibility('{{ $cardKey }}')"
                class="no-drag rounded p-1.5 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                title="{{ __('Toggle Visibility') }}"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
    @endif

    @if($variant === 'minimal')
        <div>
            <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $card['title'] }}</p>
            <p class="mt-1 text-2xl font-semibold {!! $isGrowthMetric ? 'tracking-tight' : 'tracking-normal' !!} {{ $valueClass }}">
                {!! $formattedValue !!}
            </p>
            @if($subtitle)
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">{{ $subtitle }}</p>
            @endif
        </div>
    @else
        <div class="flex items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $card['title'] }}</p>
                <p class="text-3xl font-semibold {{ $valueClass }}">
                    {!! $formattedValue !!}
                </p>
                @if($subtitle)
                    <p class="text-xs text-zinc-500 dark:text-zinc-500">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="rounded-lg {{ $color['bg'] }} p-3">
                <svg class="h-6 w-6 {{ $color['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $iconPath !!}
                </svg>
            </div>
        </div>
    @endif
</div>

