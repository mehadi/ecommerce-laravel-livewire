@props([
    'title' => __('Advanced Analytics'),
    'description' => null,
])

<div class="rounded-2xl border border-dashed border-indigo-300/70 bg-indigo-50/40 p-10 text-center dark:border-indigo-800/50 dark:bg-indigo-900/10">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-indigo-500 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
        </svg>
    </div>
    <p class="mt-4 text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</p>
    <p class="mx-auto mt-2 max-w-md text-sm text-zinc-500 dark:text-zinc-400">
        {{ $description ?? __('This report is part of Advanced Analytics, available on higher-tier plans.') }}
    </p>
    <flux:button :href="route('admin.billing.index')" wire:navigate variant="primary" size="sm" class="mt-5">
        {{ __('View Upgrade Options') }}
    </flux:button>
</div>
