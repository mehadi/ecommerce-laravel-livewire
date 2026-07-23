@props(['title', 'description' => null, 'icon' => null, 'badge' => null])

@php
    // Back-compat: an SVG path d-string always contains spaces; a heroicon
    // name (the preferred form, rendered via flux:icon) never does.
    $iconIsPath = $icon && str_contains($icon, ' ');
@endphp

<section class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm transition-colors dark:border-zinc-700 dark:bg-zinc-900 sm:p-7">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3.5">
            @if($icon)
                <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400" aria-hidden="true">
                    @if($iconIsPath)
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"></path>
                        </svg>
                    @else
                        <flux:icon :name="$icon" class="size-4.5" />
                    @endif
                </div>
            @endif

            <div class="space-y-1">
                <flux:heading size="sm">{{ $title }}</flux:heading>
                @if($description)
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
                @endif
            </div>
        </div>

        @if($badge)
            <div class="shrink-0">{{ $badge }}</div>
        @endif
    </div>

    <div class="space-y-6">
        {{ $slot }}
    </div>
</section>
