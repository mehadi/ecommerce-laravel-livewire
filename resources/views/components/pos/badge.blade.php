{{-- Status badges derived entirely from existing Product fields — no new schema. --}}
@props(['type'])

@php
    [$classes, $label] = match ($type) {
        'low-stock' => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', __('Low stock')],
        'out-of-stock' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', __('Out of stock')],
        'promotion' => ['bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400', __('Sale')],
        'new' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', __('New')],
        default => ['bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', $type],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[10px] font-semibold leading-none $classes"]) }}>
    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
    {{ $label }}
</span>
