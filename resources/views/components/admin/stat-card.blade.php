@props(['label', 'value', 'tone' => 'zinc'])

@php
    [$iconBg, $iconText] = match ($tone) {
        'blue' => ['bg-blue-100 dark:bg-blue-900/20', 'text-blue-600 dark:text-blue-400'],
        'emerald' => ['bg-emerald-100 dark:bg-emerald-900/20', 'text-emerald-600 dark:text-emerald-400'],
        'purple' => ['bg-purple-100 dark:bg-purple-900/20', 'text-purple-600 dark:text-purple-400'],
        'amber' => ['bg-amber-100 dark:bg-amber-900/20', 'text-amber-600 dark:text-amber-400'],
        'red' => ['bg-red-100 dark:bg-red-900/20', 'text-red-600 dark:text-red-400'],
        'indigo' => ['bg-indigo-100 dark:bg-indigo-900/20', 'text-indigo-600 dark:text-indigo-400'],
        'rose' => ['bg-rose-100 dark:bg-rose-900/20', 'text-rose-600 dark:text-rose-400'],
        'violet' => ['bg-violet-100 dark:bg-violet-900/20', 'text-violet-600 dark:text-violet-400'],
        default => ['bg-zinc-100 dark:bg-zinc-800', 'text-zinc-500 dark:text-zinc-400'],
    };

    $valueColor = match ($tone) {
        'blue' => 'text-blue-600 dark:text-blue-400',
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
        'purple' => 'text-purple-600 dark:text-purple-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'red' => 'text-red-600 dark:text-red-400',
        'indigo' => 'text-indigo-600 dark:text-indigo-400',
        'rose' => 'text-rose-600 dark:text-rose-400',
        'violet' => 'text-violet-600 dark:text-violet-400',
        default => 'text-zinc-900 dark:text-white',
    };
@endphp

<div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold {{ $valueColor }}">{{ $value }}</p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $iconBg }}">
            <span class="h-6 w-6 {{ $iconText }}">
                @isset($icon){{ $icon }}@endisset
            </span>
        </div>
    </div>
</div>
