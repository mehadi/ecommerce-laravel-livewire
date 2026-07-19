{{--
    Shared shell for the dashboard "Customize" panel: header (title + reset
    button) and the how-to instructions callout. Used identically across all
    5 dashboard pages — only the section checklists inside differ, so those
    are passed in via the default slot (see x-dashboard.customize-section).
    Binds to the host Livewire component's resetDashboardPreferences() method.
--}}
<div class="relative overflow-hidden rounded-3xl border border-zinc-200/80 bg-white shadow-xl ring-1 ring-black/5 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-white/10">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/60 via-indigo-50/30 to-purple-50/20 dark:from-blue-950/20 dark:via-indigo-950/10 dark:to-purple-950/10"></div>

    <!-- Content -->
    <div class="relative p-6">
        <!-- Header -->
        <div class="mb-6 flex flex-col items-start justify-between gap-4 border-b border-zinc-200/80 pb-5 dark:border-zinc-700/80 md:flex-row md:items-center">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg ring-1 ring-blue-600/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ __('Customize Dashboard') }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Select visible cards and drag to rearrange. Changes save automatically.') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button wire:click="resetDashboardPreferences" size="sm" variant="outline" class="whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 10-16 0m16 8a8 8 0 10-16 0" />
                    </svg>
                    {{ __('Reset to defaults') }}
                </flux:button>
            </div>
        </div>

        <!-- Instructions -->
        <flux:callout class="mb-6" icon="information-circle" variant="info">
            <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('How to customize your dashboard:') }}</p>
            <ul class="mt-2 space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                <li class="flex items-start gap-2">
                    <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                    <span>{{ __('Drag cards up or down to reorder them') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                    <span>{{ __('Toggle checkboxes to show or hide cards') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                    <span>{{ __('Changes are saved automatically') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                    <span>{{ __('Use "Reset to defaults" to restore the original layout') }}</span>
                </li>
            </ul>
        </flux:callout>

        {{ $slot }}
    </div>
</div>
