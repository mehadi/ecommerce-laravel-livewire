@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-600/10 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-500/20']) }}>
        {{ $status }}
    </div>
@endif
