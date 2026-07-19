@props([
    // Raw <path> markup for the section's icon (kept as inline SVG paths to
    // match the rest of the dashboard's hand-rolled icons rather than pulling
    // in a Flux icon name for each).
    'iconPath',
    'color', // emerald | cyan | indigo — matches the accent used elsewhere for this card type
    'title',
    'items', // assoc array: card key => card definition (needs at least 'title')
    'type', // 'metric' | 'chart' | 'insight' — passed to toggleCardVisibility's card_type lookup
    'userPreferences', // Collection of card preference models (card_key, card_type, is_visible)
])

@php
    $colorClasses = [
        'emerald' => ['chip' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400', 'active' => 'border-emerald-300 bg-emerald-50/80 ring-1 ring-emerald-200/50 dark:border-emerald-700 dark:bg-emerald-900/20 dark:ring-emerald-800/30'],
        'cyan' => ['chip' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400', 'active' => 'border-cyan-300 bg-cyan-50/80 ring-1 ring-cyan-200/50 dark:border-cyan-700 dark:bg-cyan-900/20 dark:ring-cyan-800/30'],
        'indigo' => ['chip' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400', 'active' => 'border-indigo-300 bg-indigo-50/80 ring-1 ring-indigo-200/50 dark:border-indigo-700 dark:bg-indigo-900/20 dark:ring-indigo-800/30'],
    ];

    $palette = $colorClasses[$color] ?? $colorClasses['emerald'];
@endphp

<div>
    <div class="mb-4 flex items-center gap-3">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $palette['chip'] }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $iconPath !!}
            </svg>
        </div>
        <h4 class="text-base font-bold text-zinc-900 dark:text-white">{{ $title }}</h4>
        <div class="flex-1 border-t border-zinc-200 dark:border-zinc-700"></div>
        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ count($items) }} {{ __('available') }}</span>
    </div>

    <flux:checkbox.group class="flex flex-wrap gap-3">
        @foreach($items as $key => $item)
            @php
                $pref = $userPreferences->firstWhere(fn ($p) => $p->card_key === $key && $p->card_type === $type);
                $isVisible = $pref?->is_visible ?? true;
            @endphp
            <div class="group relative flex min-w-0 flex-1 basis-48 rounded-xl border bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-zinc-900 {{ $isVisible ? $palette['active'] : 'border-zinc-200 dark:border-zinc-700' }}">
                <flux:checkbox
                    wire:click.stop="toggleCardVisibility('{{ $key }}')"
                    :checked="$isVisible"
                    label="{{ $item['title'] }}"
                />
            </div>
        @endforeach
    </flux:checkbox.group>
</div>
