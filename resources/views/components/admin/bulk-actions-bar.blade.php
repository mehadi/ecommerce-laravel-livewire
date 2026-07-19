@props(['count'])

<div class="flex items-center justify-between gap-4 rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
    <div class="flex items-center gap-2">
        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
            {{ __(':count item(s) selected', ['count' => $count]) }}
        </span>
    </div>
    <div class="flex gap-2">
        {{ $slot }}
    </div>
</div>
